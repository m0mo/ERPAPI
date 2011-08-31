<?php

require_once 'Constants.php';
require_once 'SparqlParser.php';
require_once 'SparqlQuery.php';

/**
 * -----------------------------------------------------------------------------
 * ERP API
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com>
 *
 * @name        XPathSparqlEngine.php
 * @version     2011-08-31
 * @package     XPathEngine
 * @access      public
 *
 * Description  The Sparql XPath Engine
 *
 * -----------------------------------------------------------------------------
 */
class SparqlEngine implements ISparqlEngine {

    /**
     * The query string
     *
     * @var string
     */
    private $queryString;

    /**
     * The query object
     *
     * @var SparqlQuery
     */
    private $query;

    /**
     * The model to query
     *
     * @var Model
     */
    private $model;

    /**
     * List of processed variables and their resuts. Key is the subject uri or
     * the variable.
     *
     * @var array
     */
    private $processedVariables;

    /**
     * List of variabled that are not finnished processing. Used to prevent
     * dead locks.
     *
     * @var array
     */
    private $processingVariables;

    /**
     * Table of results
     *
     * @var array
     */
    private $resultTable;

    /**
     * Pattern used for creating an order of queries
     *
     * @var array
     */
    private $pattern;

    /**
     * Constructor
     */
    function __construct() {
        
    }

    /**
     * Performs a query against a model
     *
     * @param string $queryString
     * @param Model $model
     * @return mixed 
     */
    public function query($queryString, $model) {

        if (!Check::isModel($model))
            throw new SparqlException(SPARQL_ERROR . "Parameter is not a Model.");

        if (!Check::isString($queryString))
            throw new SparqlException(SPARQL_ERROR . "Parameter is not a string.");

        $this->init();

        // set first variables
        $this->queryString = $queryString;
        $this->model = $model;

        // parse the query to an object
        $parser = new SparqlParser();
        $this->query = $parser->parse($queryString);
        $parser = null;

        switch ($this->query->getResultForm()) {

            case SELECT:
                return $this->querySelect($queryString, $model);
                break;

            case ASK:
                throw new SparqlException(SPQRQL_QUERY_RESULT_SUPPORT);

            case DESCRIBE:
                throw new SparqlException(SPQRQL_QUERY_RESULT_SUPPORT);
                
            case CONSTRUCT:
                throw new SparqlException(SPQRQL_QUERY_RESULT_SUPPORT);

            default:
                throw new SparqlException(SPQRQL_ERROR_QUERY_RESULT_FORMAT);
        }
    }

    /**
     * Performs a select query against a model
     *
     * @param string $queryString
     * @param Model $model
     * @return array
     */
    private function querySelect($queryString, $model) {

        // build the pattern for the sparql query
        $pattern = $this->buildPattern();
        // perform inner filters
        $result = $this->applyPattern($pattern);
        // filter result
        $result = $this->filterBySelect($result);

        return $result;
    }

    /**
     * Resets the engine;
     */
    private function init() {
        $this->queryString = null;
        $this->model = null;
        $this->processedVariables = null;
        $this->processingVariables = null;
        $this->query = null;
        $this->pattern = null;
    }

    /**
     * Builds a pattern (tree) of relations of the where triple variables of the
     * query.
     *
     * @return array
     */
    private function buildPattern() {

        //initiate
        $this->processedVariables = null;
        $this->processingVariables = null;

        // retrieve tripples
        $whereTriples = $this->query->getWhereTriples();

//        print_r($whereTriples);

        foreach ($whereTriples as $key => $triples) {

            // starting with the first variable. If the variable was already
            // processed it will not be checked again
            if (!isset($this->processedVariables[$key]))
                $this->pattern[$key] = $this->buildRecursiveTreePattern($key);
        }

        // freeing some memory
        $this->processedVariables = null;
        $this->processingVariables = null;

        return $this->pattern;
    }

