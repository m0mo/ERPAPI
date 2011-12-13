<?php

/**
 * --------------------------------------------------------------------
 * ERP API
 * --------------------------------------------------------------------
 *
 * This class represents the whole RDF model in form of a list of statements.
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com>
 * @name        Model.php
 * @version     2011-09-01
 * @package     model
 * @access      public
 *
 * --------------------------------------------------------------------
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
     * List for processed Resources for the recusive functions
     *
     * @var array
     */
    private $processedResources;

    /**
     * List of still processing resources for the recusive functions
     *
     * @var array
     */
    private $processingResources;

    /**
     * List of indexed statements
     *
     * @var array
     */
    private $indexedStatements;

    /**
     * List of returned Resources for the searchResources() function
     *
     * @var array
     */
    private $returnResources;

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
    public function getNamespaces() {

        return $this->namespaces;
    }

    /**
     * Returns an array of all stored statements
     *
     * @return array
     */
    public function getStatements() {
        return $this->statements;
    }

    /**
     * Returns an array of all stored statements
     *
     * @return array
     */
    public function getTripples() {
        return $this->getStatements();
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

        $bool = false;

        // if there are no properties we can't add any statements
        if (!$resource->hasProperties())
            return false;

        $properties = $resource->getProperties();

        $r = clone $resource;
        $r->removeAllProperties();

        foreach ($properties as $prop) {

            $predicate = $prop["predicate"];
            $object = $prop["object"];

            if (Check::isSubject($object) && $object->hasProperties()) {
                $bool = $this->addResource($object, $double) || $bool;
                $object = clone $object;
                $object->removeAllProperties();
            }

            $bool = $this->addStatement(new Statement($r, $predicate, $object), $double) || $bool;
        }

        return $bool;
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
        } else if (Check::isResource($statement_or_resource)) {
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

        $bool = false;

        foreach ($this->statements as $key => $value) {

            if ($statement->equals($value)) {
                unset($this->statements[$key]);
                $bool = true;
            }
        }

        return $bool;
    }

    /**
     * This function removes a resource (with all properties recursively) from the model
     *
     * @param Resource $resource
     * @return bool true = removed, false = not removed
     * @throws APIException
     */
    private function removeResource($resource) {

        if (!Check::isResource($resource))
            throw new APIException(ERP_ERROR_SUBJECT);

        $bool = false;
        $properties = $resource->getProperties();

        // if there are no properties we can't remove any statements
        if (empty($properties)) {
            return false;
        }

        foreach ($properties as $prop) {

            $predicate = $prop["predicate"];
            $object = $prop["object"];

            $bool = $this->removeStatement(new Statement($resource, $predicate, $object)) || $bool;

            if (Check::isResource($object))
                $bool = $this->removeResource($object) || $bool;
        }

        return $bool;
    }
    
    /**
     * Removes a statement or resource (and its properties) of the model
     *
     * @param mixed $statement_or_resource can be Statement or Resource
     * @return bool true = removed, false = not removed
     * @throws APIException
     */
    public function edit($old_statement_or_resource, $new_statement_or_resource) {

        if (empty($old_statement_or_resource))
            throw new APIException(API_ERROR . "Parameter is null");
        
        if (empty($new_statement_or_resource))
            throw new APIException(API_ERROR . "Parameter is null");

        if (Check::isStatement($old_statement_or_resource) && Check::isStatement($new_statement_or_resource)) {
            return $this->editStatement($old_statement_or_resource, $new_statement_or_resource);
        } else if (Check::isResource($old_statement_or_resource) && Check::isResource($new_statement_or_resource)) {
            return $this->editResource($old_statement_or_resource, $new_statement_or_resource);
        } else {
            // if nothing above fits, throw new exception
            throw new APIException(API_ERROR . "tryed to add non-statement or non-resource to the model.");
        }

        return false;
    }

    /**
     * Edits a statement to the list of statements
     *
     * @param Statement $statement
     * @return bool true = removed, false = not removed
     * @throws APIException
     */
    private function editStatement($oldstatement, $newstatement) {

        if (!Check::isStatement($oldstatement) || !Check::isStatement($newstatement))
            throw new APIException(API_ERROR_STATEMENT);

        $this->removeStatement($oldstatement);
        $this->addStatement($newstatement);
        
    }

    /**
     * This function removes a resource (with all properties recursively) from the model
     *
     * @param Resource $resource
     * @return bool true = removed, false = not removed
     * @throws APIException
     */
    private function editResource($oldresource, $newresource) {

        if (!Check::isResource($oldresource) || !Check::isResource($newresource))
            throw new APIException(ERP_ERROR_SUBJECT);

        $this->removeResource($oldresource);
        $this->addResource($newresource);
    }

    /**
     * Returns a list of statements that contain the parameters
     *
     * @param Resource|BlankNode $subject
     * @param Resource $predicate
     * @param Resource|BlankNode|LiteralNode $object
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

        $foundStatements = array();

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
     * Searchs for a resource and adds all properties to it.
     *
     * @param Resource|BlankNode $subject
     * @param Resource $predicate
     * @param Resource|BlankNode|LiteralNode $object
     * @return array|Resource
     * @throws APIException
     */
    public function searchResources($subject = null, $predicate = null, $object = null) {

        // search cares about the input checks
        $statements = $this->search($subject, $predicate, $object);

        $wholeList = $statements;

        //preperations
        $this->processedResources = null;
        $this->processingResources = null;

        foreach ($statements as $statement) {

            $this->processedResources[$statement->getSubject()->getUri()] = true;

            $object = $statement->getObject();

            $addList = array();

            if (Check::isSubject($object))
                $addList = $this->searchResourcesRecursive($object);


            if (Check::isArray($addList))
                $wholeList = array_merge($wholeList, $addList);
        }

//        print_r($wholeList);
        // cleanup
        $this->processedResources = null;
        $this->processingResources = null;

        $resources = $this->statementListToResourceList($wholeList);

        if (empty($resources))
            return null;

        if (count($resources) == 1)
            return end($resources);

        return $resources;
    }

    /**
     * Helper for searchResources(). Recusrively searches for more resources.
     *
     * @param Resource $subject
     * @return array List of Statements
     */
    private function searchResourcesRecursive($subject) {

        if (!Check::isSubject($subject))
            return null;

        $key = $subject->getUri();

        // prevent dead lock
        if (isset($this->processedResources[$key]))
            return null;

        // prevent dead lock
        if (isset($this->processingResources[$key]))
            return null;

        $statements = $this->search($subject, null, null);
        $returnList = $statements;

        if (!empty($statements)) {

            // set the resource as processing
            $this->processingResources[$key] = true;

            foreach ($statements as $statement) {

                $object = $statement->getObject();

                $addList = array();

                // if object is a resource or blank node try to recursively call
                // this function, but only if there are some statements in the
                // list that can be used.
                if (Check::isSubject($object))
                    $addList = $this->searchResourcesRecursive($object);

                if (Check::isArray($addList))
                    $returnList = array_merge($returnList, $addList);
            }

            unset($this->processingResources[$key]);
        }

        $this->processedResources[$key] = true;

//        print_r($returnList);

        return $returnList;
    }

    /**
     * Transforms a lost of statements to a list of resources plus their properties.
     * The returned list is indexed with the resource URI.
     *
     * @param array $statements
     * @return array
     * @throws APIException
     */
    public function statementListToResourceList($statements) {

        // input checks
        if (!Check::isArray($statements))
            throw new APIException(API_ERROR . "Parameter is not an array.");

        // preperation
        $this->indexedStatements = array();
        $this->processedResources = null;
        $this->processingResources = null;

        // indexing
        foreach ($statements as $statement) {

            if (!Check::isStatement($statement))
                throw new APIException(API_ERROR . "Array does not contain an statement on position " . $key . ".");

            // index array
            $this->indexedStatements[$statement->getSubject()->getURI()][] = $statement;
        }

        $this->returnResources = array();

        foreach ($this->indexedStatements as $key => $statementArray) {
            if (!isset($this->processedResources[$key]))
                $this->returnResources[$key] = $this->statementListToResourceListRecursive($key, $statementArray);
        }

//        print_r($this->returnResources);

        $resources = $this->returnResources;

        //cleanup
        $this->indexedStatements = null;
        $this->processedResources = null;
        $this->processingResources = null;
        $this->returnResources = null;

        return $resources;
    }

    /**
     * Helper for statementListToResourceList(). Used for recursive calls
     *
     * @param string $key
     * @param array $statements
     * @return array
     */
    private function statementListToResourceListRecursive($key, $statements) {

        // input checks
        if (!Check::isArray($statements))
            return null;

        // prevent dead lock
        if (isset($this->processedResources[$key])) {

            // if it was processed already it may be in the retrun array
            // If it is called again it means that another resource requires it as
            // a child. Therefore we remove it from the return array.
            unset($this->returnResources[$key]);
            return $this->processedResources[$key];
        }

        // prevent dead lock
        if (isset($this->processingResources[$key])) {

            // if the resource is still in process it means that there is a circle
            // in the graph
            return null;
        }

        // set the resource as processing
        $this->processingResources[$key] = true;

        // if there are no statements we cant do anything
        if (!empty($statements)) {

            // since all statements have the same subject we can just take one out
            // that will be used as our return value
            $resource = end($statements)->getSubject();
            $resource->removeAllProperties();

            foreach ($statements as $statement) {

                if (!Check::isStatement($statement))
                    throw new APIException(API_ERROR . "Statement expected.");

                $object = $statement->getObject();

                // if object is a resource or blank node try to recursively call
                // this function, but only if there are some statements in the
                // list that can be used.
                if (Check::isSubject($object) && isset($this->indexedStatements[$object->getUri()]))
                    $object = $this->statementListToResourceListRecursive($object->getUri(), $this->indexedStatements[$object->getUri()]);

                $object = (empty($object)) ? $statement->getObject() : $object;

                // add the properties
                $resource->addProperty($statement->getPredicate(), $object);
            }

            $this->processedResources[$key] = $resource;
        }

        // resource is finnished processing
        unset($this->processingResources[$key]);


        return $resource;
    }

    /**
     * Transforms a list of resources with properties to a list of statements
     *
     * @param array $resources
     * @return array
     * @throws APIException
     */
    public function resourceListToStatementList($resources) {

        if (!Check::isArray($resources))
            throw new APIException(API_ERROR . "Parameter is not an array.");

        $returnArray = array();

        foreach ($resources as $key => $resource) {

            if (!Check::isResource($resource))
                throw new APIException(API_ERROR . "Array does not contain an resource on position " . $key . ".");

            if (!$resource->hasProperties())
                throw new APIException(API_ERROR . "Resource does not contain properties.");

            foreach ($resource->getProperties() as $prop)
                $returnArray[] = new Statement($resource, $prop["predicate"], $prop["object"]);
        }

        return $returnArray;
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
     * @return string unique ID
     */
    public function generateUniqueId() {
        return BNODE . ++$this->bnodeCount;
    }
    
    /**
     * Performs a SPARQL Query against the model. NOTE: not all SPARQL constructs 
     * are supported
     *
     * @param string $query
     * @param string $format 
     * @return mixed 
     */
    public function sparqlQuery($queryString, $format = "array") {
        
        require_once INCLUDE_DIR.'sparql/sparqlEngine/SparqlEngine.php';
        return SparqlEngine::doQuery($queryString, $this, $format);
    }

    /**
     * Saves the model to a file, Default is the RDF/XML format.
     *
     * @param string $filename
     * @param string $type defines the type of the output (rdf, nt, turtle, json)
     * @return bool
     */
    public function save($filename, $type ='rdf') {

        switch ($type) {
            case "rdf":
                $serializer = ERP::getRDFXMLSerializer();
                break;

            case "nt":
                $serializer = ERP::getNTripleSerializer();
                break;

            case "turtle":
                $serializer = ERP::getTurtleSerializer();
                break;

            case "json":
                $serializer = ERP::getRDFJsonSerializer();
                break;

            default :
                throw new APIException(API_ERROR_FILETYPE);
        }

        return $serializer->serialize($filename, $this);
    }

    /**
     * Loads a model from a file and returns it.
     *
     * @param string $filename
     * @param string $type defines the type of the inputformat (rdf, nt, turtle, json)
     * @return bool
     */
    public function load($filename, $type ='rdf') {

        switch ($type) {
            case "rdf":
                $parser = ERP::getRDFXMLParser();
                break;

            case "nt":
                $parser = ERP::getNTripleParser();
                break;

            case "turtle":
                $parser = ERP::getTurtleParser();
                break;

            case "json":
                $parser = ERP::getRDFJsonParser();
                break;

            default :
                throw new APIException(API_ERROR_FILETYPE);
        }

        return $parser->parse($filename, $this);
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
     */
    public function newBlankNode() {

        return new BlankNode($this->generateUniqueId());
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
     * @param string $type defines the type of the output (rdf, nt, turtle, json)
     * @return string
     */
    public function toString($type = null) {

        switch ($type) {
            case "rdf":
                $serializer = ERP::getRDFXMLSerializer();
                break;

            case "nt":
                $serializer = ERP::getNTripleSerializer();
                break;

            case "turtle":
                $serializer = ERP::getTurtleSerializer();
                break;

            case "json":
                $serializer = ERP::getRDFJsonSerializer();
                break;

            case null:
                return $this->statemensToString($this->statements);

            default :
                throw new APIException(API_ERROR_FILETYPE);
        }

        return $serializer->serializeToString($this);
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

            $returnString = null;

            if (!Check::isStatement($statement))
                throw new APIException(API_ERROR_STATEMENT);

            $returnString.= $statement->getSubject()->getUri() . " ";
            $returnString.= $statement->getPredicate()->getUri() . " ";
            $returnString.= (Check::isStatement($statement)) ? $statement->getObject()->getLiteral() : $statement->getObject()->getUri();
            $returnString.= " \n";
        }

        return $returnString;
    }

    /**
     * Returns a HTML table of the model's statements
     *
     * @return type
     */
    public function toHTML() {
        return $this->statementsToHTML($this->statements);
    }

    /**
     * Returns a HTML table of the statement array
     *
     * @param type $statements
     * @return type
     */
    public function statementsToHTML($statements) {

        if (!is_array($statements))
            throw new APIException(API_ERROR . "Array of statements expected and not given!");

        if (empty($statements))
            return "no statements in model";

        //TODO: Implement statementListToHTMLTable
        return "";
    }

}

?>
