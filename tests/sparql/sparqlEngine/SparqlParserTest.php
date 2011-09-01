<?php

require_once 'settings.php';

/**
 * -----------------------------------------------------------------------------
 * ERP API Test
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        SparqlParserTest.php
 * @version     2011-09-01
 * @package     tests
 * @access      public
 * 
 * Description  testing the Sparql Parser
 * 
 * -----------------------------------------------------------------------------
 */
class SparqlParserTest extends PHPUnit_Framework_TestCase {
    
    private $queryString;

    protected function setUp() {
        
        $query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/> ";
        $query.= "PREFIX foaf2: <http://xmlns.com/foaf/0.2/> ";
        $query.= "SELECT ?x ?y WHERE ";
        $query.= "{ ";
        $query.= "?x foaf:knows ?y . ";
        $query.= "?x foaf:name ?nameX . ";
        $query.= "?y foaf:name ?nameY ";
        $query.= "}";
        
        $this->queryString = $query;
    }

    public function testParse() {
        
        $parser = new SparqlParser();
        $object = $parser->parse($this->queryString);
        
//        print_r($object);
        
        $this->assertTrue($object instanceof SparqlQuery);
        
    }
    
    /**
     * @expectedException SparqlException
     */
    public function testParseError1() {
        $query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/> ";
        $query.= "PREFIX foaf2: <http://xmlns.com/foaf/0.2/> ";
        $query.= "DESCRIBE ?x ?y WHERE ";
        $query.= "{ ";
        $query.= "?x foaf:knows ?y . ";
        $query.= "}";
        
        $parser = new SparqlParser();
        $object = $parser->parse($query);
        
    }
    
    /**
     * @expectedException SparqlException
     */
    public function testParseError2() {
        $query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/> ";
        $query.= "BLA ?x ?y WHERE ";
        $query.= "{ ";
        $query.= "?x foaf:knows ?y . ";
        $query.= "}";
        
        $parser = new SparqlParser();
        $object = $parser->parse($query);
    }
    
    /**
     * @expectedException SparqlException
     */
        public function testParseError3() {
        $query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/> ";
        $query.= "CONSTRUCT ?x ?y WHERE ";
        $query.= "{ ";
        $query.= "?x foaf:knows ?y . ";
        $query.= "}";
        
        $parser = new SparqlParser();
        $object = $parser->parse($query);
    }
    
    /**
     * @expectedException SparqlException
     */
            public function testParseError4() {
        $query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/ ";
        $query.= "SELECT ?x ?y WHERE ";
        $query.= "{ ";
        $query.= "?x foaf:knows ?y . ";
        $query.= "}";
        
        $parser = new SparqlParser();
        $object = $parser->parse($query);
    }

    
    /**
     * @expectedException SparqlException
     */
            public function testParseError5() {
        $query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/ ";
        $query.= "SELECT ? WHERE ";
        $query.= "{ ";
        $query.= "?x foaf:knows ?y . ";
        $query.= "}";
        
        $parser = new SparqlParser();
        $object = $parser->parse($query);
    }

    protected function tearDown() {
        
    }

}

?>
