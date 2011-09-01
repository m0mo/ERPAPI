<?php

/**
 * --------------------------------------------------------------------
 * ERP API 
 * --------------------------------------------------------------------
 *
 * Interface for Engines
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * @name        ISparqlEngine.php
 * @version     2011-08-23
 * @package     sparql
 * @access      public
 * 
 * --------------------------------------------------------------------
 */
Interface ISparqlEngine {
    
    public function query($query, $model);
    
}

?>
