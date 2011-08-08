<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        Model.php
 * @version     0.2.5 (Aug 8, 2011)
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
    private $foundResources;
    
    private $baseUri;
    private $basePrefix;

    /**
     * Create a new model
     */
    function __construct() {
        
    }
    
    /**
     * Adds a standart namespace to the model, which is used for node creation
     * by the model. So that nodes, that don't have a prefix or full Uri are
     * automatically added to the base namespace.
     *
     * @param type $prefix
     * @param type $uri 
     */
    public function addBaseNamespace($prefix, $uri) {
        
        if (!Check::isValidPrefix($prefix))
            throw new APIException(API_ERROR_URI);

        if (!Check::isValidURI($uri))
            throw new APIException(API_ERROR_URI);
        
        $this->baseUri = $uri;
        $this->basePrefix = $prefix;
        
        $this->addNamespace($prefix, $uri);
    }

    /**
     * Add a namespace to the model
     * 
     * @param String $prefix
     * @param String $uri 
     */
    public function addNamespace($prefix, $uri) {

        if (!Check::isValidPrefix($prefix))
            throw new APIException(API_ERROR_URI);

        if (!Check::isValidURI($uri))
            throw new APIException(API_ERROR_URI);

        $this->namespaces[$prefix] = $uri;
    }

    /**
     * Get the URI of the namespace
     *
     * @param String $prefix
     * @return String $uri 
     */
    public function getNamespace($prefix) {

        if (!Check::isValidPrefix($prefix))
            throw new APIException(API_ERROR_URI);

        return $this->namespaces[$prefix];
    }

    /**
     * Get a list of namespaces inform of namespaces[$prefix] = $uri
     *
     * @param String $prefix
     * @return String $uri 
     */
    public function getNamespaces() {

        return $this->namespaces;
    }

    /**
     * Remove a namespace from the model
     *
     * @param String $prefix
     * @return true if removed 
     */
    public function removeNamespace($prefix) {

        if (!Check::isValidPrefix($prefix))
            throw new APIException(API_ERROR_URI);

        unset($this->namespaces[$prefix]);
        return!$this->hasNamespace($prefix);
    }

    /**
     * Checks if the model contains a specific namespace
     *
     * @param String $prefix
     * @return true if yes, otherwise false 
     */
    public function hasNamespace($prefix) {

        if (!Check::isValidPrefix($prefix))
            throw new APIException(API_ERROR_URI);

        return isset($this->namespaces[$prefix]);
    }

    /**
     * Adds a statement or a resource (with properties) to the model. If $double
     * is true (standart) there will be no check performed if the statement is 
     * already part of the model and a copy will be saved. If $double is false
     * double entries are forbidden.
     *
     * @param Statement or Resource $statement_or_resource
     * @param bool $double
     * @return true if added, false if not
     */
    public function add($statement_or_resource, $double=true) {

        if (empty($statement_or_resource))
            throw new APIException(API_ERROR . "Parameter is null");

        if (Check::isStatement($statement_or_resource)) {
            return $this->addStatement($statement_or_resource, $double);
        } else if (Check::isSubject($statement_or_resource)) {
            return $this->addResource($statement_or_resource, $double);
        } else {
            // if nothing above fits, throw new exception
            throw new APIException(API_ERROR . "tryed to add non-statement or non-resource to the model.");
        }

        return false;
    }

    /**
     * Add a statement to the list of statements if it does not exist already
     *
     * @param Statement $statement 
     * @return true if successfull,otherwise false
     */
    private function addStatement($statement, $double) {

        if (!Check::isStatement($statement))
            throw new APIException(API_ERROR_STATEMENT);

        // Dublicates are forbidden
        if (!$double && !$this->contains($statement)) {
            $this->statements[] = $statement;
            return true;
        } else if (!$double) {
            return false;
        }

        // Dublicates are allowed
        $this->statements[] = $statement;
        return true;
    }

    /**
     * This function transforms a resource and its properties to a list of statements
     * and adds them to the list if they don't exist already.
     *
     * @param Resource $resource 
     * @return true if successfull,otherwise false
     */
    private function addResource($resource, $double) {
        
        if (!Check::isSubject($resource))
            throw new APIException(ERP_ERROR_SUBJECT);

        $properties = $resource->getProperties();

        // if there are no properties we can't add any statements
        if (empty($properties)) {
            throw new APIException(API_ERROR . "A resource needs to contain at least one property to be added to the model.");
        }

        foreach ($properties as $prop) {

            $predicate = $prop["predicate"];
            $object = $prop["object"];

            $this->addStatement(new Statement($resource, $predicate, $object), $double);

            if (Check::isSubject($object))
                $this->addResource($object, $double);
        }

        return true;
    }

    /**
     * Removes a statement or resource (and its properties) of the model
     *
     * @param Statement or Resource $statement_or_resource 
     */
    public function remove($statement_or_resource) {

        if (empty($statement_or_resource))
            throw new APIException(API_ERROR . "Parameter is null");

        if (Check::isStatement($statement_or_resource)) {
            return $this->removeStatement($statement_or_resource);
        } else if (Check::isSubject($statement_or_resource)) {
            return $this->removeResource($statement_or_resource);
        } else {
            // if nothing above fits, throw new exception
            throw new APIException(API_ERROR . "tryed to add non-statement or non-resource to the model.");
        }

        return false;
    }

    /**
     * Removes a statement to the list of statements
     *
     * @param Statement $statement
     */
    private function removeStatement($statement) {

        if (!Check::isStatement($statement))
            throw new APIException(API_ERROR_STATEMENT);

        foreach ($this->statements as $key => $value) {

            if ($statement->equals($value)) {
                unset($this->statements[$key]);
                return true;
            }
        }

        return false;
    }

    /**
     * This function removes a resource (with its properties) from the model
     *
     * @param Resource $resource 
     */
    private function removeResource($resource) {

        if (!Check::isSubject($resource))
            throw new APIException(ERP_ERROR_SUBJECT);

        $properties = $resource->getProperties();

        // if there are no properties we can't remove any statements
        if (empty($properties)) {
            throw new APIException(API_ERROR . "A resource needs to contain at least one property to be removed from the model.");
        }

        foreach ($properties as $prop) {

            $predicate = $prop["predicate"];
            $object = $prop["object"];

            return $this->removeStatement(new Statement($resource, $predicate, $object));
        }

        return false;
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

        if (empty($this->statements))
            return null;

        if (!Check::isSubject($subject) && !empty($subject))
            throw new APIException(ERP_ERROR_SUBJECT);

        if (!Check::isPredicate($predicate) && !empty($predicate))
            throw new APIException(ERP_ERROR_PREDICATE);

        if (!Check::isObject($object) && !empty($object))
            throw new APIException(ERP_ERROR_OBJECT);

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
     * Searchs for a resource and adds all found properties (recursive) to it. 
     * The resource that is returned will contain all properties and their
     * properties creating a tree of related nodes. 
     *
     * @param Resource $resource
     * @return Resource
     */
    public function searchResource($resource) {

        if (!Check::isSubject($resource))
            throw new APIException(ERP_ERROR_SUBJECT);

        // prevent dead lock
        if (isset($this->foundResources[$resource->getUri()])) {

            //return the element of the array rather than creating 
            //a new one

            return $this->foundResources[$resource->getUri()];
        }

        $statements = $this->search($resource);

        if (!empty($statements)) {

            foreach ($statements as $statement) {

                $this->foundResources[$resource->getUri()] = $resource;
                $object = $statement->getObject();

                if (Check::isSubject($object))
                    $object = $this->searchResource($object);

                $resource->addProperty($statement->getPredicate(), $object);
            }
        }

        unset($this->foundResources[$resource->getUri()]);

        return $resource;
    }

    /**
     * Returns the number of statements stored in the model
     *
     * @return Integer 
     */
    public function size() {
        return count($this->statements);
    }

    /**
     *
     * @return true if no statements are in the model, otherwise false 
     */
    public function isEmpty() {
        return empty($this->statements);
    }

    /**
     * Checks if the statement is already present in the model
     *
     * @param Statement $statement 
     * @return true if model contains the statement, otherwise false
     */
    public function contains($statement) {

        if (!Check::isStatement($statement))
            throw new APIException(API_ERROR_STATEMENT);

        if (empty($this->statements))
            return false;

        foreach ($this->statements as $value) {
            if ($statement->equals($value)) {
                return true;
            }
        }
    }

    // ------------------------------------------------------------------------
    // Generators for convenience
    // ------------------------------------------------------------------------

    /**
     * Returns a new object of type Resource if $uri is set, otherwise it will 
     * return a BlankNode
     *
     * @param String $uri
     * @return Resource 
     */
    public function newResource($name_or_uri = null) {
        if (!empty($name_or_uri))
            return new Resource($name_or_uri);
        
        // TODO: check URI and include base namespace;

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

    // ------------------------------------------------------------------------
    // Simple printers
    // ------------------------------------------------------------------------

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

        if (!is_array($statements))
            throw new APIException(API_ERROR . "Array of statements expected and not given!");

        if (empty($statements))
            return "no statements in model";

        $returnString = "";

        foreach ($statements as $statement) {

            if (!Check::isStatement($statement))
                throw new APIException(API_ERROR_STATEMENT);

            $returnString.= $statement->getSubject()->getUri() . " ";
            $returnString.= $statement->getPredicate()->getUri() . " ";
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

}

?>
