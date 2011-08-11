<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        ISerializer.php
 * @version     2011-08-10
 * @package     serializers
 * @access      public
 * 
 * Description  Interface for the serializers
 * 
 * -----------------------------------------------------------------------------
 */
interface ISerializer {
    
    public function serializeToString($model);
    public function serialize($file, $model);
    
}

?>
