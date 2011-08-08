<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        constants.php
 * @version     0.1.5 (Aug 6, 2011)
 * @package     util
 * @access      public
 * 
 * Description  Contains a list of definitions used by the API
 * 
 * -----------------------------------------------------------------------------
 */

// ERRORS
define("API_ERROR", "ERP Error: ");
define(ERP_ERROR_SUBJECT, API_ERROR . "Subject has to be a resource!");
define(ERP_ERROR_PREDICATE, API_ERROR . "Predicate has to be a resource and can't be a blank node!");
define(ERP_ERROR_OBJECT, API_ERROR . "Object has to be a resource or a literal!");
define(API_ERROR_STATEMENT, API_ERROR . "Parameter is not a statement");
define(API_ERROR_URI, API_ERROR . "Parameter is not a valid URI");


define("STRING", "string");
define("BOOL", "boolean");
define("INTEGER", "integer");

define("INCLUDE_DIR", "../");
?>
