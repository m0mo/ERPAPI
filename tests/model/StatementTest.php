<?php

require_once "settings.php";

/**
 * --------------------------------------------------------------------
 * ERP API Test
 * --------------------------------------------------------------------
 *
 * Testing the Statement class
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * @name        StatementTest.php
 * @version     2011-08-09
 * @package     tests/model
 * @access      public
 * 
 * --------------------------------------------------------------------
 */
class StatementTest extends PHPUnit_Framework_TestCase {

    private $statement;
    private $subj;
    private $pred;
    private $obj;

    protected function setUp() {

        $this->subj = new Resource(NS . "Subj");
        $this->pred = new Resource(NS . "Pred");
        $this->obj = new LiteralNode("Literal");

        $this->statement = new Statement($this->subj, $this->pred, $this->obj);
    }

    public function testGenerateStatement() {
        $statement1 = new Statement($this->subj, $this->pred, $this->obj);
        $statement2 = new Statement($this->subj, $this->pred, new Resource(NS . "Res1"));

        $this->assertTrue(Check::isStatement($statement1));
        $this->assertTrue(Check::isStatement($statement2));
        $this->assertFalse($statement1->equals($statement2));
    }

    public function testGetterMethods() {

        $this->assertTrue($this->statement->getSubject()->equals($this->subj));
        $this->assertTrue($this->statement->getObject()->equals($this->obj));
        $this->assertTrue($this->statement->getPredicate()->equals($this->pred));
    }

    /**
     * @expectedException APIException
     */
    public function testErrorSubject1() {

        $statement = new Statement("not a node", $this->pred, $this->obj);
    }

    /**
     * @expectedException APIException
     */
    public function testErrorSubject2() {

        $statement = new Statement(new LiteralNode("Test"), $this->pred, $this->obj);
    }

    /**
     * @expectedException APIException
     */
    public function testErrorPredicate1() {

        $statement = new Statement($this->subj, "not a node", $this->obj);
    }

    /**
     * @expectedException APIException
     */
    public function testErrorPredicate2() {

        $statement = new Statement($this->subj, new LiteralNode("Test"), $this->obj);
    }

    /**
     * @expectedException APIException
     */
    public function testErrorPredicate3() {

        $statement = new Statement($this->subj, new BlankNode("id"), $this->obj);
    }

    /**
     * @expectedException APIException
     */
    public function testErrorObject1() {

        $statement = new Statement($this->subj, $this->pred, "not a node");
    }

    /**
     * @expectedException APIException
     */
    public function testErrorObject2() {

        $statement = new Statement(new Resource(NS . "Res3"), $this->pred, $this->statement);
    }

    public function testAcceptedSubject1() {

        $statement = new Statement(new Resource(NS . "Res2"), $this->pred, $this->obj);
        $this->assertTrue(Check::isStatement($statement));
    }

    public function testAcceptedSubject2() {

        $statement = new Statement(new BlankNode("id"), $this->pred, $this->obj);
        $this->assertTrue(Check::isStatement($statement));
    }

    public function testAcceptedPredicate() {

        $statement = new Statement($this->subj, new Resource(NS . "Pred2"), $this->obj);
        $this->assertTrue(Check::isStatement($statement));
    }

    public function testAcceptedObject1() {

        $statement = new Statement($this->subj, $this->pred, new LiteralNode("test"));
        $this->assertTrue(Check::isStatement($statement));
    }

    public function testAcceptedObject2() {

        $statement = new Statement($this->subj, $this->pred, new Resource(NS."test"));
        $this->assertTrue(Check::isStatement($statement));
    }

    public function testToString() {
        $statement = new Statement($this->subj, $this->pred, $this->obj);
        $this->assertTrue(Check::isString($statement->toString()));
    }

    protected function tearDown() {
        
    }

}

?>