<?php

require_once "settings.php";

/**
 * --------------------------------------------------------------------
 * ERP API Test
 * --------------------------------------------------------------------
 * 
 * Testing the Check class
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * @name        CheckTest.php
 * @version     2011-08-10
 * @package     tests
 * @access      public
 * 
 * --------------------------------------------------------------------
 */
class CheckTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        
    }

    public function testResourceSuccess() {

        $this->assertTrue(Check::isResource(new Resource(NS . "test")));
        $this->assertTrue(Check::isResource(new BlankNode("id")));
    }

    public function testResourceError() {

        $this->assertFalse(Check::isResource(null));
        $this->assertFalse(Check::isResource("string"));
        $this->assertFalse(Check::isResource(new LiteralNode("test")));
    }

    public function testBlankNodeSuccess() {

        $this->assertTrue(Check::isBlankNode(new BlankNode("id")));
    }

    public function testBlankNodeError() {

        $this->assertFalse(Check::isBlankNode(null));
        $this->assertFalse(Check::isBlankNode("string"));
        $this->assertFalse(Check::isBlankNode(new Resource(NS . "test")));
        $this->assertFalse(Check::isBlankNode(new LiteralNode("test")));
    }

    public function testLiteralNodeSuccess() {

        $this->assertTrue(Check::isLiteralNode(new LiteralNode("test")));
    }

    public function testLiteralNodeError() {

        $this->assertFalse(Check::isLiteralNode(null));
        $this->assertFalse(Check::isLiteralNode("string"));
        $this->assertFalse(Check::isLiteralNode(new Resource(NS . "test")));
        $this->assertFalse(Check::isLiteralNode(new BlankNode("id")));
    }

    public function testIsStringSucess() {
        $this->assertTrue(Check::isString("string"));
    }

    public function testIsStringError() {
        $this->assertFalse(Check::isString(null));
        $this->assertFalse(Check::isString(new Resource(NS . "test")));
        $this->assertFalse(Check::isString(new BlankNode("id")));
        $this->assertFalse(Check::isString(new LiteralNode("test")));
    }

    public function testSubjectSuccess() {

        $this->assertTrue(Check::isSubject(new Resource(NS . "test")));
        $this->assertTrue(Check::isSubject(new BlankNode("id")));

        // adding a full ressource with properties
        $res = new Resource(NS . "test");
        $res->addProperty(new Resource(NS . "pred"), new LiteralNode("Test"));

        $this->assertTrue(Check::isSubject($res));
    }

    public function testPredicateSuccess() {

        $this->assertTrue(Check::isPredicate(new Resource(NS . "test")));
    }

    public function testObjectSuccess() {

        $this->assertTrue(Check::isObject(new Resource(NS . "test")));
        $this->assertTrue(Check::isObject(new LiteralNode("literal")));
        $this->assertTrue(Check::isObject(new BlankNode("id")));

        // adding a full ressource with properties
        $res = new Resource(NS . "test");
        $res->addProperty(new Resource(NS . "pred"), new LiteralNode("Test"));

        $this->assertTrue(Check::isObject($res));
    }

    public function testSubjectError() {

        $this->assertFalse(Check::isSubject("this is a string"));
        $this->assertFalse(Check::isSubject(new LiteralNode("subject can't be a literal")));

        // adding a full ressource with properties
        $statement = new Statement(new Resource(NS . "test"), new Resource(NS . "pred"), new LiteralNode("Test"));

        $this->assertFalse(Check::isSubject($statement));
    }

    public function testPredicateError() {

        $this->assertFalse(Check::isPredicate("this is a string"));
        $this->assertFalse(Check::isPredicate(new LiteralNode("predicate can't be a literal")));
        $this->assertFalse(Check::isPredicate(new BlankNode("id")));

        $res = new Resource(NS . "test");
        $statement = $res->addProperty(new Resource(NS . "pred"), new LiteralNode("Test"));

        $this->assertFalse(Check::isPredicate($res));
        $this->assertFalse(Check::isPredicate($statement));
    }

    public function testIsUri() {

        $this->assertFalse(Check::isUri("test"));
        $this->assertFalse(Check::isUri(NS . "test/"));
        $this->assertFalse(Check::isUri(NS . "test#"));

        $this->assertTrue(Check::isUri(NS . "test"));
        $this->assertTrue(Check::isUri(NS . "test/test2"));
    }

    public function testIsPrefix() {

        $this->assertFalse(Check::isPrefix("+#"));
        $this->assertFalse(Check::isPrefix(NS . "test/"));
        $this->assertFalse(Check::isPrefix(NS . "test#"));

        $this->assertTrue(Check::isPrefix("test"));
        $this->assertTrue(Check::isPrefix("test2"));
        $this->assertTrue(Check::isPrefix(PREFIX));
    }

    public function testIsName() {

        $this->assertFalse(Check::isName("+#"));
        $this->assertFalse(Check::isName(NS . "test/"));
        $this->assertFalse(Check::isName(NS . "test#"));

        $this->assertTrue(Check::isName("test"));
        $this->assertTrue(Check::isName("test2"));
    }
    
        public function testIsPrefixAndName() {

        $this->assertFalse(Check::isPrefixAndName("+#"));
        $this->assertFalse(Check::isPrefixAndName(NS . "test/"));
        $this->assertFalse(Check::isPrefixAndName(NS . ":name"));
        $this->assertFalse(Check::isPrefixAndName(PREFIX . "test"));

        $this->assertTrue(Check::isPrefixAndName(PREFIX.":test"));
    }
    
     public function testIsNamespace() {

        $this->assertFalse(Check::isNamespace("+#"));
        $this->assertFalse(Check::isNamespace(NS . "test"));
        $this->assertFalse(Check::isNamespace(NS . ":name"));
        $this->assertFalse(Check::isNamespace(PREFIX . "test"));

        $this->assertTrue(Check::isNamespace(NS));
    }

    public function testObjectError() {

        $this->assertFalse(Check::isObject("this is a string"));
    }

    protected function tearDown() {
        
    }

}

?>
