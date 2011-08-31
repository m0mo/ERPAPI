<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        Node.php
 * @version     2011-08-10
 * @package     model
 * @access      public
 * 
 * Description  This abstract class represents an RDF Node
 * 
 * -----------------------------------------------------------------------------
 */
abstract class Node {

    /**
     * prints the propertis of the node
     *
     * @return string 
     */
    public function toString() {

        $variables = get_object_vars($this);
        
        $vars = null;

        foreach ($variables as $key => $value)
            $vars .= $key . "='" . $value . "'; ";

        return "Instance of " . get_class($this) . "; Properties: " . $vars;
    }

    /**
     * Checks if two Nodes are the same
     *
     * @param Node $that
     * @return bool true if equal, else false 
     */
    public function equals($that) {

        if ($this == $that) {
            return true;
        }
        if (($that == NULL) or !(is_a($that, get_class($this)))) {
            return false;
        }

        return false;
    }
}

?>
