<?php

/**
 * --------------------------------------------------------------------
 * ERP API 
 * --------------------------------------------------------------------
 *
 * Constants for the ERP Sparql engine
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * @name        Constants.php
 * @version     2011-09-01
 * @package     sparql/sparqlEngine
 * @access      public
 * 
 * --------------------------------------------------------------------
 */

define("SPARQL_ERROR", "Sparql Exception: ");
define("SPQRQL_QUERY_RESULT_FORMAT", SPARQL_ERROR."The format of the query could not be determined.");
define("SPQRQL_QUERY_RESULT_SUPPORT", SPARQL_ERROR."The format of the query is not supported.");

/*
 * @see http://www.w3.org/TR/rdf-sparql-query/#QueryForms
 */
define("SELECT", "SELECT");
define("ASK", "ASK");
define("DESCRIBE", "DESCRIBE");
define("CONSTRUCT", "CONSTRUCT");


?>
