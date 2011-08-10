<?php

require_once 'settings.php';

/**
 * -----------------------------------------------------------------------------
 * ERP API Test
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        UtilsTest.php
 * @version     2011-08-10  
 * @package     tests
 * @access      public
 * 
 * Description  Checks the staic functions of the Utils class
 * 
 * -----------------------------------------------------------------------------
 */
class UtilsTest extends PHPUnit_Framework_TestCase {
    
    public function testGetterSuccess() {     
        $this->assertTrue(Utils::getName(NS."test") == "test");
        $this->assertTrue(Utils::getNamespace(NS."test") == NS);
        $this->assertTrue(Utils::getNamespaceEnd(NS."test") == strlen(NS));
        $this->assertTrue(Utils::getName("test") == "test");
    }
    
    /**
     * @expectedException APIException
     */
    public function testGetNamespaceError1() {
        $this->assertTrue(Utils::getName(null) == "");
    }
    
    
    public function testGetNamespaceError2() {
        $this->assertTrue(Utils::getNamespaceEnd("test") == 0);
        $this->assertTrue(Utils::getNamespace("test") == "");
    }

}

?>
