<?php

require_once "settings.php";

/**
 * -----------------------------------------------------------------------------
 * ERP API Test
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        ModelTest.php
 * @version     2011-08-10
 * @package     tests
 * @access      public
 * 
 * Description  Testing the class Model
 * 
 * -----------------------------------------------------------------------------
 */
class ModelTest extends PHPUnit_Framework_TestCase {

    private $model;

    protected function setUp() {
        $this->model = new Model();
        $this->model->addBaseNamespace(PREFIX, NS);
    }

    public function testAddNamespace() {
        $this->model->addNamespace("ns", NS);
        $this->assertEquals($this->model->getNamespace("ns"), NS);
    }

    public function testRemoveNamespace() {

        $this->assertFalse($this->model->hasNamespace("ns"));
        
        $this->model->addNamespace("ns", NS);
        $this->assertTrue($this->model->hasNamespace("ns"));
        $this->assertTrue($this->model->removeNamespace("ns"));
        $this->assertFalse($this->model->hasNamespace("ns"));
    }

    public function testGetNamespace() {
        $this->model->addNamespace("ns", NS);
        $this->assertTrue($this->model->getNamespace("ns") == NS);
    }

    public function testHasNamespace() {
        $this->model->addNamespace("ns", NS);
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
        $this->model->add(new Resource(NS."test"));
    }

    /**
     * @expectedException APIException
     */
    public function testAddError3() {
        $this->model->add($this->model->newBlankNode());
    }

    /**
     * @expectedException APIException
     */
    public function testAddError4() {
        $this->model->add(new LiteralNode("test"));
    }

    public function testAddDoubleError() {
        $statement = new Statement(new Resource(NS."test"), new Resource(NS."pred"), new LiteralNode("literal"));
        $this->model->add($statement);

        $this->assertTrue($this->model->contains($statement));
        $this->assertFalse($this->model->add($statement, false));
        $this->assertTrue($this->model->size() == 1);
    }

    public function testAddDoubleSuccess() {
        $statement = new Statement(new Resource(NS."test"), new Resource(NS."pred"), new LiteralNode("literal"));
        $this->model->add($statement);

        $this->assertTrue($this->model->contains($statement));
        $this->assertTrue($this->model->add($statement, true));
        $this->assertTrue($this->model->size() == 2);
    }

    public function testSearchError() {
        $this->assertTrue($this->model->search(null, null, null) == null);

        $res = new Resource(NS."test");
        $pred = new Resource(NS."pred");
        $obj = new LiteralNode("literal");

        $statement = new Statement($res, $pred, $obj);

        $this->model->add($statement);
        $this->assertTrue($this->model->search($res, $pred, $obj) != null);
        $this->assertTrue($this->model->search(new Resource(NS."test2"), $pred, $obj) == null);
    }

    public function testSearchSuccess1() {
        $statement = new Statement(new Resource(NS."test"), new Resource(NS."pred"), new LiteralNode("literal"));
        $this->model->add($statement);

        $this->assertTrue($this->model->contains($statement));
    }

    public function testSearchSuccess2() {
        $statement1 = new Statement(new Resource(NS."test1"), new Resource(NS."pred"), new LiteralNode("literal1"));

        $this->model->add($statement1);

        $this->assertTrue($this->model->contains($statement1));
        $this->assertTrue($this->model->search(new Resource(NS."test1")) != null);

        $resource = $this->model->searchResource(new Resource(NS."test1"));

        $this->assertTrue($resource instanceof Resource);
        $this->assertTrue($resource->equals(new Resource(NS."test1")));
        $this->assertTrue($resource->hasProperty(new Resource(NS."pred")));
    }

    public function testSearchSuccess3() {

        //simple resource tree

        $statement1 = new Statement(new Resource(NS."test1"), new Resource(NS."pred"), new Resource(NS."test2"));
        $statement2 = new Statement(new Resource(NS."test2"), new Resource(NS."pred"), new LiteralNode("literal2"));

        $this->model->add($statement1);
        $this->model->add($statement2);

        $this->assertTrue($this->model->contains($statement1));
        $this->assertTrue($this->model->contains($statement2));
        $this->assertTrue($this->model->search(new Resource(NS."test1")) != null);
        $this->assertTrue($this->model->search(new Resource(NS."test2")) != null);

        $resource = $this->model->searchResource(new Resource(NS."test1"));

        $this->assertTrue($resource instanceof Resource);
        $this->assertTrue($resource->equals(new Resource(NS."test1")));
        $this->assertTrue($resource->hasProperty(new Resource(NS."pred")));

        $this->assertTrue($resource->getProperty(new Resource(NS."pred"))->equals(new Resource(NS."test2")));
        $this->assertTrue($resource->getProperty(new Resource(NS."pred"))->hasProperty(new Resource(NS."pred")));
        $this->assertTrue($resource->getProperty(new Resource(NS."pred"))->getProperty(new Resource(NS."pred"))->equals(new LiteralNode("literal2")));
    }

