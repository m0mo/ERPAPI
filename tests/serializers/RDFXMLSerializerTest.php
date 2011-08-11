<?php

require_once 'settings.php';

/**
 * -----------------------------------------------------------------------------
 * ERP API Test
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        RDFXMLSerializerTest.php
 * @version     Aug 10, 2011
 * @package     tests
 * @access      public
 * 
 * Description  here
 * 
 * -----------------------------------------------------------------------------
 */
class RDFXMLSerializerTest extends PHPUnit_Framework_TestCase {

    private $model;
    private $filename = "test.rdf";

    protected function setUp() {

        if (file_exists($this->filename)) {
            // yes the file does exist 
            unlink($this->filename);
        }

        $model = new Model();
        $model->addBaseNamespace(PREFIX, NS);

        $res = $model->newResource("test")->addProperty($model->newResource("pred"), new LiteralNode("deutsch", STRING, "de"))
                ->addProperty($model->newResource("pred2"), new LiteralNode("englisch", STRING, "en"))
                ->addProperty($model->newResource("pred3"), $model->newResource("test2")
                ->addProperty($model->newResource("pred"), new LiteralNode("literal"))
                ->addProperty($model->newResource("pred2"), $model->newBlankNode()
                        ->addProperty($model->newResource("pred"), new LiteralNode("literal"))
                        ->addProperty($model->newResource("pred2"), new BlankNode("id"))
                )
        );
        $model->add($res);

        $this->model = $model;
    }

    public function testSerializeSuccess1() {

        $this->assertFalse(file_exists($this->filename));

        $ser = new RDFXMLSerializer();
        $ser->serialize($this->filename, $this->model);

        $this->assertTrue(file_exists($this->filename));
    }

     public function testSerializeSuccess2() {

        $this->assertFalse(file_exists($this->filename));

        $this->assertTrue($this->model->save($this->filename));

        $this->assertTrue(file_exists($this->filename));
    }
    
    /**
     * @expectedException APIException
     */
    public function testSerializeError1() {

        $this->assertFalse(file_exists($this->filename));

        $ser = new RDFXMLSerializer();
        $ser->serialize(null, $this->model);
    }

    /**
     * @expectedException APIException
     */
    public function testSerializeError2() {

        $this->assertFalse(file_exists($this->filename));

        $ser = new RDFXMLSerializer();
        $ser->serialize($this->filename, null);
    }

    /**
     * @expectedException APIException
     */
    public function testSerializeError3() {

        $this->assertFalse(file_exists($this->filename));

        $ser = new RDFXMLSerializer();
        $ser->serialize($this->filename, new Model());
    }
    
    /**
     * @expectedException APIException
     */
    public function testSerializeError4() {

        $this->model->save($this->filename, "xml");
    }

    public function testSerializeToStringSuccess() {
        $ser = new RDFXMLSerializer();
        $this->assertTrue(Check::isString($ser->serializeToString($this->model)));
    }

    protected function tearDown() {

        if (file_exists($this->filename)) {
            // yes the file does exist 
            unlink($this->filename);
        }
    }

}

?>
