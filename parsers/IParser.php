<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        IParser.php
 * @version     2011-08-10
 * @package     parsers
 * @access      public
 * 
 * Description  Interface for parsers
 * 
 * -----------------------------------------------------------------------------
 */
interface IParser {
    
    public function parse($file);
    
}

?>
