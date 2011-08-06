<?php

require_once 'PHPUnit/Autoload.php';
require_once "../API.php"; 

class ModelTest extends PHPUnit_Framework_TestCase
{
    
    private $model;
    
    protected function setUp() {
        $this->model = new Model();
    }

    public function testAddNamespace()
    {
        
        $this->model->addNamespace("ns", "http://test/");
        $this->assertEquals($this->model->getNamespace("ns"), "http://test/");
    }
    
    public function testDeleteNamespace() {
        
        $this->model->addNamespace("ns", "1234");
        $this->assertTrue($this->model->removeNamespace("ns"));
    }
    
    public function testGetNamespace() {
        $this->model->addNamespace("ns", "1234");
        $this->assertTrue($this->model->getNamespace("ns") == "1234");
    }
    
    /**
     * @expectedException APIException
     */
    public function testAddStatementError() {
        $this->model->add("bla");
    }
    
    protected function tearDown() {
        
    }
}
?>