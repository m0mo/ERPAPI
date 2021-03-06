<?php

require_once "settings.php";

/**
 * --------------------------------------------------------------------
 * ERP API Test
 * --------------------------------------------------------------------
 *
 * Testing the BlankNode class
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * @name        BlankNodeTest.php
 * @version     2011-08-09
 * @package     tests/model
 * @access      public
 * 
 * --------------------------------------------------------------------
 */
class BlankNodeTest extends PHPUnit_Framework_TestCase {

    public function testGenerateBlankNode() {
        $blank = new BlankNode("bnode1");
        $this->assertTrue(Check::isBlankNode($blank));
        $this->assertEquals($blank->getId(), "bnode1");
        $this->assertEquals($blank->getName(), "bnode1");
        $this->assertEquals($blank->getUri(), "bnode1");
    }

    public function testToString() {
        $blank = new BlankNode("bnode1");
        $this->assertTrue(Check::isString($blank->toString()));
    }
    
    public function testEquals() {
        $m = new Model();
        
        $blank1 = new BlankNode(BNODE."1");
        $blank2 = $m->newBlankNode();
        
        $this->assertEquals($blank1->getId(), $blank2->getId());
        $this->assertTrue($blank1->equals($blank2));
        
    }
    

}

?>
