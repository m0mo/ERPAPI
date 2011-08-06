<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner@gmail.com> 
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

    public function equals($that) {

        if ($this == $that) {
            return true;
        }
        if (($that == NULL) or !(is_a($that, get_class($this)))) {
            return false;
        }

        if ($this->getURI() == $that->getURI()) {
            return true;
        }

        return false;
    }
}

?>
