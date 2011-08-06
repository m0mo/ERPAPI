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
 * @name        StatementTest.php
 * @version     0.1.5 (Aug 6, 2011)
 * @package     tests
 * @access      public
 * 
 * Description  here
 * 
 * -----------------------------------------------------------------------------
 */
class StatementTest extends PHPUnit_Framework_TestCase {

    private $statement;
    private $ns = "http://thisIsMyUri/test/";
    private $subj;
    private $pred;
    private $obj;

    protected function setUp() {

        $this->subj = new Resource($this->ns . "Subj");
        $this->pred = new Resource($this->ns . "Pred");
        $this->obj = new LiteralNode("Literal");

        $this->statement = new Statement($this->subj, $this->pred, $this->obj);
    }

    public function testGenerateStatement() {
        $statement1 = new Statement($this->subj, $this->pred, $this->obj);
        $statement2 = new Statement($this->subj, $this->pred, new Resource($this->ns . "Res1"));

        $this->assertTrue(is_a($statement1, Statement));
        $this->assertTrue(is_a($statement2, Statement));
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

        $statement = new Statement($this->subj, new BlankNode(), $this->obj);
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

        $statement = new Statement(new Resource($this->ns . "Res3"), $this->pred, $this->statement);
    }

    public function testAcceptedSubject1() {

        $statement = new Statement(new Resource($this->ns . "Res2"), $this->pred, $this->obj);
        $this->assertTrue(is_a($statement, Statement));
    }

    public function testAcceptedSubject2() {

        $statement = new Statement(new BlankNode(), $this->pred, $this->obj);
        $this->assertTrue(is_a($statement, Statement));
    }

    public function testAcceptedPredicate() {

        $statement = new Statement($this->subj, new Resource($this->ns . "Pred2"), $this->obj);
        $this->assertTrue(is_a($statement, Statement));
    }

    public function testAcceptedObject1() {

        $statement = new Statement($this->subj, $this->pred, new LiteralNode("test"));
        $this->assertTrue(is_a($statement, Statement));
    }

    public function testAcceptedObject2() {

        $statement = new Statement($this->subj, $this->pred, new Resource("test"));
        $this->assertTrue(is_a($statement, Statement));
    }

    protected function tearDown() {
        
    }

}

?>