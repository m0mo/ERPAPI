<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        settings.php
 * @version     2011-08-10
 * @package     tests
 * @access      public
 * 
 * Description  Settings for tests
 * 
 * -----------------------------------------------------------------------------
 */


require_once 'PHPUnit/Autoload.php';
require_once '../API.php';

// Parsers
include_once INCLUDE_DIR."parsers/IParser.php";
include_once INCLUDE_DIR."parsers/RDFXMLParser.php";
include_once INCLUDE_DIR."parsers/NTripleParser.php";
include_once INCLUDE_DIR."parsers/TurtleParser.php";
include_once INCLUDE_DIR."parsers/JsonParser.php";

// Serializers
include_once INCLUDE_DIR."serializers/ISerializer.php";
include_once INCLUDE_DIR."serializers/RDFXMLSerializer.php";
include_once INCLUDE_DIR."serializers/NTripleSerializer.php";
include_once INCLUDE_DIR."serializers/TurtleSerializer.php";
include_once INCLUDE_DIR."serializers/JsonSerializer.php";

// sparql
include_once INCLUDE_DIR."sparql/sparqlEngine/SparqlEngine.php";

define("NS", "http://example.org/");
define("PREFIX", "ex");


?>
