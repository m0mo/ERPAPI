<?php

require_once 'PHPUnit/Autoload.php';
require_once "../API.php";

/**
 * -----------------------------------------------------------------------------
 * ERP API Test
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        BlankNodeTest.php
 * @version     2011-08-09
 * @package     tests
 * @access      public
 * 
 * Description  here
 * 
 * -----------------------------------------------------------------------------
 */
class BlankNodeTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        
    }
    
    public function testGenerateBlankNode() {
        $blank = new BlankNode("http://example.org/", "bnode1");
        $this->assertTrue(is_a($blank, BlankNode));
        $this->assertTrue($blank->getId() == "bnode1");
        $this->assertTrue($blank->getName() == "bnode1");
        $this->assertTrue($blank->getUri() == "http://example.org/bnode1");
        
    }

    // Add your tests here

    protected function tearDown() {
        
    }

}

?>
