<?php

require_once "settings.php";

/**
 * --------------------------------------------------------------------
 * ERP API Test
 * --------------------------------------------------------------------
 *
 * Testing the Model class
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com>
 * @name        ModelTest.php
 * @version     2011-08-31
 * @package     tests/model
 * @access      public
 *
 * --------------------------------------------------------------------
 */
class ModelTest extends PHPUnit_Framework_TestCase {

    private $model;

    protected function setUp() {
        $this->model = ERP::getModel();
        $this->model->addBaseNamespace(PREFIX, NS);
    }

    public function testAddNamespace() {
        $this->model->addNamespace("ns", NS);
        $this->assertEquals($this->model->getNamespace("ns"), NS);
        $this->assertTrue(count($this->model->getNamespaces()) == 2);
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

    public function testAddError2() {
        $this->assertFalse($this->model->add(new Resource(NS . "test")));
    }

    public function testAddError3() {
        $this->assertFalse($this->model->add($this->model->newBlankNode()));
    }

    /**
     * @expectedException APIException
     */
    public function testAddError4() {
        $this->model->add(new LiteralNode("test"));
    }

    public function testAddDoubleError() {
        $statement = new Statement(new Resource(NS . "test"), new Resource(NS . "pred"), new LiteralNode("literal"));
        $this->model->add($statement);

        $this->assertTrue($this->model->contains($statement));
        $this->assertFalse($this->model->add($statement, false));
        $this->assertTrue($this->model->size() == 1);
    }

    public function testAddDoubleSuccess() {
        $statement = new Statement(new Resource(NS . "test"), new Resource(NS . "pred"), new LiteralNode("literal"));
        $this->model->add($statement);

        $this->assertTrue($this->model->contains($statement));
        $this->assertTrue($this->model->add($statement, true));
        $this->assertTrue($this->model->size() == 2);
    }

    public function testSearchError() {
        $this->assertTrue($this->model->search(null, null, null) == null);

        $res = new Resource(NS . "test");
        $pred = new Resource(NS . "pred");
        $obj = new LiteralNode("literal");

        $statement = new Statement($res, $pred, $obj);

        $this->model->add($statement);
        $this->assertTrue($this->model->search($res, $pred, $obj) != null);
        $this->assertTrue($this->model->search(new Resource(NS . "test2"), $pred, $obj) == null);
    }

    public function testSearchSuccess1() {
        $statement = new Statement(new Resource(NS . "test"), new Resource(NS . "pred"), new LiteralNode("literal"));
        $this->model->add($statement);

        $this->assertTrue($this->model->contains($statement));
    }

    public function testSearchSuccess2() {
        $statement1 = new Statement(new Resource(NS . "test1"), new Resource(NS . "pred"), new LiteralNode("literal1"));

        $this->model->add($statement1);

        $this->assertTrue($this->model->contains($statement1));
        $this->assertTrue($this->model->search(new Resource(NS . "test1")) != null);

        $resource = $this->model->searchResources(new Resource(NS . "test1"));

        $this->assertTrue(Check::isResource($resource));
        $this->assertTrue($resource->equals(new Resource(NS . "test1")));
        $this->assertTrue($resource->hasProperty(new Resource(NS . "pred")));
    }

    public function testSearchSuccess3() {

        //simple resource tree

        $statement1 = new Statement(new Resource(NS . "test1"), new Resource(NS . "pred"), new Resource(NS . "test2"));
        $statement2 = new Statement(new Resource(NS . "test2"), new Resource(NS . "pred"), new LiteralNode("literal2"));

        $this->model->add($statement1);
        $this->model->add($statement2);

        $this->assertTrue($this->model->contains($statement1));
        $this->assertTrue($this->model->contains($statement2));
        $this->assertTrue($this->model->search(new Resource(NS . "test1")) != null);
        $this->assertTrue($this->model->search(new Resource(NS . "test2")) != null);

        $resource = $this->model->searchResources(new Resource(NS . "test1"));

        $this->assertTrue(Check::isResource($resource));
        $this->assertTrue($resource->equals(new Resource(NS . "test1")));
        $this->assertTrue($resource->hasProperty(new Resource(NS . "pred")));

        $this->assertTrue($resource->getProperty(new Resource(NS . "pred"))->equals(new Resource(NS . "test2")));
        $this->assertTrue($resource->getProperty(new Resource(NS . "pred"))->hasProperty(new Resource(NS . "pred")));
        $this->assertTrue($resource->getProperty(new Resource(NS . "pred"))->getProperty(new Resource(NS . "pred"))->equals(new LiteralNode("literal2")));
    }

    public function testSearchSuccess4() {

        // recursion

        $statement1 = new Statement(new Resource(NS . "test1"), new Resource(NS . "pred"), new Resource(NS . "test2"));
        $statement2 = new Statement(new Resource(NS . "test2"), new Resource(NS . "pred"), new Resource(NS . "test1"));

        $this->model->add($statement1);
        $this->model->add($statement2);

        $this->assertTrue($this->model->contains($statement1));
        $this->assertTrue($this->model->contains($statement2));
        $this->assertTrue($this->model->search(new Resource(NS . "test1")) != null);
        $this->assertTrue($this->model->search(new Resource(NS . "test2")) != null);

        $resource = $this->model->searchResources(new Resource(NS . "test1"));

        $this->assertTrue(Check::isResource($resource));
        $this->assertTrue($resource->equals(new Resource(NS . "test1")));
        $this->assertTrue($resource->hasProperty(new Resource(NS . "pred")));

        $this->assertTrue($resource->getProperty(new Resource(NS . "pred"))->equals(new Resource(NS . "test2")));
        $this->assertTrue($resource->getProperty(new Resource(NS . "pred"))->hasProperty(new Resource(NS . "pred")));
        $this->assertTrue($resource->getProperty(new Resource(NS . "pred"))->getProperty(new Resource(NS . "pred"))->equals($resource));
    }

    public function testSearchSuccess5() {

        // recursion

        $statement1 = new Statement(new Resource(NS . "test1"), new Resource(NS . "pred"), new Resource(NS . "test2"));
        $statement2 = new Statement(new Resource(NS . "test2"), new Resource(NS . "pred"), new Resource(NS . "test3"));

        $statement3 = new Statement(new Resource(NS . "test4"), new Resource(NS . "pred"), new Resource(NS . "test3"));

        $this->model->add($statement1);
        $this->model->add($statement2);
        $this->model->add($statement3);

        $this->assertTrue($this->model->contains($statement1));
        $this->assertTrue($this->model->contains($statement2));
        $this->assertTrue($this->model->contains($statement3));

        $resources = $this->model->searchResources();

        $this->assertTrue(Check::isArray($resources));
        $this->assertEquals(count($resources), 2);

        foreach ($resources as $resource) {

            $this->assertTrue(Check::isResource($resource));
            $this->assertTrue($resource->hasProperties());
        }
    }

    public function testContainsSuccess() {
        $statement = new Statement(new Resource(NS . "test"), new Resource(NS . "pred"), new LiteralNode("literal"));
        $this->model->add($statement);

        $this->assertTrue($this->model->contains($statement));
    }

    public function testContainsError() {
        $statement = new Statement(new Resource(NS . "test"), new Resource(NS . "pred"), new LiteralNode("literal"));
        $this->assertFalse($this->model->contains($statement));
    }

    public function testAddSuccess1() {
        $res = new Resource(NS . "test");
        $pred = new Resource(NS . "pred");
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
        $res = new Resource(NS . "test");
        $pred = new Resource(NS . "pred");
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
        $res = new Resource(NS . "test");
        $pred1 = new Resource(NS . "pred");
        $pred2 = new Resource(NS . "pred2");
        $obj = new LiteralNode("literal");

        $res2 = new Resource(NS . "test2");
        $pred3 = new Resource(NS . "pred3");
        $obj2 = new LiteralNode("literal2");

        $res->addProperty($pred1, $obj)->addProperty($pred2, $res2->addProperty($pred3, $obj2));
        $res->addProperty($pred3, $obj2);
        $res->addProperty(new Resource(NS . "pred4"), $res2);

        $res2->addProperty($pred1, $obj);
        $res2->addProperty(new Resource(NS . "pred4"), $obj);

        $this->model->add($res);

        $this->assertTrue($this->model->search($res, $pred1, $obj) != null);
        $this->assertTrue($this->model->search($res, $pred3, $obj2) != null);
        $this->assertTrue($this->model->search($res, $pred2, $res2) != null);
        $this->assertTrue($this->model->search($res2, $pred3, $obj2) != null);
        $this->assertTrue($this->model->search($res2, $pred1, $obj) != null);
        $this->assertTrue($this->model->search($res2, new Resource(NS . "pred4"), $obj) != null);
        $this->assertTrue($this->model->search($res, new Resource(NS . "pred4"), $res2) != null);
    }

    public function testInstanceCreation() {

        $this->assertTrue(Check::isResource($this->model->newResource("test")));
        $this->assertTrue(Check::isResource($this->model->newResource()));
        $this->assertTrue(Check::isBlankNode($this->model->newResource()));
        $this->assertTrue(Check::isBlankNode($this->model->newBlankNode()));
        $this->assertTrue(Check::isLiteralNode($this->model->newLiteralNode("literal")));
        $this->assertTrue(Check::isLiteralNode($this->model->newLiteralNode("literal", STRING)));
        $this->assertTrue(Check::isStatement($this->model->newStatement(new BlankNode("id"), new Resource(NS . "pred"), new BlankNode("id"))));

        $this->assertEquals($this->model->newBlankNode()->getId(), BNODE . "4");
    }

    public function testSize() {

        $this->assertTrue($this->model->size() == 0);

        $statement = new Statement(new Resource(NS . "test"), new Resource(NS . "pred"), new LiteralNode("literal"));
        $this->model->add($statement);

        $this->assertTrue($this->model->size() == 1);
    }

    public function testIsEmpty() {

        $this->assertTrue($this->model->isEmpty());

        $statement = new Statement(new Resource(NS . "test"), new Resource(NS . "pred"), new LiteralNode("literal"));
        $this->model->add($statement);

        $this->assertFalse($this->model->isEmpty());
    }

    /**
     * @expectedException APIException
     */
    public function testRemoveError1() {

        $this->model->remove(null);
    }

    public function testRemoveError2() {

        $this->assertFalse($this->model->remove(new Resource(NS . "test")));
    }

    /**
     * @expectedException APIException
     */
    public function testRemoveError3() {

        $this->model->remove(new LiteralNode("test"));
    }

    public function testRemoveError4() {

        $res = $this->model->newResource("test")->addProperty(new Resource(NS . "pred"), new LiteralNode("literal"));
        $this->model->add($res);

        $res1 = $this->model->newResource("test2")->addProperty(new Resource(NS . "pred"), new LiteralNode("literal"));
        $this->assertFalse($this->model->remove($res1));
    }

    public function testRemoveSuccess1() {

        $statement = new Statement(new Resource(NS . "test"), new Resource(NS . "pred"), new LiteralNode("literal"));

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

        $res = new Resource(NS . "test");

        $res->addProperty(new Resource(NS . "pred"), new LiteralNode("literal"));

        $this->assertTrue($this->model->size() == 0);
        $this->assertTrue($this->model->add($res));
        $this->assertEquals($this->model->size(), 1);
        $this->assertTrue($this->model->search($res) != null);
        $this->assertTrue($this->model->remove($res));
        $this->assertEquals($this->model->size(), 0);
        $this->assertTrue($this->model->search($res) == null);
    }

    public function testRemoveSuccess3() {

        $res = $this->model->newResource("test")
                ->addProperty($this->model->newResource("pred1"), new LiteralNode("literal1"))
                ->addProperty($this->model->newResource("pred2"), new LiteralNode("literal2"))
                ->addProperty($this->model->newResource("pred3"), $this->model->newResource("test2")
                ->addProperty($this->model->newResource("pred"), new LiteralNode("litera3"))
                ->addProperty($this->model->newResource("pred2"), new LiteralNode("litera4"))
        );

        $this->model->add($res);

        $this->assertEquals($this->model->size(), 5);
        $this->assertTrue($this->model->search($res) != null);

        $this->model->remove($res);

        $this->assertEquals($this->model->size(), 0);
        $this->assertTrue($this->model->search($res) == null);
    }

    public function testToString() {

        $res = $this->model->newResource("test")->addProperty(new Resource(NS . "pred"), new LiteralNode("literal"));

        $this->model->add($res);

        $this->assertTrue(Check::isString($this->model->toString()));
        $this->assertTrue(Check::isString($this->model->toString("rdf")));
        $this->assertTrue(Check::isString($this->model->toString("nt")));
        $this->assertTrue(Check::isString($this->model->toString("turtle")));
        $this->assertTrue(Check::isString($this->model->toString("json")));
        $this->assertTrue(Check::isString($this->model->toHTML()));
    }

    /**
     * @expectedException APIException
     */
    public function testToStringError() {

        $res = $this->model->newResource("test")->addProperty(new Resource(NS . "pred"), new LiteralNode("literal"));

        $this->model->add($res);

        $this->assertTrue(Check::isString($this->model->toString("wathever")));
    }

    public function testStatements() {
        $res = $this->model->newResource("test")
                ->addProperty($this->model->newResource("pred1"), new LiteralNode("literal1"))
                ->addProperty($this->model->newResource("pred2"), new LiteralNode("literal2"))
                ->addProperty($this->model->newResource("pred3"), $this->model->newResource("test2")
                ->addProperty($this->model->newResource("pred"), new LiteralNode("litera3"))
                ->addProperty($this->model->newResource("pred2"), new LiteralNode("litera4"))
        );

        $this->model->add($res);

        $this->assertEquals(count($this->model->getStatements()), 5);
        $this->assertEquals(count($this->model->getStatements()), count($this->model->getTripples()));
    }
    
    public function testSLtoRLSuccess1() {
        
        $res = $this->model->newResource("test")
                ->addProperty($this->model->newResource("pred1"), new LiteralNode("literal1"))
                ->addProperty($this->model->newResource("pred2"), new LiteralNode("literal2"))
                ->addProperty($this->model->newResource("pred3"), $this->model->newResource("test2"))
                ->addProperty($this->model->newResource("pred4"), $this->model->newResource("test2")
                ->addProperty($this->model->newResource("pred"), new LiteralNode("litera3"))
                ->addProperty($this->model->newResource("pred2"), new LiteralNode("litera4"))
        );

        $this->model->add($res);
        
        $sL = $this->model->getStatements();
        
        $rL = $this->model->statementListToResourceList($sL);
        
        $this->assertTrue(Check::isArray($rL));
        
    }
    
        public function testRLtoSLSuccess1() {
        
        $res = $this->model->newResource("test")
                ->addProperty($this->model->newResource("pred1"), new LiteralNode("literal1"))
                ->addProperty($this->model->newResource("pred2"), new LiteralNode("literal2"))
                ->addProperty($this->model->newResource("pred3"), $this->model->newResource("test2"))
                ->addProperty($this->model->newResource("pred4"), $this->model->newResource("test2")
                ->addProperty($this->model->newResource("pred"), new LiteralNode("litera3"))
                ->addProperty($this->model->newResource("pred2"), new LiteralNode("litera4"))
        );

        $sL = $this->model->resourceListToStatementList(array($res));
        
        $this->assertTrue(Check::isArray($sL));
        
    }

    protected function tearDown() {

        $this->model = null;
    }

}

?>