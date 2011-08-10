<?php

require_once "settings.php";

/**
 * -----------------------------------------------------------------------------
 * ERP API Test
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        ResourceTest.php
 * @version     2011-08-10
 * @package     tests
 * @access      public
 * 
 * Description  Testing the class Resource
 * 
 * -----------------------------------------------------------------------------
 */
class ResourceTest extends PHPUnit_Framework_TestCase {

    private $resource;

    protected function setUp() {
        $this->resource = new Resource(NS, "R1");
    }

    function testGenerateResource() {

        $res = new Resource(NS . "R2");
        $this->assertTrue(is_a($res, Resource));
    }

    public function testGetUri() {

        echo $this->resource->getUri();

        $this->assertTrue($this->resource->getUri() == NS . "R1");
    }

    public function testAddProperty() {

        $predicate = new Resource(NS . "arc");
        $object = new LiteralNode("test", BOOL);
        $statement1 = new Statement($this->resource, $predicate, $object);

        $this->resource->addProperty($predicate, $object);

        $this->assertTrue($statement1->getSubject()->equals($this->resource));
        $this->assertTrue($this->resource->getProperty($predicate) != null);
    }

    public function testGetProperty() {

        $predicate = new Resource(NS . "arc");
        $object = new LiteralNode("test");

        $this->resource->addProperty($predicate, $object);

        $this->assertTrue($this->resource->getProperty($predicate) == $object);
    }

    public function testRemoveProperty() {

        $predicate = new Resource(NS . "arc");
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