    /**
     * Recursivley checks for further variables
     *
     * @param string $var
     * @return array
     */
    private function buildRecursiveTreePattern($var) {

        // Variable was already processed and will be returned instead of
        // processing it again.
        if ($this->isVariable($var) && isset($this->processedVariables[$var]))
            return $this->processedVariables[$var];


        // Variable is still processed (apperently a circle in the query)
        if ($this->isVariable($var) && isset($this->processingVariables[$var]))
            return null;

        // Get tripples and extract tripples that schould be processed
        $processTriples = array();
        $whereTriples = $this->query->getWhereTriples();

        if (isset($whereTriples[$var]))
            $processTriples = $whereTriples[$var];

        // if there are no tripples found then there are no further levels to go
        // and we return null;
        if (empty($processTriples)) {
            $this->processedVariables[$var] = $var;
            return null;
        }

        // reset return tripples
        $returnTriples = array();

        // set that the variable is in process
        $this->processingVariables[$var] = true;

        // process the variable
        foreach ($processTriples as $key => $triple) {

            $object = null;

            // if in the tripple the object is another variable then we will call
            // this functon recursivly.
            if ($this->isVariable($triple["object"])) {

                // recursive call
                $object = $this->buildRecursiveTreePattern($triple["object"]);
            }

            // if the object is set replace the original object with the set
            // of triples that are returned. Otherwise we do nothing
            if (!empty($object))
                $triple["object"] = $object;

            // we save the tripple in an array that will be returned
            $returnTriples["variable"] = $var;
            $returnTriples[$key] = $triple;
        }

        // remove the variable from the list of processing variables since no
        // furhter processing is neccessary
        unset($this->processingVariables[$var]);

        // save the result also in the processed Variable array so if further
        // calls require the same variable we dont have to process everything again
        $this->processedVariables[$var] = $returnTriples;

        // return the tripple with the result
        return $returnTriples;
    }

    /**
     * Applyes the pattern to the model to filter the results
     *
     * @param array $pattern
     * @return array
     */
    private function applyPattern($pattern) {

        // preperation
        $this->resultTable = array();

        foreach ($pattern as $branch) {
            $this->applyPatternRecusively($branch);
        }

        return $this->resultTable;
    }

