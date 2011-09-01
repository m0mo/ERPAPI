<?php

require_once 'settings.php';

/**
 * --------------------------------------------------------------------
 * ERP API Test
 * --------------------------------------------------------------------
 *
 * Teting the SarqlEngine class
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com>
 * @name        SparqlEngineTest.php
 * @version     2011-09-01
 * @package     tests/sparql/sparqlEngine
 * @access      public
 *
 * --------------------------------------------------------------------
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

        $this->assertTrue(Check::isArray($res));
        $this->assertTrue(Check::isArray($res["variables"]));
        $this->assertEquals(count($res["variables"]), 2);
        $this->assertTrue(Check::isArray($res["table"]));
        $this->assertEquals(count(end($res["table"])), 5);
        $this->assertTrue(Check::isString($res["time"]));
        $this->assertTrue(Check::isString($res["query"]));
        $this->assertEquals(count($res), 4);
    }

    public function testQuery2() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "SELECT ?x ?y WHERE ";
        $query.= "{ ";
        $query.= "?x ex:pred1 ?y";
        $query.= "}";

        $res = $this->model->sparqlQuery($query);

        $this->assertTrue(Check::isArray($res));
        $this->assertTrue(Check::isArray($res["variables"]));
        $this->assertEquals(count($res["variables"]), 2);
        $this->assertTrue(Check::isArray($res["table"]));
        $this->assertEquals(count(end($res["table"])), 1);
        $this->assertTrue(Check::isString($res["time"]));
        $this->assertTrue(Check::isString($res["query"]));
        $this->assertEquals(count($res), 4);
    }

    public function testQuery3() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "SELECT ?x ?y WHERE ";
        $query.= "{ ";
        $query.= "?x ex:pred3 ?y";
        $query.= "}";

        $engine = new SparqlEngine();
        $res = $engine->query($query, $this->model);

        $this->assertTrue(Check::isArray($res));
        $this->assertTrue(Check::isArray($res["variables"]));
        $this->assertEquals(count($res["variables"]), 2);
        $this->assertTrue(Check::isArray($res["table"]));
        $this->assertEquals(count(end($res["table"])), 1);
        $this->assertTrue(Check::isString($res["time"]));
        $this->assertTrue(Check::isString($res["query"]));
        $this->assertEquals(count($res), 4);
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

        $this->assertTrue(Check::isArray($res));
        $this->assertTrue(Check::isArray($res["variables"]));
        $this->assertEquals(count($res["variables"]), 2);
        $this->assertTrue(Check::isArray($res["table"]));
        $this->assertEquals(count(end($res["table"])), 1);
        $this->assertTrue(Check::isString($res["time"]));
        $this->assertTrue(Check::isString($res["query"]));
        $this->assertEquals(count($res), 4);
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

        $this->assertTrue(Check::isArray($res));
        $this->assertTrue(Check::isArray($res["variables"]));
        $this->assertEquals(count($res["variables"]), 2);
        $this->assertTrue(Check::isArray($res["table"]));
        $this->assertEquals(count(end($res["table"])), 1);
        $this->assertTrue(Check::isString($res["time"]));
        $this->assertTrue(Check::isString($res["query"]));
        $this->assertEquals(count($res), 4);
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

        $this->assertTrue(Check::isArray($res));
        $this->assertTrue(Check::isArray($res["variables"]));
        $this->assertEquals(count($res["variables"]), 3);
        $this->assertTrue(Check::isArray($res["table"]));
        $this->assertEquals(count(end($res["table"])), 1);
        $this->assertTrue(Check::isString($res["time"]));
        $this->assertTrue(Check::isString($res["query"]));
        $this->assertEquals(count($res), 4);
    }

    public function testQuery7() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "SELECT ?x WHERE ";
        $query.= "{ ";
        $query.= "?x ex:pred1 \"literal1\"^^xsd:string ";
        $query.= "}";

        $engine = new SparqlEngine();
        $res = $engine->query($query, $this->model);

        $this->assertTrue(Check::isArray($res));
        $this->assertTrue(Check::isArray($res["variables"]));
        $this->assertEquals(count($res["variables"]), 1);
        $this->assertTrue(Check::isArray($res["table"]));
        $this->assertEquals(count(end($res["table"])), 1);
        $this->assertTrue(Check::isString($res["time"]));
        $this->assertTrue(Check::isString($res["query"]));
        $this->assertEquals(count($res), 4);
    }

    public function testQuery8() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "SELECT ?x ?z WHERE ";
        $query.= "{ ";
        $query.= "?x ex:pred1 \"literal1\"^^xsd:string . ";
        $query.= "?x ex:pred1 ?z ";
        $query.= "}";

        $engine = new SparqlEngine();
        $res = $engine->query($query, $this->model, "unknown returns array");

        $this->assertTrue(Check::isArray($res));
        $this->assertTrue(Check::isArray($res["variables"]));
        $this->assertEquals(count($res["variables"]), 2);
        $this->assertTrue(Check::isArray($res["table"]));
        $this->assertEquals(count(end($res["table"])), 1);
        $this->assertTrue(Check::isString($res["time"]));
        $this->assertTrue(Check::isString($res["query"]));
        $this->assertEquals(count($res), 4);
    }

    public function testQuery9() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "SELECT ?x WHERE ";
        $query.= "{ ";
        $query.= "?x ex:pred3 ex:test2 ";
        $query.= "}";

        $engine = new SparqlEngine();
        $res = $engine->query($query, $this->model, "objectarray");

        $this->assertTrue(Check::isArray($res));
        $this->assertTrue(Check::isArray($res["variables"]));
        $this->assertEquals(count($res["variables"]), 1);
        $this->assertTrue(Check::isArray($res["table"]));
        $this->assertEquals(count(end($res["table"])), 1);
        $this->assertTrue(Check::isString($res["time"]));
        $this->assertTrue(Check::isString($res["query"]));
        $this->assertEquals(count($res), 4);
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

        $this->assertTrue(Check::isArray($res));
        $this->assertTrue(Check::isArray($res["variables"]));
        $this->assertEquals(count($res["variables"]), 2);
        $this->assertTrue(Check::isArray($res["table"]));
        $this->assertEquals(count(end($res["table"])), 1);
        $this->assertTrue(Check::isString($res["time"]));
        $this->assertTrue(Check::isString($res["query"]));
        $this->assertEquals(count($res), 4);
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

        $this->assertTrue(Check::isArray($res));
        $this->assertTrue(Check::isArray($res["variables"]));
        $this->assertEquals(count($res["variables"]), 1);
        $this->assertTrue(Check::isArray($res["table"]));
        $this->assertEquals(count(end($res["table"])), 1);
        $this->assertTrue(Check::isString($res["time"]));
        $this->assertTrue(Check::isString($res["query"]));
        $this->assertEquals(count($res), 4);
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

        $this->assertTrue(Check::isArray($res));
        $this->assertTrue(Check::isArray($res["variables"]));
        $this->assertEquals(count($res["variables"]), 2);
        $this->assertTrue(Check::isArray($res["table"]));
        $this->assertEquals(count(end($res["table"])), 5);
        $this->assertTrue(Check::isString($res["time"]));
        $this->assertTrue(Check::isString($res["query"]));
        $this->assertEquals(count($res), 4);
    }

    /**
     * @expectedException SparqlException
     */
    public function testQueryError1() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "ASK ?x ?y WHERE ";
        $query.= "{ ";
        $query.= "?x ?y ?z . ";
        $query.= "?x ?y ?z . ";
        $query.= "?x ?y ?z1 . ";
        $query.= "?x ?y1 ?z ";
        $query.= "}";

        $this->model->sparqlQuery($query);
    }

    public function testQuery13() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "SELECT ?x ?y ?z ?a ?b WHERE ";
        $query.= "{ ";
        $query.= "?x ?y ?z . ";
        $query.= "?z ?a ?b ";
        $query.= "}";

        $res = $this->model->sparqlQuery($query, "array");
    }

    public function testQuery14() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "SELECT ?x ?y ?z ?a ?b ?c ?d WHERE ";
        $query.= "{ ";
        $query.= "?x ?y ?z . ";
        $query.= "?x ?c ?d . ";
        $query.= "?z ?a ?b ";
        $query.= "}";

        $res = $this->model->sparqlQuery($query, "array");
    }

    public function testQuery15() {

        $query = "PREFIX " . PREFIX . ": <" . NS . "> ";
        $query.= "SELECT ?x ?y ?z ?a ?b ?c ?d WHERE ";
        $query.= "{ ";
        $query.= "?x ?y ?z . ";
        $query.= "?x ?y ?c . ";
        $query.= "?z ?a ?b ";
        $query.= "}";

        $res = $this->model->sparqlQuery($query, "array");
    }

    protected function tearDown() {

    }

}

?>
