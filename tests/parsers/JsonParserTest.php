<?php

require_once 'settings.php';

/**
 * --------------------------------------------------------------------
 * ERP API Test
 * --------------------------------------------------------------------
 *
 * Testing the JasonParser class
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * @name        JsonParserTest.php
 * @version     2011-08-12  
 * @package     tests/parsers
 * @access      public
 * 
 * --------------------------------------------------------------------
 */
class JsonParserTest extends PHPUnit_Framework_TestCase {

    private $filename = "test2.json";

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
        $model->save($this->filename, "json");
        
//        echo $model->toString("turtle");

        $model = null;
    }

    public function testNTParserSuccess1() {

        $this->assertTrue(file_exists($this->filename));
        
        $model = new Model();
        
        $parser = new JsonParser();
        $parser->parse($this->filename, $model);

        $this->assertFalse($model->isEmpty());
        $this->assertEquals($model->size(), "7");
        
//        echo $model->toString("turtle");
    }
    
    public function testNTParserSuccess2() {

        $this->assertTrue(file_exists($this->filename));
        
        $model = new Model();
        
        $model->load($this->filename, "json");

        $this->assertFalse($model->isEmpty());
        $this->assertEquals($model->size(), "7");
    }

    
    /**
     * @expectedException APIException
     */
    public function testNTParserError1() {

        $this->assertFalse(file_exists($this->filename . "2"));
        $parser = new JsonParser();
        $model = $parser->parse($this->filename . "2", new Model());
    }

    /**
     * @expectedException APIException
     */
    public function testNTParserError2() {
        $parser = new JsonParser();
        $model = $parser->parse(null, new Model());
    }

    /**
     * @expectedException APIException
     */
    public function testNTParserError3() {

        $ourFileHandle = fopen($this->filename, 'w');
        fclose($ourFileHandle);

        $parser = new JsonParser();
        $model = $parser->parse($this->filename, new Model());
    }

    
    /**
     * @expectedException APIException
     */
    public function testRDFXMLParserError4() {

        $content = "bla";

        $ourFileHandle = fopen($this->filename, 'w');
        fwrite($ourFileHandle, $content);
        fclose($ourFileHandle);

        $parser = new JsonParser();
        $model = $parser->parse($this->filename, new Model());
    }
    
    /**
     * @expectedException APIException
     */
    public function testRDFXMLParserError5() {

        $content = null;
        
        $ourFileHandle = fopen($this->filename, 'w');
        fwrite($ourFileHandle, $content);
        fclose($ourFileHandle);

        $parser = new JsonParser();
        $model = $parser->parse($this->filename, new Model());
    }


    protected function tearDown() {
        
        if (file_exists($this->filename)) {
            // yes the file does exist 
            unlink($this->filename);
        }
        
    }

}

?>
