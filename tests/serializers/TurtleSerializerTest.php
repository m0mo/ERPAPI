<?php

require_once 'settings.php';

/**
 * -----------------------------------------------------------------------------
 * ERP API Test
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        TurtleSerializerTest.php
 * @version     2011-08-11
 * @package     tests
 * @access      public
 * 
 * Description  Tests the Turtle serializer
 * 
 * -----------------------------------------------------------------------------
 */
class TurtleSerializerTest extends PHPUnit_Framework_TestCase {

    private $model;
    private $filename = "test.turtle";

    protected function setUp() {

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

        $ser = new TurtleSerializer();
        $ser->serialize($this->filename, $this->model);

        $this->assertTrue(file_exists($this->filename));
    }

    public function testSerializeSuccess2() {

        $this->assertFalse(file_exists($this->filename));

        $this->model->save($this->filename, "turtle");

        $this->assertTrue(file_exists($this->filename));
    }

    public function testSerializeStringSuccess() {

        $ser = new TurtleSerializer();
        $this->assertTrue(Check::isString($ser->serializeToString($this->model)));
    }

    /**
     * @expectedException APIException
     */
    public function testSerializeError1() {

        $this->assertFalse(file_exists($this->filename));

        $ser = new TurtleSerializer();
        $ser->serialize($this->filename, new Model());
    }

    protected function tearDown() {

        if (file_exists($this->filename)) {
            // yes the file does exist 
            unlink($this->filename);
        }
    }

}

?>
