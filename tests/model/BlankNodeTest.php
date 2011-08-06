<?php

require_once 'PHPUnit/Autoload.php';
require_once "../API.php";

/**
 * -----------------------------------------------------------------------------
 * ERP API Test
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner@gmail.com> 
 * 
 * @name        BlankNodeTest.php
 * @version     Aug 5, 2011
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
        $blank = new BlankNode();
        $this->assertTrue(is_a($blank, BlankNode));
        
    }

    // Add your tests here

    protected function tearDown() {
        
    }

}

?>
