<?php

/**
 * --------------------------------------------------------------------
 * ERP API 
 * --------------------------------------------------------------------
 *
 * Interface for parsers
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * @name        IParser.php
 * @version     2011-08-10
 * @package     parsers
 * @access      public
 * 
 * --------------------------------------------------------------------
 */
interface IParser {
    
    public function parse($file, &$model);
    
}

?>
