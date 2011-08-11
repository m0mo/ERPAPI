<?php

require_once 'settings.php';

/**
 * -----------------------------------------------------------------------------
 * ERP API Test
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        NTripleSerializerTest.php
 * @version     2011-08-11
 * @package     tests
 * @access      public
 * 
 * Description  tests the nt serializer
 * 
 * -----------------------------------------------------------------------------
 */
class NTripleSerializerTest extends PHPUnit_Framework_TestCase {

    private $model;
    private $filename = "test.nt";

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

        $ser = new NTripleSerializer();
        $ser->serialize($this->filename, $this->model);

        $this->assertTrue(file_exists($this->filename));
    }

    public function testSerializeSuccess2() {

        $this->assertFalse(file_exists($this->filename));

        $this->model->save($this->filename, "nt");

        $this->assertTrue(file_exists($this->filename));
    }

    public function testSerializeStringSuccess() {

        $ser = new NTripleSerializer();
        $this->assertTrue(Check::isString($ser->serializeToString($this->model)));
    }

    /**
     * @expectedException APIException
     */
    public function testSerializeError1() {

        $this->assertFalse(file_exists($this->filename));

        $ser = new NTripleSerializer();
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
