<?php

/**
 * --------------------------------------------------------------------
 * ERP API 
 * --------------------------------------------------------------------
 *
 * This class represents an RDF statement containing a triple of subject, 
 * predicate, object
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * @name        Statement.php
 * @version     2011-08-31
 * @package     model
 * @access      public
 *
 * --------------------------------------------------------------------
 */
class Statement {

    /**
     * The subject of the statement
     *
     * @var Resource 
     */
    private $subject;
    
    /**
     * The predicate of the statement
     *
     * @var Resource
     */
    private $predicate;
    
    /**
     * The object of the statement
     *
     * @var Node Can be Resource or LiteralNode
     */
    private $object;

    /**
     * Constructs a statement containing a subject, predicate and object
     * This function does not add the predicate and object as property to 
     * the subject.
     *
     * @param Resource $subject
     * @param Resource $predicate
     * @param Resource or Literal $object 
     */
    public function __construct($subject, $predicate, $object) {

        if (!Check::isSubject($subject))
            throw new APIException(ERP_ERROR_SUBJECT);
        
        if (!Check::isPredicate($predicate))
            throw new APIException(ERP_ERROR_PREDICATE);
        
        if (!Check::isObject($object))
            throw new APIException(ERP_ERROR_OBJECT);
        
        // Save the predicate and object to represent the statement
        $this->subject = $subject;
        $this->predicate = $predicate;
        $this->object = $object;
        
    }

    /**
     * Returns the subject of the statement triple
     *
     * @return Resource
     */
    public function getSubject() {
        return $this->subject;
    }

    /**
     * Returns the predicate of the statement triple
     *
     * @return Resource 
     */
    public function getPredicate() {
        return $this->predicate;
    }

    /**
     * Returns the object of the statement triple
     *
     * @return mixed Resource, BlankNode or Literal 
     */
    public function getObject() {
        return $this->object;
    }

    /**
     * Checks if two statements are equal
     * 
     * @param Statement $that
     * @return bool true or false
     */
    public function equals($that) {
        
        if(!Check::isStatement($that))
                return false;

        return $this->getSubject()->equals($that->getSubject()) && 
               $this->getPredicate()->equals($that->getPredicate()) && 
               $this->getObject()->equals($that->getObject());
    }
    
    public function toString() {
        $string = "\n";
        $string.= "Subject: ".$this->getSubject()->toString(). "\n";
        $string.= "Predicate: ".$this->getPredicate()->toString(). "\n";
        $string.= "Object: ".$this->getObject()->toString(). "\n";
        
        return $string;
    }

}

?>
