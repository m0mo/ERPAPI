<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        Literal.php
 * @version     2011-08-07
 * @package     model
 * @access      public
 * 
 * Description  here
 * 
 * -----------------------------------------------------------------------------
 */
class LiteralNode extends Node {
    
    private $datatype;
    private $literal;
    
    /**
     * Creates a new literal node
     *
     * @param String $literal
     * @param String $datatype 
     */
    function __construct($literal, $datatype = STRING) {
        $this->datatype = $datatype;
        $this->literal = $literal;
    }
    
    /**
     * Returns the datatype of the literal node
     *
     * @return String 
     */
    public function getDatatype() {
        return $this->datatype;
    }

    /**
     * Returns the value of the literal node
     *
     * @return String 
     */
    public function getLiteral() {
        return $this->literal;
    }
    
    /**
     * Checks if two literal nodes are equal
     *
     * @param LiteralNode $that
     * @return true if equal, otherwise false 
     */
    public function equals($that) {

        if ($this == $that) {
            return true;
        }
        if (($that == NULL) or !(is_a($that, LiteralNode))) {
            return false;
        }

        if ($this->getLiteral() == $that->getLiteral() && $this->getDatatype() == $that->getDatatype()) {
            return true;
        }

        return false;
    }

   
}

?>
