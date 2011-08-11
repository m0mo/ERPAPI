<?php

require_once 'settings.php';

/**
 * -----------------------------------------------------------------------------
 * ERP API Test
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        RDFXMLParserTest.php
 * @version     2011-08-11  
 * @package     tests
 * @access      public
 * 
 * Description  Tests the RDF/XML parser
 * 
 * -----------------------------------------------------------------------------
 */
class RDFXMLParserTest extends PHPUnit_Framework_TestCase {

    private $filename = "test2.rdf";

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
        $model->save($this->filename);

        $model = null;
    }

    public function testRDFXMLParserSuccess1() {

        $this->assertTrue(file_exists($this->filename));
        $parser = new RDFXMLParser();
        $model1 = $parser->parse($this->filename);

        $this->assertFalse($model1->isEmpty());
        $this->assertTrue($model1->hasNamespace(PREFIX));
        $this->assertEquals($model1->size(), "7");
    }

    public function testRDFXMLParserSuccess2() {

        $this->assertTrue(file_exists($this->filename));
        $parser = new RDFXMLParser();
        $model = new Model();
        $model1 = $model->load($this->filename, "rdf", false);

        $this->assertFalse($model1->isEmpty());
        $this->assertTrue($model1->hasNamespace(PREFIX));
        $this->assertEquals($model1->size(), "7");

        // original model is not overwritten
        $this->assertTrue($model->isEmpty());
        $this->assertFalse($model->hasNamespace(PREFIX));
        $this->assertEquals($model->size(), "0");
    }

    public function testRDFXMLParserSuccess3() {

        $this->assertTrue(file_exists($this->filename));
        $parser = new RDFXMLParser();
        $model = new Model();
        $model->load($this->filename);

        // data is loaded into model
        $this->assertFalse($model->isEmpty());
        $this->assertTrue($model->hasNamespace(PREFIX));
        $this->assertEquals($model->size(), "7");
    }

    /**
     * @expectedException APIException
     */
    public function testRDFXMLParserError1() {

        $this->assertFalse(file_exists($this->filename . "2"));
        $parser = new RDFXMLParser();
        $model = $parser->parse($this->filename . "2");
    }

    /**
     * @expectedException APIException
     */
    public function testRDFXMLParserError2() {
        $parser = new RDFXMLParser();
        $model = $parser->parse(null);
    }

    /**
     * @expectedException APIException
     */
    public function testRDFXMLParserError3() {

        $ourFileHandle = fopen($this->filename, 'w');
        fclose($ourFileHandle);

        $parser = new RDFXMLParser();
        $model = $parser->parse($this->filename);
    }

    /**
     * @expectedException APIException
     */
    public function testRDFXMLParserError4() {

        $content = "<?xml version='1.0'?><rdf:RDF xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#' xmlns:ex='http://example.org/'></rdf:RDF>";

        $ourFileHandle = fopen($this->filename, 'w');
        fwrite($ourFileHandle, $content);
        fclose($ourFileHandle);

        $parser = new RDFXMLParser();
        $model = $parser->parse($this->filename);
    }

    /**
     * @expectedException APIException
     */
    public function testRDFXMLParserError5() {
        $model = new Model();
        $model = $model->load($this->filename, "unknownFiletype");
    }

    protected function tearDown() {
        
        if (file_exists($this->filename)) {
            // yes the file does exist 
            unlink($this->filename);
        }
        
    }

}

?>
