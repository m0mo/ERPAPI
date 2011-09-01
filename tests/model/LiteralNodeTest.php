<?php

require_once "settings.php";

/**
 * --------------------------------------------------------------------
 * ERP API Test
 * --------------------------------------------------------------------
 *
 * Testing the LiteralNode class
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * @name        LiteralTest.php
 * @version     2011-08-06
 * @package     tests/model
 * @access      public
 * 
 * --------------------------------------------------------------------
 */
class LiteralNodeTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        
    }
    
    public function testGenerateLiteral() {
        
        $l = new LiteralNode("Test");
        $this->assertTrue(is_a($l, "LiteralNode"));
        
    }
    
    public function testLiteral() {
        $l = new LiteralNode("Test");
        $this->assertTrue($l->getLiteral()!=null);
        $this->assertEquals($l->getLiteral(), "Test");
        $this->assertEquals($l->getDatatype(), "string");
        
        $l = new LiteralNode("Test", DATE);
        $this->assertEquals($l->getLiteral(), "Test");
        $this->assertEquals($l->getDatatype(), "date");
        
    }
    
    public function testGetLanguage() {
        
        $l = new LiteralNode("Test", STRING, "de");
        $this->assertEquals($l->getLanguage(), "de");
        
    }
    
    public function testEquals() {
        
        $l = new LiteralNode("Test", STRING, "de");
        
        $this->assertTrue($l->equals($l));
        $this->assertFalse($l->equals(new LiteralNode("Test", STRING, "en")));
        
        $this->assertTrue($l->equals(new LiteralNode("Test", STRING, "de")));
        
    }
    

    protected function tearDown() {
        
    }

}

?>
