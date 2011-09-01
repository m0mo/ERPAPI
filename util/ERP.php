<?php

/**
 * --------------------------------------------------------------------
 * ERP API 
 * --------------------------------------------------------------------
 *
 * Offers various static functions
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        ERP.php
 * @version     2011-08-22
 * @package     util
 * @access      public
 * 
 * --------------------------------------------------------------------
 */
class ERP {
    
    /**
     * Returns a new Model
     *
     * @return Model 
     */
    public static function getModel() {
        return new Model();
    }

    /**
     * Returns a RDF XML Parser object
     *
     * @return RDFXMLParser 
     */
    public static function getRDFXMLParser() {
        
        include_once(INCLUDE_DIR . "parsers/RDFXMLParser.php");
        return new RDFXMLParser();
    }

    /**
     * Returns a N-Tripple Parser object
     *
     * @return NTripleParser 
     */
    public static function getNTripleParser() {

        include_once(INCLUDE_DIR . "parsers/NTripleParser.php");
        return new NTripleParser();
    }

    /**
     * Returns a Turtle Parser object
     *
     * @return TurtleParser 
     */
    public static function getTurtleParser() {
        
        include_once(INCLUDE_DIR . "parsers/TurtleParser.php");
        return new TurtleParser();
    }

    /**
     * Returns a Json Parser object
     *
     * @return JsonParser 
     */
    public static function getRDFJsonParser() {
        
        include_once(INCLUDE_DIR . "parsers/JsonParser.php");
        return new JsonParser();
    }
    
       /**
     * Returns a RDF XML Serializer object
     *
     * @return RDFXMLParser 
     */
    public static function getRDFXMLSerializer() {
        
        include_once(INCLUDE_DIR . "serializers/RDFXMLSerializer.php");
        return new RDFXMLSerializer();
    }

    /**
     * Returns a N-Tripple Serializer object
     *
     * @return NTripleParser 
     */
    public static function getNTripleSerializer() {

        include_once(INCLUDE_DIR . "serializers/NTripleSerializer.php");
        return new NTripleSerializer();
    }

    /**
     * Returns a Turtle Serializer object
     *
     * @return TurtleParser 
     */
    public static function getTurtleSerializer() {
        
        include_once(INCLUDE_DIR . "serializers/TurtleSerializer.php");
        return new TurtleSerializer();
    }

    /**
     * Returns a Json Serializer object
     *
     * @return JsonParser 
     */
    public static function getRDFJsonSerializer() {
        
        include_once(INCLUDE_DIR . "serializers/JsonSerializer.php");
        return new JsonSerializer();
    }


}

?>
