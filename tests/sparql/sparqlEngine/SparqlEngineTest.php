<?php

require_once 'settings.php';

/**
 * -----------------------------------------------------------------------------
 * ERP API Test
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        SparqlEngineTest.php
 * @version     2011-08-31
 * @package     tests
 * @access      public
 * 
 * Description  Testing the Sparql Engine
 * 
 * -----------------------------------------------------------------------------
 */
class SparqlEngineTest extends PHPUnit_Framework_TestCase {

    private $model;

    protected function setUp() {

        $model = new Model();
        $model->addBaseNamespace(PREFIX, NS);

        $res = $model->newResource("test")
                ->addProperty($model->newResource("pred1"), new LiteralNode("literal1"))
                ->addProperty($model->newResource("pred2"), new LiteralNode("literal2"))
                ->addProperty($model->newResource("pred3"), $model->newResource("test2")
                ->addProperty($model->newResource("pred"), new LiteralNode("literal3"))
                ->addProperty($model->newResource("pred2"), new LiteralNode("literal4"))
        );

        $model->add($res);

        $this->model = $model;
    }

    public function testQuery1() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "SELECT ?x ?y WHERE ";
        $query.= "{ ";
        $query.= "?x ?y ?z";
        $query.= "}";

        $engine = new SparqlEngine();
        $res = $engine->query($query, $this->model);

//        print_r($res);
    }

    public function testQuery2() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "SELECT ?x ?y WHERE ";
        $query.= "{ ";
        $query.= "?x ex:pred1 ?z";
        $query.= "}";

        $engine = new SparqlEngine();
        $res = $engine->query($query, $this->model);

//        print_r($res);
    }

    public function testQuery3() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "SELECT ?x ?y WHERE ";
        $query.= "{ ";
        $query.= "?subject ex:pred3 ?object";
        $query.= "}";

        $engine = new SparqlEngine();
        $res = $engine->query($query, $this->model);

//        print_r($res);
    }

    public function testQuery4() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "SELECT ?x ?y WHERE ";
        $query.= "{ ";
        $query.= "?x ex:pred3 ?y . ";
        $query.= "?y ex:pred2 ?z";
        $query.= "}";

        $engine = new SparqlEngine();
        $res = $engine->query($query, $this->model);

//        print_r($res);
    }

    public function testQuery5() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "SELECT ?x ?y WHERE ";
        $query.= "{ ";
        $query.= "?x ex:pred3 ?y . ";
        $query.= "?y ex:pred2 ?z1 . ";
        $query.= "?y ex:pred ?z2";
        $query.= "}";

        $engine = new SparqlEngine();
        $res = $engine->query($query, $this->model);

//        print_r($res);
    }

    public function testQuery6() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "SELECT ?x ?y ?z1 WHERE ";
        $query.= "{ ";
        $query.= "?x ex:pred3 ?y . ";
        $query.= "?x ex:pred1 \"literal1\"^^xsd:string . ";
        $query.= "?y ex:pred2 ?z1 . ";
        $query.= "?y ex:pred ?z2";
        $query.= "}";

        $engine = new SparqlEngine();
        $res = $engine->query($query, $this->model);

//        print_r($res);
    }

    public function testQuery7() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "SELECT ?x WHERE ";
        $query.= "{ ";
        $query.= "?x ex:pred1 \"literal1\"^^xsd:string ";
        $query.= "}";

        $engine = new SparqlEngine();
        $res = $engine->query($query, $this->model);

//        print_r($res);
    }

    public function testQuery8() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "SELECT ?x ?z WHERE ";
        $query.= "{ ";
        $query.= "?x ex:pred1 \"literal1\"^^xsd:string . ";
        $query.= "?x ex:pred1 ?z ";
        $query.= "}";

        $engine = new SparqlEngine();
        $res = $engine->query($query, $this->model);

//        print_r($res);
    }

    public function testQuery9() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "SELECT ?x WHERE ";
        $query.= "{ ";
        $query.= "?x ex:pred3 ex:test2 ";
        $query.= "}";

        $engine = new SparqlEngine();
        $res = $engine->query($query, $this->model);

//        print_r($res);
    }

    public function testQuery10() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "SELECT ?x ?z WHERE ";
        $query.= "{ ";
        $query.= "?x ex:pred3 ex:test2 . ";
        $query.= "?x ex:pred3 ?z ";
        $query.= "}";

        $engine = new SparqlEngine();
        $res = $engine->query($query, $this->model);

//        print_r($res);
    }
    
        public function testQuery11() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "SELECT ?y WHERE ";
        $query.= "{ ";
        $query.= "ex:test ?y ex:test2 . ";
        $query.= "ex:test ?y ?z . ";
        $query.= "ex:test ?y ?z ";
        $query.= "}";

        $engine = new SparqlEngine();
        $res = $engine->query($query, $this->model);

//        print_r($res);
    }
    
    public function testQuery12() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "SELECT ?x ?y WHERE ";
        $query.= "{ ";
        $query.= "?x ?y ?z . ";
        $query.= "?x ?y ?z . ";
        $query.= "?x ?y ?z1 . ";
        $query.= "?x ?y1 ?z ";
        $query.= "}";

        $engine = new SparqlEngine();
        $res = $engine->query($query, $this->model);
    }

    // Add your tests here

    protected function tearDown() {
        
    }

}

?>
