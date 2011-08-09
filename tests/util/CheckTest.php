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
 * @name        CheckTest.php
 * @version     2011-08-06
 * @package     tests
 * @access      public
 * 
 * Description  here
 * 
 * -----------------------------------------------------------------------------
 */
class CheckTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        
    }

    public function testAcceptSubject() {

        $this->assertTrue(Check::isSubject(new Resource("test")));
        $this->assertTrue(Check::isSubject(new BlankNode()));

        // adding a full ressource with properties
        $res = new Resource("test");
        $statement = $res->addProperty(new Resource("pred"), new LiteralNode("Test"));

        $this->assertTrue(Check::isSubject($res));
    }

    public function testAcceptPredicate() {

        $this->assertTrue(Check::isPredicate(new Resource("test")));
    }

    public function testAcceptObject() {

        $this->assertTrue(Check::isObject(new Resource("test")));
        $this->assertTrue(Check::isObject(new LiteralNode("literal")));
        $this->assertTrue(Check::isObject(new BlankNode()));

        // adding a full ressource with properties
        $res = new Resource("test");
        $statement = $res->addProperty(new Resource("pred"), new LiteralNode("Test"));

        $this->assertTrue(Check::isObject($res));
    }

    public function testErrorSubject() {

        $this->assertFalse(Check::isSubject("this is a string"));
        $this->assertFalse(Check::isSubject(new LiteralNode("subject can't be a literal")));

        // adding a full ressource with properties
        $statement = new Statement(new Resource("test"), new Resource("pred"), new LiteralNode("Test"));

        $this->assertFalse(Check::isSubject($statement));
    }

    public function testErrorPredicate() {

        $this->assertFalse(Check::isPredicate("this is a string"));
        $this->assertFalse(Check::isPredicate(new LiteralNode("predicate can't be a literal")));
        $this->assertFalse(Check::isPredicate(new BlankNode()));

        $res = new Resource("test");
        $statement = $res->addProperty(new Resource("pred"), new LiteralNode("Test"));

        $this->assertFalse(Check::isPredicate($res));
        $this->assertFalse(Check::isPredicate($statement));
    }

    public function testErrorObject() {

        $this->assertFalse(Check::isObject("this is a string"));
    }

    protected function tearDown() {
        
    }

}

?>
