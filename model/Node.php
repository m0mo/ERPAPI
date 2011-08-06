<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        Node.php
 * @version     0.1.0 (Aug 5, 2011)
 * @package     model
 * @access      public
 * 
 * Description  This abstract class represents an RDF Node
 * 
 * -----------------------------------------------------------------------------
 */
abstract class Node {

    public function toString() {

        $variables = get_object_vars($this);

        foreach ($variables as $key => $value)
            $vars .= $key . "='" . $value . "'; ";

        return "Instance of " . get_class($this) . "; Properties: " . $vars;
    }

    /**
     * Checks if two Nodes are the same
     *
     * @param Node $that
     * @return true if equal, else false 
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
