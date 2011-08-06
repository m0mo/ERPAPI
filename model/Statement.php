<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        Statement.php
 * @version     0.2.0 (Aug 6, 2011)
 * @package     model
 * @access      public
 * 
 * Description  This class represents an RDF statement containing a triple of
 *              subject, predicate, object
 * 
 * -----------------------------------------------------------------------------
 */
class Statement {

    private $subject;
    private $predicate;
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

        if (!Check::isSubject($subject)) {
            throw new APIException(ERP_ERROR_SUBJECT);
        }

        if (!Check::isPredicate($predicate)) {
            throw new APIException(ERP_ERROR_PREDICATE);
        }

        if (!Check::isObject($object)) {
            throw new APIException(ERP_ERROR_OBJECT);
        }

        // Save the predicate and object to represent the statement
        $this->subject = $subject;
        $this->predicate = $predicate;
        $this->object = $object;

//        $prop = $this->subject->getProperty($predicate);
//        
//        if(!empty($prop))
//            $this->subject->addProperty($predicate, $object);
        
    }

    /**
     * Returns the subject of the statement triple
     *
     * @return Resource or BlankNode 
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
     * @return Resource, BlankNode or Literal 
     */
    public function getObject() {
        return $this->object;
    }

    /**
     * Checks if two statements are equal
     * 
     * @param Statement $that
     * @return true or false
     */
    public function equals($that) {
        
        if(!is_a($that, Statement))
                return false;

        return $this->getSubject()->equals($that->getSubject()) && 
               $this->getPredicate()->equals($that->getPredicate()) && 
               $this->getObject()->equals($that->getObject());
    }

}

?>
