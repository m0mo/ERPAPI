<?php

require_once 'PHPUnit/Autoload.php';
require_once "../API.php";

/**
 * -----------------------------------------------------------------------------
 * ERP API Test
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        ModelTest.php
 * @version     0.1.5 (Aug 6, 2011)
 * @package     tests
 * @access      public
 * 
 * Description  here
 * 
 * -----------------------------------------------------------------------------
 */
class ModelTest extends PHPUnit_Framework_TestCase {

    private $model;

    protected function setUp() {
        $this->model = new Model();
    }

    public function testAddNamespace() {
        $this->model->addNamespace("ns", "http://test/");
        $this->assertEquals($this->model->getNamespace("ns"), "http://test/");
    }

    public function testDeleteNamespace() {

        $this->model->addNamespace("ns", "1234");
        $this->assertTrue($this->model->hasNamespace("ns"));
        $this->assertTrue($this->model->removeNamespace("ns"));
        $this->assertFalse($this->model->hasNamespace("ns"));
    }

    public function testGetNamespace() {
        $this->model->addNamespace("ns", "1234");
        $this->assertTrue($this->model->getNamespace("ns") == "1234");
    }

    public function testHasNamespace() {
        $this->model->addNamespace("ns", "1234");
        $this->assertTrue($this->model->hasNamespace("ns"));
        $this->assertFalse($this->model->hasNamespace("ns1"));
    }

    /**
     * @expectedException APIException
     */
    public function testAddError1() {
        $this->model->add("bla");
    }

    /**
     * @expectedException APIException
     */
    public function testAddError2() {
        $this->model->add(new Resource("ns:test"));
    }

    /**
     * @expectedException APIException
     */
    public function testAddError3() {
        $this->model->add(new BlankNode());
    }

    /**
     * @expectedException APIException
     */
    public function testAddError4() {
        $this->model->add(new LiteralNode("test"));
    }

    public function testSearchError() {
        $this->assertTrue($this->model->search(null, null, null) == null);

        $res = new Resource("ns:test");
        $pred = new Resource("ns:pred");
        $obj = new LiteralNode("literal");

        $statement = new Statement($res, $pred, $obj);

        $this->model->add($statement);
        $this->assertTrue($this->model->search($res, $pred, $obj) != null);
        $this->assertTrue($this->model->search(new Resource("ns:test2"), $pred, $obj) == null);
    }

    public function testSearchSuccess() {
        $statement = new Statement(new Resource("ns:test"), new Resource("ns:pred"), new LiteralNode("literal"));
        $this->model->add($statement);

        $this->assertTrue($this->model->contains($statement));
    }

    public function testContainsSuccess() {
        $statement = new Statement(new Resource("ns:test"), new Resource("ns:pred"), new LiteralNode("literal"));
        $this->model->add($statement);

        $this->assertTrue($this->model->contains($statement));
    }

    public function testContainsError() {
        $statement = new Statement(new Resource("ns:test"), new Resource("ns:pred"), new LiteralNode("literal"));
        $this->assertFalse($this->model->contains($statement));
    }

    public function testAddSuccess1() {
        $res = new Resource("ns:test");
        $pred = new Resource("ns:pred");
        $obj = new LiteralNode("literal");

        $statement = new Statement($res, $pred, $obj);

        $this->model->add($statement);
        $this->assertTrue($this->model->search($res, $pred, $obj) != null);

        $result = $this->model->search($res, $pred, $obj);

        $this->assertTrue($result[0]->equals($statement));

        // Following searches should return same result as above since there is just
        // one statement in the model

        $this->assertTrue($this->model->search(null, null, null) != null);
        $this->assertTrue($this->model->search($res, null, null) != null);
        $this->assertTrue($this->model->search(null, $pred, null) != null);
        $this->assertTrue($this->model->search(null, null, $obj) != null);
    }

    public function testAddSuccess2() {
        $res = new Resource("ns:test");
        $pred = new Resource("ns:pred");
        $obj = new LiteralNode("literal");

        $res->addProperty($pred, $obj);

        $this->model->add($res);
        $this->assertTrue($this->model->search($res, $pred, $obj) != null);

        $result = $this->model->search($res, $pred, $obj);

        $this->assertTrue($result[0]->getSubject()->equals($res));

        // Following searches should return same result as above since there is just
        // one statement in the model

        $this->assertTrue($this->model->search(null, null, null) != null);
        $this->assertTrue($this->model->search($res, null, null) != null);
        $this->assertTrue($this->model->search(null, $pred, null) != null);
        $this->assertTrue($this->model->search(null, null, $obj) != null);
    }

    public function testAddSuccess3() {
        $res = new Resource("ns:test");
        $pred1 = new Resource("ns:pred");
        $pred2 = new Resource("ns:pred2");
        $obj = new LiteralNode("literal");

        $res2 = new Resource("ns:test2");
        $pred3 = new Resource("ns:pred3");
        $obj2 = new LiteralNode("literal2");

        $res->addProperty($pred1, $obj)->addProperty($pred2, $res2->addProperty($pred3, $obj2));
        $res->addProperty($pred3, $obj2);
        $res->addProperty(new Resource("ns:pred4"), $res2);

        $res2->addProperty($pred1, $obj);
        $res2->addProperty(new Resource("ns:pred4"), $obj);

        $this->model->add($res);

        $this->assertTrue($this->model->search($res, $pred, $obj) != null);
        $this->assertTrue($this->model->search($res, $pred3, $obj2) != null);
        $this->assertTrue($this->model->search($res, $pred2, $res2) != null);
        $this->assertTrue($this->model->search($res2, $pred3, $obj2) != null);
        $this->assertTrue($this->model->search($res2, $pred, $obj) != null);
        $this->assertTrue($this->model->search($res2, new Resource("ns:pred4"), $obj) != null);
        $this->assertTrue($this->model->search($res, new Resource("ns:pred4"), $res2) != null);

        echo $this->model->modelToString();
    }

    public function testInstanceCreation() {

        $this->assertTrue(is_a($this->model->newResource("ns:test"), Resource));
        $this->assertTrue(is_a($this->model->newResource(), Resource));
        $this->assertTrue(is_a($this->model->newResource(), BlankNode));
        $this->assertTrue(is_a($this->model->newBlankNode(), BlankNode));
        $this->assertTrue(is_a($this->model->newLiteralNode("literal"), LiteralNode));
        $this->assertTrue(is_a($this->model->newLiteralNode("literal", STRING), LiteralNode));
        $this->assertTrue(is_a($this->model->newStatement(new BlankNode(), new Resource("ns:pred"), new BlankNode), Statement));
    }

    protected function tearDown() {

        $this->model = null;
    }

}

?>