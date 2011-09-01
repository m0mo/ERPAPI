<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        ISparqlEngine.php
 * @version     2011-08-23
 * @package     sparql
 * @access      public
 * 
 * Description  Interface for Engines
 * 
 * -----------------------------------------------------------------------------
 */
Interface ISparqlEngine {
    
    public function query($query, $model);
    
}

?>
