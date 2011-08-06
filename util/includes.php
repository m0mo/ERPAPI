<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        includes.php
 * @version     0.1.5 (Aug 6, 2011)
 * @package     util
 * @access      public
 * 
 * Description  Handels all necessary includes so the user does not have to 
 *              care.
 * 
 * -----------------------------------------------------------------------------
 */

// Utilities
include_once "config.php";
include_once "constants.php";
include_once "APIException.php";
include_once "Check.php";

// Parsers

// Serializers

// Model
include_once INCLUDE_DIR."model/Model.php";
include_once INCLUDE_DIR."model/Node.php";
include_once INCLUDE_DIR."model/Resource.php";
include_once INCLUDE_DIR."model/BlankNode.php";
include_once INCLUDE_DIR."model/LiteralNode.php";
include_once INCLUDE_DIR."model/Statement.php";

?>
