<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        BlankNode.php
 * @version     0.1.0 (Aug 5, 2011)
 * @package     model
 * @access      public
 * 
 * Description  here
 * 
 * -----------------------------------------------------------------------------
 */
class BlankNode extends Resource {
    
    function __construct($uri = null) {
        $this->uri = $uri;
    }

    
}

?>
