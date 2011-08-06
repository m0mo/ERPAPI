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
 * @name        ResourceTest.php
 * @version     0.1.5 (Aug 6, 2011)
 * @package     tests
 * @access      public
 * 
 * Description  here
 * 
 * -----------------------------------------------------------------------------
 */
class ResourceTest extends PHPUnit_Framework_TestCase {

    private $resource;
    private $ns = "http://thisIsMyUri/test/";
    
    protected function setUp() {
        $this->resource = new Resource($this->ns."R1");
    }

    function testGenerateResource() {
        
        $res = new Resource($this->ns."R2");
        $this->assertTrue(is_a($res, Resource));
    }
    
    public function testGetUri() {
        $this->assertTrue($this->resource->getUri() == $this->ns."R1");
    }
    
    public function testAddProperty() {
 
        $predicate = new Resource($this->ns."arc");
        $object = new LiteralNode("test", BOOL);
        $statement1 = new Statement($this->resource, $predicate, $object);
        
        $statement2 = $this->resource->addProperty($predicate, $object);
        
        $this->assertTrue($statement1->equals($statement2));
        $this->assertTrue($this->resource->getProperty($predicate) != null);
        
    }
    
    public function testGetProperty() {
        
        $predicate = new Resource($this->ns."arc");
        $object = new LiteralNode("test");
       
        $this->resource->addProperty($predicate, $object);
        
        $this->assertTrue($this->resource->getProperty($predicate) == $object);
        
    }
    
    public function testRemoveProperty() {
        
        $predicate = new Resource($this->ns."arc");
        $object = new LiteralNode("test");
       
        $this->resource->addProperty($predicate, $object);
        $this->assertTrue($this->resource->getProperty($predicate) == $object);
        $this->assertTrue($this->resource->removeProperty($predicate));
        $this->assertFalse($this->resource->getProperty($predicate) == $object);
        
    }

    protected function tearDown() {
        
    }

}

?>
