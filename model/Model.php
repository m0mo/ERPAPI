<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        Model.php
 * @version     0.2.0 (Aug 6, 2011)
 * @package     model
 * @access      public
 * 
 * Description  This class represents the whole RDF model in form of a list 
 *              of statements.
 * 
 * -----------------------------------------------------------------------------
 */
class Model {

    private $statements;
    private $namespaces;

    /**
     * Create a new model
     */
    function __construct() {
        
    }

    /**
     * Add a namespace to the model
     * 
     * @param String $prefix
     * @param String $uri 
     */
    public function addNamespace($prefix, $uri) {
        
        // TODO: Check input
        
        $this->namespaces[$prefix] = $uri;
    }

    /**
     * Get the URI of the namespace
     *
     * @param String $prefix
     * @return String $uri 
     */
    public function getNamespace($prefix) {
        
        // TODO: Check input
        
        return $this->namespaces[$prefix];
    }
    
    /**
     * Get a list of namespaces inform of namespaces[$prefix] = $uri
     *
     * @param String $prefix
     * @return String $uri 
     */
    public function getNamespaces() {
        
        // TODO: Check input
        
        return $this->namespaces;
    }

    /**
     * Remove a namespace from the model
     *
     * @param String $prefix
     * @return true if removed 
     */
    public function removeNamespace($prefix) {
        
        // TODO: Check input

        unset($this->namespaces[$prefix]);
        return !$this->hasNamespace($prefix);
    }

    /**
     * Checks if the model contains a specific namespace
     *
     * @param String $prefix
     * @return true if yes, otherwise false 
     */
    public function hasNamespace($prefix) {
        
        // TODO: Check input
        
        return isset($this->namespaces[$prefix]);
    }

    /**
     * Add a statement or resource to the model
     *
     * @param Statement of Resource $statement_or_resource 
     */
    public function add($statement_or_resource) {


        if (Check::isStatement($statement_or_resource)) {

            $this->addStatement($statement_or_resource);
        } else if (Check::isSubject($statement_or_resource)) {

            $this->addResource($statement_or_resource);
        } else {

            // if nothing above fits, throw new exception
            throw new APIException(API_ERROR . "tryed to add non-statement or non-resource to the model.");
        }
    }

    /**
     * Add a statement to the list of statements if it does not exist already
     *
     * @param Statement $statement 
     * @return true if successfull,otherwise false
     */
    private function addStatement($statement) {

        if (!Check::isStatement($statement)) {
            throw new APIException(API_ERROR_STATEMENT);
        }

        if (!$this->contains($statement)) {

            $this->statements[] = $statement;
        }
    }

    /**
     * This function transforms a resource and its properties to a list of statements
     * and adds them to the list if they don't exist already.
     *
     * @param Resource $resource 
     * @return true if successfull,otherwise false
     */
    private function addResource($resource) {

        if (!Check::isSubject($resource)) {
            throw new APIException(ERP_ERROR_SUBJECT);
        }

        $properties = $resource->getProperties();

        // if there are no properties we can't add any statements
        if (empty($properties)) {
            throw new APIException(API_ERROR . "A resource needs to contain at least one property to be added to the model.");
        }

        foreach ($properties as $prop) {

            $predicate = $prop["predicate"];
            $object = $prop["object"];

            $this->addStatement(new Statement($resource, $predicate, $object));
            
            if(Check::isSubject($object))
                $this->addResource ($object);
        }
    }

    /**
     * Checks if the statement is already present in the model
     *
     * @param Statement $statement 
     * @return true if model contains the statement, otherwise false
     */
    public function contains($statement) {

        if (!Check::isStatement($statement)) {
            throw new APIException(API_ERROR_STATEMENT);
        }

        $results = $this->search($statement->getSubject(), $statement->getPredicate(), $statement->getObject());

        return!empty($results);
    }

    /**
     * Returns a list of statements which fit to the input
     *
     * @param Node $subject
     * @param Node $predicate
     * @param Node $object
     * @return Statement Array 
     */
    public function search($subject = null, $predicate = null, $object = null) {
        
        if (!Check::isSubject($subject) && !empty($subject)) {
            throw new APIException(ERP_ERROR_SUBJECT);
        }

        if (!Check::isPredicate($predicate) && !empty($predicate)) {
            throw new APIException(ERP_ERROR_PREDICATE);
        }

        if (!Check::isObject($object) && !empty($object)) {
            throw new APIException(ERP_ERROR_OBJECT);
        }

        if (empty($this->statements))
            return null;

        foreach ($this->statements as $statement) {
            
            $subFound = false;
            $preFound = false;
            $objFound = false;

            if (!empty($subject)) {

                $subFound = $subject->equals($statement->getSubject());
            } else {
                $subFound = true;
            }

            if (!empty($predicate)) {

                $preFound = $predicate->equals($statement->getPredicate());
            } else {
                $preFound = true;
            }

            if (!empty($object)) {

                $objFound = $object->equals($statement->getObject());
            } else {
                $objFound = true;
            }

            if ($subFound && $preFound && $objFound) {
                $foundStatements[] = $statement;
            }
        }

        return $foundStatements;
    }

    /**
     * Returns a list of all statements in the model using following format:
     * subjectURI, predicateURI, objectUri or Literal
     *
     * @return String
     */
    public function modelToString() {
        return $this->statementListToString($this->statements);
    }

    /**
     * Returns a list of provided statements using following format:
     * subjectURI, predicateURI, objectUri or Literal
     *
     * @param Statement Array $statements
     * @return String
     */
    public function statementListToString($statements) {
        
        $returnString = "";
        
        foreach ($statements as $statement) {
            
            $returnString.= $statement->getSubject()->getUri(). " ";
            $returnString.= $statement->getPredicate()->getUri(). " ";
            $returnString.= (is_a($statement->getObject(), LiteralNode)) ? $statement->getObject()->getLiteral() : $statement->getObject()->getUri();
            $returnString.= " \n";
        }
        
        return $returnString;
        
    }

    public function modelToHTMLTable() {
        return $this->statementListToHTMLTable($this->statements);
    }

    public function statementListToHTMLTable($statements) {
        //TODO: Implement statementListToHTMLTable
    }

    /**
     * Returns a new object of type Resource if $uri is set, otherwise it will 
     * return a BlankNode
     *
     * @param String $uri
     * @return Resource 
     */
    public function newResource($uri = null) {
        if (!empty($uri))
            return new Resource($uri);
       
        return $this->newBlankNode();
    }

    /**
     * Returns a new object of type LiteralNode
     *
     * @param type $literal
     * @param type $datatype
     * @return Literal 
     */
    public function newLiteralNode($literal, $datatype = STRING) {
        return new LiteralNode($literal, $datatype);
    }

    /**
     * Returns a new object of type BlankNode
     *
     * @return BlankNode 
     */
    public function newBlankNode() {
        return new BlankNode();
    }

    /**
     * Returns a new object of type Statement
     *
     * @param Node $subject
     * @param Node $predicate
     * @param Node $object
     * @return Statement 
     */
    public function newStatement($subject, $predicate, $object) {
        return new Statement($subject, $predicate, $object);
    }

}

?>
