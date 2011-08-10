<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        Model.php
 * @version     2011-08-10
 * @package     model
 * @access      public
 * 
 * Description  This class represents the whole RDF model in form of a list 
 *              of statements.
 * 
 * -----------------------------------------------------------------------------
 */
class Model {

    /**
     * List of Statements
     *
     * @var array Array of statements
     */
    private $statements;

    /**
     * A list of URIs
     *
     * @var array Array of URIs
     */
    private $namespaces;

    /**
     * A list of resources used by the serach method to prevent dead lock
     *
     * @var array Array of resources
     */
    private $foundResources;

    /**
     * A uri used for creating nodes with the model
     *
     * @var string 
     */
    private $baseNamespace;

    /**
     * A prefix used for creating nodes with the model
     *
     * @var string 
     */
    private $basePrefix;

    /**
     * A counter for created BlankNodes so that it can be used for creating IDs
     * 
     * @var integer
     */
    private $bnodeCount = 0;

    /**
     * Constructor
     */
    function __construct() {
        
    }

    /**
     * Adds a base URI
     * 
     * Adds a standart namespace to the model, which is used for node creation
     * by the model. So that nodes, that don't have a prefix or full Uri are
     * automatically added to the base namespace.
     *
     * @param string $prefix
     * @param string $uri 
     * @throws APIException
     */
    public function addBaseNamespace($prefix, $namespace) {

        if (!Check::isPrefix($prefix))
            throw new APIException(API_ERROR_PREFIX);

        if (!Check::isNamespace($namespace))
            throw new APIException(API_ERROR_NS);

        $this->baseNamespace = $namespace;
        $this->basePrefix = $prefix;

        $this->addNamespace($prefix, $namespace);
    }

    /**
     * Add a namespace to the model
     * 
     * @param string $prefix
     * @param string $uri 
     * @throws APIException
     */
    public function addNamespace($prefix, $namespace) {

        if (!Check::isPrefix($prefix))
            throw new APIException(API_ERROR_PREFIX);

        if (!Check::isNamespace($namespace))
            throw new APIException(API_ERROR_NS);

        $this->namespaces[$prefix] = $namespace;
    }

    /**
     * Get the URI of the namespace
     *
     * @param string $prefix
     * @return string $uri
     * @throws APIException 
     */
    public function getNamespace($prefix) {

        if (!Check::isPrefix($prefix))
            throw new APIException(API_ERROR_PREFIX);

        return $this->namespaces[$prefix];
    }

    /**
     * Get a list of namespaces inform of namespaces[$prefix] = $uri. May return null
     *
     * @param string $prefix
     * @return string the uri fitting to the $prefix 
     */
    public function getAllNamespaces() {

        return $this->namespaces;
    }

    /**
     * Remove a namespace from the model
     *
     * @param string $prefix
     * @return bool true = removed, false = not removed
     * @throws APIException
     */
    public function removeNamespace($prefix) {

        if (!Check::isPrefix($prefix))
            throw new APIException(API_ERROR_PREFIX);

        unset($this->namespaces[$prefix]);
        return!$this->hasNamespace($prefix);
    }

    /**
     * Checks if the model contains a specific namespace
     *
     * @param string $prefix
     * @return bool true if yes, otherwise false 
     * @throws APIException
     */
    public function hasNamespace($prefix) {

        if (!Check::isPrefix($prefix))
            throw new APIException(API_ERROR_PREFIX);

        return isset($this->namespaces[$prefix]);
    }

    /**
     * Adds a statement or a resource (with properties) to the model. If $double
     * is true (standart) there will be no check performed if the statement is 
     * already part of the model and a copy will be saved. If $double is false
     * double entries are forbidden.
     *
     * @param mixed $statement_or_resource can be Statement or Resource
     * @param bool $double
     * @return bool true if added, false if not
     * @throws APIException
     */
    public function add($statement_or_resource, $double = ALLOW_DUPLICATES) {

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
     * @return bool true if successfull,otherwise false
     * @throws APIException
     */
    private function addStatement($statement, $double = ALLOW_DUPLICATES) {

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
     * @return bool true if successfull,otherwise false
     * @throws APIException
     */
    private function addResource($resource, $double = ALLOW_DUPLICATES) {

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
     * @param mixed $statement_or_resource can be Statement or Resource
     * @return bool true = removed, false = not removed
     * @throws APIException
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
     * @return bool true = removed, false = not removed  
     * @throws APIException
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
     * @return bool true = removed, false = not removed
     * @throws APIException
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
     * @param Resource $subject
     * @param Resource $predicate
     * @param Node $object can be Resource or literal
     * @return array Array of Statements
     * @throws APIException
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
     * @throws APIException
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
     * @return integer 
     */
    public function size() {
        return count($this->statements);
    }

    /**
     * Checks if the model is empty
     *
     * @return bool true if no statements are in the model, otherwise false 
     */
    public function isEmpty() {
        return empty($this->statements);
    }

    /**
     * Checks if the statement is already present in the model
     *
     * @param Statement $statement 
     * @return bool true if model contains the statement, otherwise false
     * @throws APIException
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

    /**
     * Generates a unique ID for a BlankNode
     *
     * @return string ID
     */
    public function generateUniqueId() {
        return "bNode" . $this->bnodeCount++;
    }

    // ------------------------------------------------------------------------
    // Generators for convenience
    // ------------------------------------------------------------------------

    /**
     * Returns a new object of type Resource if $uri is set, otherwise it will 
     * return a BlankNode
     *
     * @param string $uri
     * @return Resource
     * @throws APIException
     */
    public function newResource($name = null) {

        if (!Check::isNamespace($this->baseNamespace))
            throw new APIException(API_ERROR_BASENS);

        if (empty($name))
            return $this->newBlankNode();

        return new Resource($this->baseNamespace, $name);
    }

    /**
     * Returns a new object of type LiteralNode
     *
     * @param string $literal
     * @param string $datatype
     * @return LiteralNode
     * @throws APIException
     */
    public function newLiteralNode($literal, $datatype = STRING) {

        if (!Check::isNamespace($this->baseNamespace))
            throw new APIException(API_ERROR_BASENS);

        return new LiteralNode($literal, $datatype);
    }

    /**
     * Returns a new object of type BlankNode
     *
     * @return BlankNode 
     * @throws APIException
     */
    public function newBlankNode() {

        if (!Check::isNamespace($this->baseNamespace))
            throw new APIException(API_ERROR_BASENS);

        return new BlankNode($this->baseNamespace, $this->generateUniqueId());
    }

    /**
     * Returns a new object of type Statement
     *
     * @param Resource $subject
     * @param Resource $predicate
     * @param Node $object Can be Resource or LiteralNode
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
     * @return string
     */
    public function toString() {
        return $this->statementListToString($this->statements);
    }

    /**
     * Returns a list of provided statements using following format:
     * subjectURI, predicateURI, objectUri or Literal
     *
     * @param array $statements Array of Statements
     * @return string
     * @throws APIException
     */
    public function statemensToString($statements) {

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

    public function toHTML() {
        return $this->statementListToHTMLTable($this->statements);
    }

    public function statementsToHTML($statements) {
        //TODO: Implement statementListToHTMLTable
    }

}

?>
