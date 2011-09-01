<?php

/**
 * --------------------------------------------------------------------
 * ERP API 
 * --------------------------------------------------------------------
 *
 * Class for parsing the Sparql Query
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * @name        SparqlParser.php
 * @version     2011-09-01
 * @package     sparql/sparqlEngine
 * @access      public
 * 
 * --------------------------------------------------------------------
 */
class SparqlParser {

    /**
     * The Query Object
     *
     * @var SparqlQuery 
     */
    private $query;

    /**
     * The Querystring
     * @var string
     */
    private $queryString;

    /**
     * Constructor
     */
    public function __construct() {
        
    }

    /**
     * Parses the Query and returns an SparqlQuery Object
     *
     * @param string $queryString
     * @return SparqlQuery 
     */
    public function parse($queryString) {

        if (!Check::isString($queryString))
            throw new SparqlException();

        if (stripos($queryString, "FROM") !== false)
            throw new SparqlException("FROM is not supported");

        $this->init();

        $this->queryString = $queryString;
        $this->query->setQueryString($queryString);
        $this->query->setResultForm($this->getResultFormat());

        switch ($this->query->getResultForm()) {
            case SELECT:
                $this->handleSelect();
                break;

            case ASK:
                throw new SparqlException(SPQRQL_QUERY_RESULT_SUPPORT);

            case DESCRIBE:
                throw new SparqlException(SPQRQL_QUERY_RESULT_SUPPORT);

            case CONSTRUCT:
                throw new SparqlException(SPQRQL_QUERY_RESULT_SUPPORT);
        }

        // if PREFIX is in the query there is a namespace definition
        if (stripos($queryString, "PREFIX") !== false)
            $this->handleNamespaces();

        if (stripos($queryString, "WHERE") !== false)
            $this->handleWhere();

        return $this->query;
    }

    /**
     * Returns the query result format
     *
     * @return string 
     */
    public function getResultFormat() {

        if (stripos($this->queryString, SELECT) !== false)
            return SELECT;

        if (stripos($this->queryString, ASK) !== false)
            return ASK;

        if (stripos($this->queryString, CONSTRUCT) !== false)
            return CONSTRUCT;

        if (stripos($this->queryString, DESCRIBE) !== false)
            return DESCRIBE;

        throw new SparqlException(SPQRQL_QUERY_RESULT_FORMAT);
    }

    /**
     * Extracts namespaces off the query
     */
    private function handleNamespaces() {

        if (preg_match_all('/(?P<prefix>[a-z0-9]+): <(?P<namespace>[^ ]+)> /i', $this->queryString, $matches)) {

            $cnt = count($matches[0]);

            for ($i = 0; $i < $cnt; $i++) {
                $this->query->addNamespace($matches["prefix"][$i], $matches["namespace"][$i]);
            }
        } else {
            throw new SparqlException("Could not determine namespaces!");
        }
    }

    /**
     * Extracts the selected variables off the query
     */
    private function handleSelect() {

        if (preg_match_all('/(?P<var>\?[a-z0-9]+)+/i', substr($this->queryString, 0, stripos($this->queryString, "WHERE")), $matches)) {

            foreach ($matches["var"] as $var)
                $this->query->addResultVariable($var);
        } else {
            throw new SparqlException("Could not determine variables!");
        }
    }

//    /**
//     * Not implemented
//     */
//    public function handleConstruct() {
//        
//    }
//
//    /**
//     * Not implemented
//     */
//    public function handleAsk() {
//        
//    }
//
//    /**
//     * Not implemented
//     */
//    public function handleDescribe() {
//        
//    }

    /**
     * Extracts the where clausels off the query
     */
    private function handleWhere() {

        $tokenString = substr($this->queryString, stripos($this->queryString, "WHERE {") + 8);
        $tokenString = substr($tokenString, 0, strrpos($tokenString, "}"));
        $tokenString = trim($tokenString);

        if (strpos($tokenString, ".") === false)
            $tokens[] = $tokenString;
        else
            $tokens = explode(".", $tokenString);

        foreach ($tokens as $token) {

            list($subj, $pred, $obj) = explode(" ", trim($token));
            $this->query->addWhereTriple($subj, $pred, $obj);
        }
    }

    /**
     * Initiates the parser
     */
    private function init() {
        $this->query = new SparqlQuery();
        $this->queryString = null;
    }

}

?>
