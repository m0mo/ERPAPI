<?php

require_once 'settings.php';

/**
 * --------------------------------------------------------------------
 * ERP API Test
 * --------------------------------------------------------------------
 *
 * Testing the SparqlQuery class
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        SparqlQueryTest.php
 * @version     2011-09-01
 * @package     tests/sparql/sparqlEngine
 * @access      public
 * 
 * --------------------------------------------------------------------
 */
class SparqlQueryTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        
    }

    public function testSuccess() {

        $query = new SparqlQuery();


        $query->addNamespace(PREFIX, NS);
        $query->setQueryString("SELECT ?x WHERE { ?x ?y ?z }");
        $query->addResultVariable("?x");
        $query->addWhereTriple("_:id1", "?y", "\"literal\"@en^^xsd:string");
        $query->setResultForm("SELECT");

        $this->assertTrue(is_array($query->getNamespaces()));
        $this->assertTrue(is_string($query->getResultForm()));
        $this->assertTrue(is_string($query->getQueryString()));
        $this->assertTrue(is_array($query->getResultVariables()));
        $this->assertTrue(is_array($query->getWhereTriples()));
    }

    /**
     * @expectedException SparqlException
     */
    public function testError1() {

        $query = new SparqlQuery();

        $query->addNamespace("#", NS);
    }

    /**
     * @expectedException SparqlException
     */
    public function testError2() {

        $query = new SparqlQuery();

        $query->addNamespace(PREFIX, "bla");
    }

    /**
     * @expectedException SparqlException
     */
    public function testError3() {

        $query = new SparqlQuery();

        $query->addResultVariable("?x+");
    }

    /**
     * @expectedException SparqlException
     */
    public function testError4() {

        $query = new SparqlQuery();

        $query->addResultVariable("?");
    }

    /**
     * @expectedException SparqlException
     */
    public function testError5() {

        $query = new SparqlQuery();

        $query->addResultVariable("x");
    }

    /**
     * @expectedException SparqlException
     */
    public function testError6() {

        $query = new SparqlQuery();
        $query->addWhereTriple("?x", "bla:y", "?z");
    }
    
        /**
     * @expectedException SparqlException
     */
    public function testError7() {

        $query = new SparqlQuery();
        $query->addWhereTriple("?x", "y", "?z");
    }
    
        
        /**
     * @expectedException SparqlException
     */
    public function testError8() {

        $query = new SparqlQuery();
        $query->setResultForm("?z");
    }

    protected function tearDown() {
        
    }

}

?>
