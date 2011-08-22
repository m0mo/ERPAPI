<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        Autoloader.php
 * @version     2011-08-12
 * @package     util
 * @access      public
 * 
 * Description  Handels all necessary includes so the user does not have to 
 *              care.
 * 
 * -----------------------------------------------------------------------------
 */

// Utilities
include_once "Config.php";
include_once "Constants.php";
include_once "APIException.php";
include_once "Check.php";
include_once 'Utils.php';
include_once 'ERP.php';

// Parsers
//include_once INCLUDE_DIR."parsers/IParser.php";
//include_once INCLUDE_DIR."parsers/RDFXMLParser.php";
//include_once INCLUDE_DIR."parsers/NTripleParser.php";
//include_once INCLUDE_DIR."parsers/TurtleParser.php";
//include_once INCLUDE_DIR."parsers/JsonParser.php";

// Serializers
//include_once INCLUDE_DIR."serializers/ISerializer.php";
//include_once INCLUDE_DIR."serializers/RDFXMLSerializer.php";
//include_once INCLUDE_DIR."serializers/NTripleSerializer.php";
//include_once INCLUDE_DIR."serializers/TurtleSerializer.php";
//include_once INCLUDE_DIR."serializers/JsonSerializer.php";

// Model
include_once INCLUDE_DIR."model/Model.php";
include_once INCLUDE_DIR."model/Node.php";
include_once INCLUDE_DIR."model/Resource.php";
include_once INCLUDE_DIR."model/BlankNode.php";
include_once INCLUDE_DIR."model/LiteralNode.php";
include_once INCLUDE_DIR."model/Statement.php";

?>
