<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        Resource.php
 * @version     0.1.5 (Aug 6, 2011)
 * @package     model
 * @access      public
 * 
 * Description  This class represents a resource node of RDF
 * 
 * -----------------------------------------------------------------------------
 */
class Resource extends Node {

    protected $uri;
    protected $properties;
    
    /**
     * Creates a new Resource from an URI
     *
     * @param String $uri 
     */
    function __construct($uri) {
        $this->uri = $uri;
    }

    /**
     * Using this method it is possible to create a statement using the 
     * resource class. The created statement will not be automatically added 
     * to the model.
     *
     * @param Resource $predicate
     * @param Node $object 
     * @return Statement of the added property
     */
    public function addProperty($predicate, $object) {
        
        if (!Check::isPredicate($predicate)) {
            throw new APIException(ERP_ERROR_PREDICATE);
        }
        
        if (!Check::isObject($object)) {
            throw new APIException(ERP_ERROR_OBJECT);
        }
        
        $this->properties[$predicate->getUri()] = array("predicate" => $predicate, "object" => $object);
        
        return $this;
        
    }
    
    /**
     * Check if the resource has a specific property, independend of the content
     *
     * @param Resource $predicate
     * @return true if resource has property, otherwise false 
     */
    public function hasProperty($predicate) {
        
        if (!Check::isPredicate($predicate)) {
            throw new APIException(ERP_ERROR_PREDICATE);
        }
        
        return !isset($this->properties[$predicate->getURI()]);
    }
    
    /**
     * Returns the object of the property
     *
     * @param Predicate $predicate
     * @return Node 
     */
    public function getProperty($predicate) {
        
        if (!Check::isPredicate($predicate)) {
            throw new APIException(ERP_ERROR_PREDICATE);
        }
        
        return $this->properties[$predicate->getURI()]["object"];
    }
    
    /**
     * Removes the predicate and its object as a property
     *
     * @param Resource $predicate
     * @return true if success else false 
     */
    public function removeProperty($predicate) {
        
        if (!Check::isPredicate($predicate)) {
            throw new APIException(ERP_ERROR_PREDICATE);
        }
        
        unset ($this->properties[$predicate->getURI()]);
        
        return !isset($this->properties[$predicate->getURI()]);
    }
    
    /**
     * Returns the URI of the resource
     *
     * @return String
     */
    public function getUri() {
        return $this->uri;
    }
    
    /**
     * Returns an array of the resources properties
     *
     * @return $properties[$predicate->getUri()] => array("predicate" => 
     *                          $predicate, "object" => $object);
     */
    public function getProperties() {
        return $this->properties;
    }
    
    /**
     * Checks if two Resources are the same
     *
     * @param Node $that
     * @return true if equal, else false 
     */
    public function equals($that) {
        
        if(parent::equals($that))
            return true;
        
        if (is_a($that, Resource) && $this->getURI() == $that->getURI()) {
            return true;
        }
        
        return false;       
    }


}

?>