    public function testSearchSuccess4() {

        // recursion

        $statement1 = new Statement(new Resource(NS."test1"), new Resource(NS."pred"), new Resource(NS."test2"));
        $statement2 = new Statement(new Resource(NS."test2"), new Resource(NS."pred"), new Resource(NS."test1"));

        $this->model->add($statement1);
        $this->model->add($statement2);

        $this->assertTrue($this->model->contains($statement1));
        $this->assertTrue($this->model->contains($statement2));
        $this->assertTrue($this->model->search(new Resource(NS."test1")) != null);
        $this->assertTrue($this->model->search(new Resource(NS."test2")) != null);

        $resource = $this->model->searchResource(new Resource(NS."test1"));

        $this->assertTrue($resource instanceof Resource);
        $this->assertTrue($resource->equals(new Resource(NS."test1")));
        $this->assertTrue($resource->hasProperty(new Resource(NS."pred")));

        $this->assertTrue($resource->getProperty(new Resource(NS."pred"))->equals(new Resource(NS."test2")));
        $this->assertTrue($resource->getProperty(new Resource(NS."pred"))->hasProperty(new Resource(NS."pred")));
        $this->assertTrue($resource->getProperty(new Resource(NS."pred"))->getProperty(new Resource(NS."pred"))->equals($resource));
    }

    public function testContainsSuccess() {
        $statement = new Statement(new Resource(NS."test"), new Resource(NS."pred"), new LiteralNode("literal"));
        $this->model->add($statement);

        $this->assertTrue($this->model->contains($statement));
    }

    public function testContainsError() {
        $statement = new Statement(new Resource(NS."test"), new Resource(NS."pred"), new LiteralNode("literal"));
        $this->assertFalse($this->model->contains($statement));
    }

    public function testAddSuccess1() {
        $res = new Resource(NS."test");
        $pred = new Resource(NS."pred");
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
        $res = new Resource(NS."test");
        $pred = new Resource(NS."pred");
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
        $res = new Resource(NS."test");
        $pred1 = new Resource(NS."pred");
        $pred2 = new Resource(NS."pred2");
        $obj = new LiteralNode("literal");

        $res2 = new Resource(NS."test2");
        $pred3 = new Resource(NS."pred3");
        $obj2 = new LiteralNode("literal2");

        $res->addProperty($pred1, $obj)->addProperty($pred2, $res2->addProperty($pred3, $obj2));
        $res->addProperty($pred3, $obj2);
        $res->addProperty(new Resource(NS."pred4"), $res2);

        $res2->addProperty($pred1, $obj);
        $res2->addProperty(new Resource(NS."pred4"), $obj);

        $this->model->add($res);

        $this->assertTrue($this->model->search($res, $pred, $obj) != null);
        $this->assertTrue($this->model->search($res, $pred3, $obj2) != null);
        $this->assertTrue($this->model->search($res, $pred2, $res2) != null);
        $this->assertTrue($this->model->search($res2, $pred3, $obj2) != null);
        $this->assertTrue($this->model->search($res2, $pred, $obj) != null);
        $this->assertTrue($this->model->search($res2, new Resource(NS."pred4"), $obj) != null);
        $this->assertTrue($this->model->search($res, new Resource(NS."pred4"), $res2) != null);
    }

    public function testInstanceCreation() {

        $this->assertTrue(is_a($this->model->newResource("test"), Resource));
        $this->assertTrue(is_a($this->model->newResource(), Resource));
        $this->assertTrue(is_a($this->model->newResource(), BlankNode));
        $this->assertTrue(is_a($this->model->newBlankNode(), BlankNode));
        $this->assertTrue(is_a($this->model->newLiteralNode("literal"), LiteralNode));
        $this->assertTrue(is_a($this->model->newLiteralNode("literal", STRING), LiteralNode));
        $this->assertTrue(is_a($this->model->newStatement(new BlankNode("http://example.org/", "bnode1"), new Resource(NS."pred"), new BlankNode("http://example.org/", "bnode2")), Statement));
        
        $this->assertTrue($this->model->newBlankNode()->getId() == "bNode3");
    }

    public function testSize() {

        $this->assertTrue($this->model->size() == 0);

        $statement = new Statement(new Resource(NS."test"), new Resource(NS."pred"), new LiteralNode("literal"));
        $this->model->add($statement);

        $this->assertTrue($this->model->size() == 1);
    }

    public function testIsEmpty() {

        $this->assertTrue($this->model->isEmpty());

        $statement = new Statement(new Resource(NS."test"), new Resource(NS."pred"), new LiteralNode("literal"));
        $this->model->add($statement);

        $this->assertFalse($this->model->isEmpty());
    }

    /**
     * @expectedException APIException
     */
    public function testRemoveError1() {

        $this->model->remove(null);
    }

    /**
     * @expectedException APIException
     */
    public function testRemoveError2() {

        $this->model->remove(new Resource(NS."test"));
    }

    /**
     * @expectedException APIException
     */
    public function testRemoveError3() {

        $this->model->remove(new LiteralNode("test"));
    }

    public function testRemoveSuccess1() {

        $statement = new Statement(new Resource(NS."test"), new Resource(NS."pred"), new LiteralNode("literal"));

        $this->assertTrue($this->model->size() == 0);
        $this->assertFalse($this->model->contains($statement));

        $this->model->add($statement);
        $this->assertTrue($this->model->size() == 1);
        $this->assertTrue($this->model->contains($statement));

        $this->model->remove($statement);

        $this->assertTrue($this->model->size() == 0);
        $this->assertFalse($this->model->contains($statement));
    }

    public function testRemoveSuccess2() {

        $res = new Resource(NS."test");

        $res->addProperty(new Resource(NS."pred"), new LiteralNode("literal"));

        $this->assertTrue($this->model->size() == 0);

        $this->model->add($res);
        $this->assertTrue($this->model->size() == 1);
        $this->assertTrue($this->model->search($res) != null);

        $this->model->remove($res);

        $this->assertTrue($this->model->size() == 0);
        $this->assertTrue($this->model->search($res) == null);
    }

    protected function tearDown() {

        $this->model = null;
    }

}

?>