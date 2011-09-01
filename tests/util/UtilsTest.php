<?php

require_once "settings.php";

/**
 * --------------------------------------------------------------------
 * ERP API Test
 * --------------------------------------------------------------------
 *
 * Testing the Utils class
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * @name        UtilsTest.php
 * @version     2011-08-10  
 * @package     tests
 * @access      public
 * 
 * --------------------------------------------------------------------
 */
class UtilsTest extends PHPUnit_Framework_TestCase {
    
    public function testGetterSuccess() {     
        $this->assertEquals(Utils::getName(NS."test"), "test");
        $this->assertEquals(Utils::getNamespace(NS."test"), NS);
        $this->assertEquals(Utils::getNamespaceEnd(NS."test"), strlen(NS));
        $this->assertEquals(Utils::getName("test"), "test");
    }
    
    /**
     * @expectedException APIException
     */
    public function testGetNamespaceError1() {
        $this->assertTrue(Utils::getName(null) == "");
    }
    
    /**
     * @expectedException APIException
     */
    public function testGetNamespaceError2() {
        Utils::getNamespaceEnd("test");
    }
    
        /**
     * @expectedException APIException
     */
    public function testGetNamespaceError3() {
        Utils::getNamespace("test");
    }

}

?>
