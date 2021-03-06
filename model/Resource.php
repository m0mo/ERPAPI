<?php

/**
 * --------------------------------------------------------------------
 * ERP API 
 * --------------------------------------------------------------------
 *
 * This class represents a resource node of RDF
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * @name        Resource.php
 * @version     2011-08-31
 * @package     model
 * @access      public
 * 
 * --------------------------------------------------------------------
 */
class Resource extends Node {

    /**
     * The uri of the resource
     *
     * @var string 
     */
    protected $uri;

    /**
     * The name of the resource
     *
     * @var string 
     */
    protected $name;

    /**
     * An array of properties of the resource
     *
     * @var array containing array("predicate" => $predicate, "object" => $object); 
     */
    protected $properties;
    
    /**
     *
     * @var String the type of the node, for example, rdfs:Class, rdfs:Property, ...
     */
    private $type;

    /**
     * Creates a new Resource from an URI and a name.
     *
     * @param string $uri
     * @param string $name 
     * @throws APIException
     */
    function __construct($namespace_or_uri, $name = null) {

        // if $name is not a string $namespace_or_uri has to be an uri
        if (!Check::isUri($namespace_or_uri) && !Check::isNamespace($namespace_or_uri))
            throw new APIException(API_ERROR_URI);

        // if $namespace_or_uri is a namespace $name has to be a valid name
        if (Check::isNamespace($namespace_or_uri) && !Check::isName($name))
            throw new APIException(API_ERROR_NAME);
        
        $this->type = RDF_NS.RDF_DESCRIPTION;

        $this->uri = (Check::isNamespace($namespace_or_uri)) ? $namespace_or_uri . $name : $namespace_or_uri;
        $this->name = (Check::isNamespace($namespace_or_uri)) ? $name : Utils::getName($namespace_or_uri);
    }

    /**
     * Using this method it is possible to create a statement using the 
     * resource class. The created statement will not be automatically added 
     * to the model.
     *
     * @param Resource $predicate
     * @param Node $object 
     * @return Statement of the added property
     * @throws APIException
     */
    public function addProperty($predicate, $object) {

        if (!Check::isPredicate($predicate))
            throw new APIException(ERP_ERROR_PREDICATE);

        if (!Check::isObject($object))
            throw new APIException(ERP_ERROR_OBJECT);

        $this->properties[$predicate->getUri()] = array("predicate" => $predicate, "object" => $object);

        return $this;
    }

    /**
     * Check if the resource has a specific property, independend of the content
     *
     * @param Resource $predicate
     * @return bool true if resource has property, otherwise false 
     * @throws APIException
     */
    public function hasProperty($predicate) {

        if (!Check::isPredicate($predicate))
            throw new APIException(ERP_ERROR_PREDICATE);

        return isset($this->properties[$predicate->getURI()]);
    }
    
    /**
     * Checks if the resource has any properties
     *
     * @return bool 
     */
    public function hasProperties() {

        return !empty($this->properties);
    }

    /**
     * Returns the object of the property
     *
     * @param Resource $predicate
     * @return Node Resource or LiteralNode
     * @throws APIException
     */
    public function getProperty($predicate) {

        if (!Check::isPredicate($predicate))
            throw new APIException(ERP_ERROR_PREDICATE);
        
        if(!$this->hasProperty($predicate))
            return null;

        return $this->properties[$predicate->getURI()]["object"];
    }

    /**
     * Removes the predicate and its object as a property
     *
     * @param Resource $predicate
     * @return bool true if success else false 
     * @throws APIException
     */
    public function removeProperty($predicate) {

        if (!Check::isPredicate($predicate))
            throw new APIException(ERP_ERROR_PREDICATE);

        unset($this->properties[$predicate->getURI()]);

        return!isset($this->properties[$predicate->getURI()]);
    }

    /**
     * Returns the URI of the resource
     *
     * @return string
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * Returns the namespace of the node
     *
     * @return string 
     */
    public function getNamespace() {
        return Utils::getNamespace($this->uri);
    }

    /**
     * Returns the name of the Resource
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Returns an array of the resources properties
     *
     * @return array $properties[$predicate->getUri()] => array("predicate" => 
     *                          $predicate, "object" => $object);
     */
    public function getProperties() {
        return $this->properties;
    }
    
    public function removeAllProperties() {
        $this->properties = null;       
    }
    
    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    
    /**
     * Checks if two Resources are the same
     *
     * @param Node $that
     * @return bool true if equal, else false 
     */
    public function equals($that) {

        if (parent::equals($that))
            return true;

        if (Check::isResource($that) && $this->getURI() == $that->getURI()) {
            return true;
        }

        return false;
    }

}

?>
