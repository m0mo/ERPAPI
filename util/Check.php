<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        InstanceCheck.php
 * @version     2011-08-06
 * @package     util
 * @access      public
 * 
 * Description  here
 * 
 * -----------------------------------------------------------------------------
 */
class Check {

    /**
     * Checks if a node can be used as a subject
     *
     * @param Node $node
     * @return true if node can fit a subject, otherwise false 
     */
    public static function isSubject($subject) {

        if ($subject instanceof Resource)
            return true;

        return false;
    }

    /**
     * Checks if a node can be used as a predicate
     *
     * @param Node $node
     * @return true if node can fit a predicate, otherwise false 
     */
    public static function isPredicate($predicate) {

        //TODO: check if is correcht Ressource (no properties)

        if ($predicate instanceof Resource && !($predicate instanceof BlankNode)) {

            $array = $predicate->getProperties();

            if (empty($array)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if a node can be used as a object
     *
     * @param Node $node
     * @return true if node can fit an object, otherwise false 
     */
    public static function isObject($object) {

        if ($object instanceof Resource || $object instanceof LiteralNode)
            return true;

        return false;
    }

    /**
     * Checks if $statement is a valid Statement.
     *
     * @param Statement $statement
     * @return true if valid, otherwise false 
     */
    public static function isStatement($statement) {

        if ($statement instanceof Statement)
            return true;

        return false;
    }

    /**
     * Checks if the URI is valid.
     *
     * @param String $uri 
     * @return true if valid, otherwise false
     */
    public function isValidURI($uri) {
        
        if(!is_string($uri))
            return false;
        
        // TODO: implement

        return true;
    }

    /**
     * Checks if the prefix is valid.
     *
     * @param String $prefix 
     * @return true if valid, otherwise false
     */
    public function isValidPrefix($prefix) {
        
        if(!is_string($prefix))
            return false;
        
        // TODO: implement

        return true;
    }

}

?>
