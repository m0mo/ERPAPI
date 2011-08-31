<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        BlankNode.php
 * @version     2011-08-31
 * @package     model
 * @access      public
 * 
 * Description  This class represents a RDF Blank Node
 * 
 * -----------------------------------------------------------------------------
 */
class BlankNode extends Resource {
    
    /**
     * 
     * The resource represented by a blank node is also called an anonymous 
     * resource. By RDF standard a blank node can only be used as subject or 
     * object in an RDF triple
     *
     * @param string $uri
     * @param string $notused is currently not used
     */
    function __construct($id, $notused = null) {
        
        // if $name is not a string $namespace_or_uri has to be an uri
        if (!Check::isString($id));
        
        $this->uri = $id;
        $this->name = $id;
        
    }
    
    /**
     * Returns the ID of the BlankNode
     *
     * @return string 
     */
    public function getId() {
        return $this->uri;
    }
    

    
}

?>
