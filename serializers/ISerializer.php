<?php

/**
 * --------------------------------------------------------------------
 * ERP API 
 * --------------------------------------------------------------------
 *
 * Interface for the serializers
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * @name        ISerializer.php
 * @version     2011-08-10
 * @package     serializers
 * @access      public
 * 
 * --------------------------------------------------------------------
 */
interface ISerializer {
    
    public function serializeToString($model);
    public function serialize($file, $model);
    
}

?>