    /**
     * Helper for applyPattern($pattern)
     *
     * @param array $branch
     * @return bool 
     */
    private function applyPatternRecusively($branch) {


        $var = $branch["variable"];
        unset($branch["variable"]);

        foreach ($branch as $triple) {

            // look for variables
            //subject
            $subject = (!$this->isVariable($triple["subject"])) ? $triple["subject"] : null;
            $subjectVar = (!$this->isVariable($triple["subject"])) ? null : $triple["subject"];

            //predicate
            $predicate = (!$this->isVariable($triple["predicate"])) ? $triple["predicate"] : null;
            $predicateVar = (!$this->isVariable($triple["predicate"])) ? null : $triple["predicate"];

            //object
            $object = null;
            $objectVar = null;

            if (is_array($triple["object"])) {

                // if the object is a array it is basically a variable that has
                // its own query, therefore we set it as a variable and call this
                // function recursively
                $objectVar = $triple["object"]["variable"];
                $b = $this->applyPatternRecusively($triple["object"]);

                // if the recursive call did not find any fitting objects the
                // whole branch will not lead to any results. So we can cancel
                // further searching
                if (!$b)
                    return false;
            } else if (!$this->isVariable($triple["object"])) {

                // if the object is not a variable it has to be a node and we
                // will set it
                $object = $triple["object"];
            } else {

                // if nothing fits, the object is a variable and we set it null
                $objectVar = (!$this->isVariable($triple["object"])) ? null : $triple["object"];
            }

            /*
             * For requireing statements there need to be considered two
             * possibilities:
             * 1) None of the triples variables are already checked
             * 2) Some of the triples variables are already checked
             *
             */

            // possibility (1)
            if (!$this->isSetVar($subjectVar) && !$this->isSetVar($predicateVar) && !$this->isSetVar($objectVar)) {

                $res = $this->model->search($subject, $predicate, $object);

                // if no statements are found the whole branch will not lead to any
                // results
                if (empty($res))
                    return false;

                // preparing the table;
                if ($this->isVariable($subjectVar))
                    $this->resultTable[$subjectVar] = array();

                if ($this->isVariable($predicateVar))
                    $this->resultTable[$predicateVar] = array();

                if ($this->isVariable($objectVar))
                    $this->resultTable[$objectVar] = array();

                // fill the table;
                foreach ($res as $statement) {

                    if ($this->isVariable($subjectVar))
                        $this->resultTable[$subjectVar][] = $statement->getSubject();

                    if ($this->isVariable($predicateVar))
                        $this->resultTable[$predicateVar][] = $statement->getPredicate();

                    if ($this->isVariable($objectVar))
                        $this->resultTable[$objectVar][] = $statement->getObject();
                }
            } else {

                //possibility 2

                /*
                 * Here it is possible to have all combinations of the different
                 * variables already saved in the table:
                 * 1)   a) ?x _o _o
                 *      b) ?x _v _o
                 *      c) ?x _o _v
                 *      d) ?x _v _v
                 *
                 * 2)   a) ?x ?y _o
                 *      b) ?x ?y _v
                 *
                 * 3)   a) ?x _o ?z
                 *      b) ?x _v ?z
                 *
                 * 4)   .) ?x ?y ?z
                 *
                 * 5)   a) _o ?y _o
                 *      b) _o ?y _v
                 *      c) _v ?y _o
                 *      d) _v ?y _v
                 *
                 * 6)   a) _o ?y ?z
                 *      b) _v ?y ?z
                 *
                 * 7    a) _o _o ?z
                 *      b) _o _v ?z
                 *      c) _v _o ?z
                 *      d) _v _v ?z
                 *
                 * Description:
                 *      ?x represent variables already in the table
                 *      _o represent an object
                 *      _v represents a variable that is not in the table
                 *
                 * All triples that dont contain _v are filtered and nothing is
                 * added, where as all triples with at least one _v can be:
                 *      1) removed (if no results)
                 *      2) variable added to the row (if one result)
                 *      3) row dublicated for each result (if more than one result)
                 */

                $deleteRows = array();

                // subject possibilities 1 2 3 4
                if ($this->isSetVar($subjectVar)) {

                    foreach ($this->resultTable[$subjectVar] as $row => $s) {

                        if ($this->isSetVar($predicateVar)) {

                            $p = $this->resultTable[$predicateVar][$row];

                            if ($this->isSetVar($objectVar)) {

                                // possibility(4)
                                // all variables already in the array so
                                // we dont have to search more
                            } else {

                                // possibility (2.a, 2.b)

                                $res = $this->model->search($s, $p, $object);

                                if (!$this->processRow($row, $res, $subjectVar, $predicateVar, $objectVar))
                                    $deleteRows[] = $row;
                            }
                        } else {

                            if ($this->isSetVar($objectVar)) {

                                $o = $this->resultTable[$objectVar][$row];
                                $res = $this->model->search($s, $predicate, $o);

                                // possibility (3.a, 3.b)
                                if (!$this->processRow($row, $res, $subjectVar, $predicateVar, $objectVar))
                                    $deleteRows[] = $row;
                            } else {

                                $res = $this->model->search($s, $predicate, $object);

                                // possibility (1.a, 1.b, 1.c, 1.d)
                                if (!$this->processRow($row, $res, $subjectVar, $predicateVar, $objectVar))
                                    $deleteRows[] = $row;
                            }
                        }
                    }
                }

                // predicate possibilities 5 & 6

                if (!$this->isSetVar($subjectVar) && $this->isSetVar($predicateVar)) {

                    foreach ($this->resultTable[$predicateVar] as $row => $p) {

                        if ($this->isSetVar($objectVar)) {

                            $o = $this->resultTable[$objectVar][$row];
                            $res = $this->model->search($subject, $p, $o);

                            // possibility (1.a, 1.b, 1.c, 1.d)
                            if (!$this->processRow($row, $res, $subjectVar, $predicateVar, $objectVar))
                                $deleteRows[] = $row;
                        } else {

                            $res = $this->model->search($subject, $p, $object);

                            // possibilities (5.a, 5.b, 5.c, 5.d)
                            if (!$this->processRow($row, $res, $subjectVar, $predicateVar, $objectVar))
                                $deleteRows[] = $row;
                        }
                    }
                }

                // object possibilitie 7

                if (!$this->isSetVar($subjectVar) && !$this->isSetVar($predicateVar) && $this->isSetVar($objectVar)) {

                    foreach ($this->resultTable[$objectVar] as $row => $o) {

                        $res = $this->model->search($subject, $predicate, $o);

                        // possibility (7.a, 7.b, 7.c, 7.d)
                        if (!$this->processRow($row, $res, $subjectVar, $predicateVar, $objectVar))
                            $deleteRows[] = $row;
                    }
                }

                // delete rows that are not part of the result
                if (count($deleteRows) >= 1) {
                    foreach ($deleteRows as $row)
                        $this->removeRow($row);
                }
            }
        }

        return true;
    }

