<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        Literal.php
 * @version     2011-08-11
 * @package     model
 * @access      public
 * 
 * Description  This class represents a literal node of RDF
 * 
 * -----------------------------------------------------------------------------
 */
class LiteralNode extends Node {

    /**
     * The type of the literal node
     *
     * @var string 
     */
    private $datatype;

    /**
     * The value of the literal node
     *
     * @var string 
     */
    private $literal;

    /**
     * The language of the literal node
     *
     * @var string 
     */
    private $language;

    /**
     * Creates a new literal node
     *
     * @param string $literal
     * @param string $datatype Standart is string
     * @throws APIException
     */
    function __construct($literal, $datatype = STRING, $language = null) {

        // if $name is not a string $namespace_or_uri has to be an uri
        if (!Check::isString($literal))
            throw new APIException(API_ERROR_STRING);

        $this->datatype = $datatype;
        $this->literal = $literal;
        $this->language = $language;
    }

    /**
     * Returns the datatype of the literal node
     *
     * @return string 
     */
    public function getDatatype() {
        return $this->datatype;
    }

    /**
     * Returns the value of the literal node
     *
     * @return string 
     */
    public function getLiteral() {
        return $this->literal;
    }

    /**
     * Returns the language of the literal node. May return null
     *
     * @return string 
     */
    public function getLanguage() {
        return $this->language;
    }
    
    /**
     * Checks if the Literal has set a datatype
     *
     * @return bool 
     */
    public function hasDatatype() { 
        return !empty($this->datatype);
    }
    
    /**
     * checks if the Literal has set a language
     *
     * @return bool 
     */
    public function hasLanguage() { 
        return !empty($this->language);
    }

    /**
     * Checks if two literal nodes are equal
     *
     * @param LiteralNode $that
     * @return bool true if equal, otherwise false 
     */
    public function equals($that) {

        if ($this == $that) {
            return true;
        }
        if (($that == NULL) or !(is_a($that, LiteralNode))) {
            return false;
        }

        return ($this->getLiteral() == $that->getLiteral() && $this->getDatatype() == $that->getDatatype() && $this->getLanguage() == $that->getLanguage());
    }

}

?>
