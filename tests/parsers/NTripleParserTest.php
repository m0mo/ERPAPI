<?php

require_once 'settings.php';

/**
 * --------------------------------------------------------------------
 * ERP API Test
 * --------------------------------------------------------------------
 *
 * Testing the NTripleParser class
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * @name        NTripleParserTest.php
 * @version     2011-08-12  
 * @package     tests/parsers
 * @access      public
 * 
 * --------------------------------------------------------------------
 */
class NTripleParserTest extends PHPUnit_Framework_TestCase {

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
        $model->save($this->filename, "nt");

        $model = null;
    }

    public function testNTParserSuccess1() {

        $this->assertTrue(file_exists($this->filename));
        
        $model = new Model();
        
        $parser = new NTripleParser();
        $parser->parse($this->filename, $model);

        $this->assertFalse($model->isEmpty());
        $this->assertEquals($model->size(), "7");
    }
    
        public function testNTParserSuccess2() {

        $this->assertTrue(file_exists($this->filename));
        
        $model = new Model();
        
        $model->load($this->filename, "nt");

        $this->assertFalse($model->isEmpty());
        $this->assertEquals($model->size(), "7");
    }

    
    /**
     * @expectedException APIException
     */
    public function testNTParserError1() {

        $this->assertFalse(file_exists($this->filename . "2"));
        $parser = new NTripleParser();
        $model = $parser->parse($this->filename . "2", new Model());
    }

    /**
     * @expectedException APIException
     */
    public function testNTParserError2() {
        $parser = new NTripleParser();
        $model = $parser->parse(null, new Model());
    }

    /**
     * @expectedException APIException
     */
    public function testNTParserError3() {

        $ourFileHandle = fopen($this->filename, 'w');
        fclose($ourFileHandle);

        $parser = new NTripleParser();
        $model = $parser->parse($this->filename, new Model());
    }

    
    /**
     * @expectedException APIException
     */
    public function testRDFXMLParserError4() {

        $content = "<http://example.org/test> <http://example.org/pred> \"deutsch\"@de^^<string> .\n";
        $content.= "<http://example.org/test> <http://example.org/pred2> \"englisch\"@en^^<string> .\n";
        $content.= "<http://example.org/test> <http://example.org/pred3> \n";

        $ourFileHandle = fopen($this->filename, 'w');
        fwrite($ourFileHandle, $content);
        fclose($ourFileHandle);

        $parser = new NTripleParser();
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
