<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        BlankNode.php
 * @version     2011-08-09
 * @package     model
 * @access      public
 * 
 * Description  This class represents a RDF Blank Node
 * 
 * -----------------------------------------------------------------------------
 */
class BlankNode extends Resource {
    
    /**
     * The id of the node
     *
     * @var string 
     */
    private $id;


    /**
     * 
     * The resource represented by a blank node is also called an anonymous 
     * resource. By RDF standard a blank node can only be used as subject or 
     * object in an RDF triple
     *
     * @param string $uri
     * @param string $id
     */
    function __construct($namespace_or_uri, $id = "") {
        
        parent::__construct($namespace_or_uri, $id);
        $this->id = $this->name;
        
    }
    
    /**
     * Returns the ID of the BlankNode
     *
     * @return string 
     */
    public function getId() {
        return $this->id;
    }
    

    
}

?>
