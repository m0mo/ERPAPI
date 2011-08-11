<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        constants.php
 * @version     2011-08-11
 * @package     util
 * @access      public
 * 
 * Description  Contains a list of definitions used by the API
 * 
 * -----------------------------------------------------------------------------
 */

// -----------------------------------------------------------------------------
// Settings
// -----------------------------------------------------------------------------

define("ALLOW_DUPLICATES", false);
define("INCLUDE_DIR", "../");

// used for unique bnode id
define("BNODE", "bNode");

// -----------------------------------------------------------------------------
// Errors
// -----------------------------------------------------------------------------

define("API_ERROR", "Easy RDF for PHP Exception: ");
define("ERP_ERROR_SUBJECT", API_ERROR . "Parameter is not a subject (has to be a resource)");
define("ERP_ERROR_PREDICATE", API_ERROR . "Parameter is not a predicate (has to be a resource and can't be a blank node)");
define("ERP_ERROR_OBJECT", API_ERROR . "Parameter is not an object (has to be a resource or a literal node)");
define("API_ERROR_STATEMENT", API_ERROR . "Parameter is not a statement");
define("API_ERROR_MODEL", API_ERROR . "Parameter is not a model");
define("API_ERROR_URI", API_ERROR . "Parameter is not a valid URI");
define("API_ERROR_NS", API_ERROR . "Parameter is not a valid namespace");
define("API_ERROR_PREFIX", API_ERROR . "Parameter is not a valid namespace");
define("API_ERROR_BASENS", API_ERROR . "Please provide a base namespace");
define("API_ERROR_STRING", API_ERROR . "Parameter is not a string");

define("API_ERROR_FILETYPE", API_ERROR . "This type is not supportet, please choose 'rdf' or 'nt'!");


// -----------------------------------------------------------------------------
// Types
// -----------------------------------------------------------------------------
define("STRING", "string");
define("BOOL", "boolean");
define("INTEGER", "integer");
define("DATE", "date");

// -----------------------------------------------------------------------------
// XML
// -----------------------------------------------------------------------------

define("XML_PREFIX", "xml");
define("XML_DECLARATION_PREFIX", "xmlns");
define("XML_NS","http://www.w3.org/2000/xmlns/" );
define("XML_LANG","lang");;
define("XML_SCHEMA_NS","http://www.w3.org/2001/XMLSchema#");

// -----------------------------------------------------------------------------
// RDF
// -----------------------------------------------------------------------------

define("RDF_NS","http://www.w3.org/1999/02/22-rdf-syntax-ns#" );
define("RDF_PREFIX","rdf" );
define("RDF_RDF","RDF");
define("RDF_DESCRIPTION","Description");
define("RDF_ID","ID");
define("RDF_ABOUT","about");
define("RDF_RESOURCE","resource");
define("RDF_DATATYPE","datatype");

// -----------------------------------------------------------------------------
// RDF Schema
// -----------------------------------------------------------------------------

define("RDFS_NS","http://www.w3.org/2000/01/rdf-schema#" );
define("RDFS_DATATYPE_NS","http://www.w3.org/TR/xmlschema-2" );
define("RDFS_PREFIX", "rdfs");
define("RDFS_SUBCLASSOF","subClassOf");
define("RDFS_SUBPROPERTYOF","subPropertyOf");
define("RDFS_RANGE","range");
define("RDFS_DOMAIN","domain");
define("RDFS_CLASS","Class");
define("RDFS_RESOURCE","Resource");
define("RDFS_DATATYPE","Datatype");
define("RDFS_LITERAL","Literal");
define("RDFS_LABEL","label");
define("RDFS_COMMENT","comment");

// -----------------------------------------------------------------------------
// OWL
// -----------------------------------------------------------------------------

define("OWL_URI","http://www.w3.org/2002/07/owl#" );
define("OWL_PREFIX", "owl");
define("OWL_SAME_AS","sameAs");
define("OWL_INVERSE_OF","inverseOf");

?>