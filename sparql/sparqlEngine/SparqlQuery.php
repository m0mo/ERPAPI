<?php

/**
 * --------------------------------------------------------------------
 * ERP API 
 * --------------------------------------------------------------------
 *
 * Sparql Query Object
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * @name        SparqlQuery.php
 * @version     2011-08-31
 * @package     sparql/sparqlEngine
 * @access      public
 * 
 * --------------------------------------------------------------------
 */
class SparqlQuery {

    /**
     * The query as string
     *
     * @var string
     */
    private $query;

    /**
     * Contains used namespaces, provided in the sparql query. Prefix is used as
     * the key.
     *
     * @var array 
     */
    private $namespaces;

    /**
     * Contains all variables that are selected. Form is i.e. ?x
     *
     * @var array 
     */
    private $resultVariables;

    /**
     * Contains all tripples that are in the where clausel. If the variable is a
     * placeholde (i.e. ?x) then its saved as a string, otherwise the variables
     * are transformed as Nodes. The subject URI or placeholder is the key
     *
     * @var array 
     */
    private $whereTripples;

    /**
     * The form of the query.
     * 
     * SPARQL has four query forms. These query forms use the solutions from 
     * pattern matching to form result sets or RDF graphs. The query forms are:
     * - SELECT
     * - CONSTRUCT
     * - ASK
     * - DESCRIBE
     *
     * @var string 
     * @see http://www.w3.org/TR/rdf-sparql-query/#QueryForms
     */
    private $form;

    /**
     * Constructor
     */
    function __construct() {
        
    }

    /**
     * Add a namespace to the Query object
     *
     * @param string $prefix
     * @param string $namespace 
     * @throws SparqlException
     */
    public function addNamespace($prefix, $namespace) {

        if (!Check::isNamespace($namespace))
            throw new SparqlException(SPARQL_ERROR . "Parameter not a valid namespace!");

        if (!Check::isPrefix($prefix))
            throw new SparqlException(SPARQL_ERROR . "Parameter not a valid prefix!");

        $this->namespaces[$prefix] = $namespace;
    }

    /**
     * Adds a result variable. These variables are used for selecting the final
     * return values. Form is i.e. ?x
     *
     * @param string $var 
     * @throws SparqlException
     */
    public function addResultVariable($var) {

        if (preg_match("/^\?([a-z0-9]+)$/i", $var) == 0)
            throw new SparqlException(SPARQL_ERROR . "Parameter needs to start with a '?' followed by characters or numbers.");

        $this->resultVariables[$var] = $var;
    }

    /**
     * Add a WHERE tripple to the Query object. Parameters can be Resources, 
     * Blank Nodes, Literal Nodes or placeholders (i.e. ?x)
     *
     * @param string $subj
     * @param string $pred
     * @param string $obj 
     * @throws SparqlException
     */
    public function addWhereTriple($subj, $pred, $obj) {

        $s = $this->transformToNode($subj);
        $p = $this->transformToNode($pred);
        $o = $this->transformToNode($obj);

        $this->whereTripples[$subj][] = array("subject" => $s, "predicate" => $p, "object" => $o);
    }

    /**
     * Returns the query as a string
     *
     * @return string 
     */
    public function getQueryString() {
        return $this->query;
    }

    /**
     * Returns the list of namespaces
     *
     * @return array 
     */
    public function getNamespaces() {
        return $this->namespaces;
    }

    /**
     * Returns the list of the requested variables
     *
     * @return array 
     */
    public function getResultVariables() {
        return $this->resultVariables;
    }

    /**
     * Returns the list of tripples of the WHERE block.
     *
     * @return array 
     */
    public function getWhereTriples() {
        return $this->whereTripples;
    }

    /**
     * Setter for the query string
     *
     * @param string $query 
     */
    public function setQueryString($query) {
        $this->query = $query;
    }

    /**
     * Returns the result format
     *
     * @return string One of: "SELECT", "CONSTRUCT", "ASK", "DESCRIBE" 
     */
    public function getResultForm() {
        return $this->form;
    }

    /**
     * Setter for the Result form. 
     * One of: "SELECT", "CONSTRUCT", "ASK", "DESCRIBE"
     *
     * @param string $form 
     */
    public function setResultForm($form) {

        switch (strtoupper($form)) {
            case SELECT:
                $this->form = SELECT;
                break;

            case CONSTRUCT:
                $this->form = CONSTRUCT;
                break;

            case ASK:
                $this->form = ASK;
                break;

            case DESCRIBE:
                $this->form = DESCRIBE;
                break;

            default:
                throw new SparqlException(SPQRQL_QUERY_RESULT_FORMAT);
        }
    }

    /**
     * If the parameter can be transformed in to a node it will do so. Otherwise 
     * it will just return the input string
     *
     * @param string $string 
     * @return Node|string
     * @throws SparqlException
     */
    public function transformToNode($string) {

        if (preg_match("/^\?([a-z0-9]+)$/i", $string))
            return $string;

        // besides placeholders (i.e. ?x) three are types of nodes:
        // 1) Resource -> indicated by a namespace or prefix
        // 2) Blank Node -> indicated by prefix _:
        // 3) Literal Node -> indicated by ""
        // Resource URI
        if (Check::isUri($string))
            return new Resource($string);

        // Resource prefix:name
        if (Check::isPrefixAndName($string)) {
            list($prefix, $name) = explode(":", $string);

            if (!isset($this->namespaces[$prefix]))
                throw new SparqlException(SPARQL_ERROR . "Node could not be identified. Namespace is missing!");

            return new Resource($this->namespaces[$prefix] . $name);
        }

        //Blank Node
        if (preg_match("/^(_:)(?P<id>[a-z0-9]+)/i", $string, $matches))
            return new BlankNode($matches["id"]);


        //Literal Node
        if (preg_match("/^\"(?P<literal>.+)\"((\^\^xsd:(?P<datatype>[a-z]+))|(@(?P<language>[a-z]+))){0,2}/i", $string, $matches))
            return new LiteralNode($matches["literal"], $matches["datatype"], $matches["language"]);


        throw new SparqlException(SPARQL_ERROR . "Could not interpret node: " . $string . "!");
    }

}

?>
