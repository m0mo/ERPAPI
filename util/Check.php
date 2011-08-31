<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        InstanceCheck.php
 * @version     2011-08-31
 * @package     util
 * @access      public
 * 
 * Description  This class provides static functions for performing various 
 *              checks
 * 
 * -----------------------------------------------------------------------------
 */
class Check {

    /**
     * Checks if the parameter is a resource
     *
     * @param Node $resource
     * @return bool 
     */
    public static function isResource($resource) {

        return ($resource instanceof Resource);
    }

    /**
     * Checks if parameter is a blank node
     *
     * @param Node $blankNode
     * @return bool 
     */
    public static function isBlankNode($blankNode) {

        return ($blankNode instanceof BlankNode);
    }

    /**
     * Checks if the parameter is a literal node
     *
     * @param LiteralNode $literalNode
     * @return bool 
     */
    public static function isLiteralNode($literalNode) {
        return ($literalNode instanceof LiteralNode);
    }

    /**
     * Checks if the parameter is a valid subject
     *
     * @param Node $subject
     * @return bool
     */
    public static function isSubject($subject) {

        return self::isResource($subject);
    }

    /**
     * Checks if the parameter is a valid predicate
     * 
     * @param Node $node
     * @return bool
     */
    public static function isPredicate($predicate) {

        if (self::isResource($predicate) && !self::isBlankNode($predicate)) {

            // predicate schould not have properties
            $array = $predicate->getProperties();

            if (empty($array)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Checks if the parameter is a valid object
     *
     * @param Node $node
     * @return bool
     */
    public static function isObject($object) {

        return (self::isResource($object) || self::isLiteralNode($object));
    }

    /**
     * Checks if $statement is a valid Statement.
     *
     * @param Statement $statement
     * @return bool
     */
    public static function isStatement($statement) {

        return ($statement instanceof Statement);
    }
    
    /**
     * Checks if the parameter is a String
     *
     * @param string $string
     * @return bool 
     */
    public static function isString($string) {
        
        return is_string($string);
        
    }

    /**
     * Checks if the parameter is a namespace. A namespace has to start with 
     * <i>http://</i> and end with either # or /
     *
     * @param string $namespace 
     * @return bool
     */
    public static function isNamespace($namespace) {

        if (!self::isString($namespace))
            return false;

        return (preg_match('/^(http:\/\/)(.+)(#|\/)$/i', $namespace)) ? true : false;
    }

    /**
     * Checks if the parameter is a valid prefix. A prefix can contain just
     * characters or numbers
     *
     * @param string $prefix 
     * @return bool
     */
    public static function isPrefix($prefix) {

        if (!self::isString($prefix))
            return false;

        return (preg_match('/^([a-z0-9]+)$/i', $prefix)) ? true : false;
    }
    
    /**
     * Checks if the parameter string is a valid name. Names can consist only of
     * characters or numbers
     *
     * @param string $name
     * @return bool 
     */
    public static function isName($name) {

        if (!self::isString($name))
            return false;

         return (preg_match('/^([a-z0-9]+)$/i', $name)) ? true : false;
    }

        
        /**
     * Checks if the parameter string is of form <i>prefix:name</i>
     *
     * @param string $prefixAndName
     * @return bool 
     */
    public static function isPrefixAndName($prefixAndName) {
        
         if (!self::isString($prefixAndName))
            return false;

         return (preg_match('/^([a-z0-9]+):([a-z0-9])+$/i', $prefixAndName)) ? true : false;
    }
    
    /**
     * Checks if the parameter string is an URI. The URI has to start with 
     * <i>http://</i> and end with either a number or character
     *
     * @param string $uri
     * @return bool 
     */
    public static function isUri($uri) {
        
        if (!self::isString($uri))
            return false;

        return (preg_match('/^(http:\/\/)(.+)(#|\/)([a-z0-9]+)$/i', $uri)) ? true : false;
    }
    
    /**
     * Checks if the parameter is instance of a Model
     *
     * @param Model $model 
     * @return bool
     */
    public static function isModel($model) {
        return ($model instanceof Model);
    }
    
    /**
     * Check if the parameter is an array
     *
     * @param array $param
     * @return bool 
     */
    public static function isArray($param) {
        return is_array($param);
    }


}

?>
