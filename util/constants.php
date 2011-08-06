<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner@gmail.com> 
 * 
 * @name        constants.php
 * @version     Aug 5, 2011
 * @package     util
 * @access      public
 * 
 * Description  Contains a list of definitions used by the API
 * 
 * -----------------------------------------------------------------------------
 */
    
    define("API_ERROR", "ERP Error: ");
    define(ERP_ERROR_SUBJECT, API_ERROR."subject has to be a resource!");
    define(ERP_ERROR_PREDICATE, API_ERROR."predicate has to be a resource and can't be a blank node!");
    define(ERP_ERROR_OBJECT, API_ERROR."object has to be a resource or a literal!");
    
    
    define("STRING", "string");
    define("BOOL", "boolean");
    define("INTEGER", "integer");
    
    define("INCLUDE_DIR", "../");
    

?>