    /**
     * Filters the result array by the select variables
     *
     * @param array $unfilteredResult
     * @return array filtered
     */
    private function filterBySelect($unfilteredResult) {

        $resultVars = $this->query->getResultVariables();

        foreach ($unfilteredResult as $key => $variableArray) {

            if (!isset($resultVars[$key]))
                unset($unfilteredResult[$key]);
        }

        return $unfilteredResult;
    }

    /**
     * Checks if the parameter is a valid variable
     *
     * @param string $param
     * @return bool
     */
    private function isVariable($param) {

        if (!Check::isString($param))
            return false;

        return (preg_match("/^\?([a-z0-9]+)$/i", $param) == 1) ? true : false;
    }

    /**
     * Checks if a Variable is already in the result array
     * 
     * @param string $var
     * @return bool 
     */
    private function isSetVar($var) {

        if (!$this->isVariable($var))
            return false;

        return isset($this->resultTable[$var]);
    }

    /**
     * Removes a row from the result array
     *
     * @param int $row 
     */
    private function removeRow($row) {

        foreach ($this->resultTable as $key => $variableArray) {
            unset($variableArray[$row]);
            $this->resultTable[$key] = $variableArray;
        }
    }

    /**
     * Processes a row if it was already in the result array
     *
     * @param integer $row row number in the array
     * @param array $res results that were found and have to be considered
     * @param string $sV variable name or null
     * @param string $pV variable name or null
     * @param string $oV variable name or null
     * @return bool 
     */
    private function processRow($row, $res, $sV, $pV, $oV) {

        if (!Check::isArray($res))
            return false;

        if (count($res) == 0)
            return false;

        // check wich variables are already set
        $isSetS = $this->isSetVar($sV);
        $isSetP = $this->isSetVar($pV);
        $isSetO = $this->isSetVar($oV);


        // check wich variables need to be set
        $toSetS = !$isSetS && $this->isVariable($sV);
        $toSetP = !$isSetP && $this->isVariable($pV);
        $toSetO = !$isSetO && $this->isVariable($oV);

        if (count($res) == 1) {

            if ($toSetS)
                $this->resultTable[$sV][$row] = $res[0]->getSubject();

            if ($toSetP)
                $this->resultTable[$pV][$row] = $res[0]->getPredicate();

            if ($toSetO)
                $this->resultTable[$oV][$row] = $res[0]->getObject();
        }

        if (count($res) > 1) {

            //TODO: Implement count($res) > 1
        }

        return true;
    }

}

?>
