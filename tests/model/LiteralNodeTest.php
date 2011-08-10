<?php

require_once "settings.php";

/**
 * -----------------------------------------------------------------------------
 * ERP API Test
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        LiteralTest.php
 * @version     2011-08-06
 * @package     tests
 * @access      public
 * 
 * Description  here
 * 
 * -----------------------------------------------------------------------------
 */
class LiteralNodeTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        
    }
    
    public function testGenerateLiteral() {
        
        $l = new LiteralNode("Test");
        $this->assertTrue(is_a($l, LiteralNode));
        
    }
    
    public function testLiteral() {
        $l = new LiteralNode("Test");
        $this->assertTrue($l->getLiteral()!=null);
        $this->assertEquals($l->getLiteral(), "Test");
        $this->assertEquals($l->getDatatype(), "string");
        
    }

    protected function tearDown() {
        
    }

}

?>
