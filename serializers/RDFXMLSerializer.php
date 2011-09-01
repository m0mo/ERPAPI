<?php

/**
 * --------------------------------------------------------------------
 * ERP API 
 * --------------------------------------------------------------------
 *
 * Serializer for RDF/XML
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * @name        RDFXMLSerializer.php
 * @version     2011-08-12
 * @package     serializers
 * @access      public
 * 
 * --------------------------------------------------------------------
 */
class RDFXMLSerializer implements ISerializer {

    /**
     * The root element
     *
     * @var DOMNode
     */
    private $rdf;

    /**
     * The XML dom document
     *
     * @var DOMDocument 
     */
    private $dom;

    /**
     * XPath
     *
     * @var DOMXpath 
     */
    private $xpath;

    /**
     * The model to process
     *
     * @var Model 
     */
    private $model;

    /**
     * Constructor
     */
    function __construct() {
        
    }

    /**
     * Returns the RDF/XML as a string
     *
     * @param Model $model
     * @return string
     * @throws APIException
     */
    public function serializeToString($model) {

        $cont = $this->transform($model)->saveXML();
        
        $this->dom = null;
        $this->rdf = null;
        $this->xpath = null;
        $this->model = null;
        
        return $cont;
    }

    /**
     * Serializes the model to a RDF/XML file
     *
     * @param string $file
     * @param Model $model 
     * @return bool
     * @throws APIException
     */
    public function serialize($file, $model) {

        if (!Check::isString($file))
            throw new APIException(API_ERROR_STRING);

        $this->transform($model)->save($file);
        
        $this->dom = null;
        $this->rdf = null;
        $this->xpath = null;
        $this->model = null;
        
        return true;
    }

    /**
     * Returns a DOM document of the model;
     *
     * @param Model $model
     * @return DOMDocument 
     * @throws APIException
     */
    private function transform($model) {

        if (!Check::isModel($model))
            throw new APIException(API_ERROR_MODEL);

        if ($model->isEmpty())
            throw new APIException(API_ERROR . "The model is empty and cant be serialized");

        $this->model = $model;
        $this->initDom();
        $this->initXPath();

        foreach ($this->model->getStatements() as $statement)
            $this->handleStatement($statement);

        return $this->dom;
    }

    /**
     * Sets up the Dom Document 
     */
    private function initDom() {

        $this->dom = new DOMDocument('1.0');
        $this->dom->formatOutput = true;
        $this->dom->preserveWhiteSpace = false;

        $this->rdf = $this->dom->createElementNS(RDF_NS, RDF_PREFIX . ":" . RDF_RDF);
        $this->rdf->setAttributeNS(XML_NS, XML_DECLARATION_PREFIX . ":" . RDF_PREFIX, RDF_NS);

        foreach ($this->model->getNamespaces() as $key => $ns) {
            $this->rdf->setAttributeNS(XML_NS, XML_DECLARATION_PREFIX . ":" . $key, $ns);
        }

        $this->dom->appendChild($this->rdf);
    }

    /**
     * Sets up the XPath
     */
    private function initXPath() {
        $xpath = new DOMXpath($this->dom);

        $xpath->registerNamespace(RDF_PREFIX, RDF_NS);

        foreach ($this->model->getNamespaces() as $key => $ns) {
            $xpath->registerNamespace($key, $ns);
        }

        $this->xpath = $xpath;
    }

    /**
     * Handles a singe statement and adds it to the dom tree
     *
     * @param Statement $statement 
     */
    private function handleStatement($statement) {

        if (!Check::isStatement($statement))
            throw new APIException(API_ERROR_STATEMENT);

        $subject = $statement->getSubject();
        $predicate = $statement->getPredicate();
        $object = $statement->getObject();

        $node = $this->getSubjectNode($subject);
        $node = $this->addProperties($node, $predicate, $object);

        $this->rdf->appendChild($node);
    }

    /**
     * Returns the node that represents the subject, or creates a new one
     *
     * @param Resource $subject
     * @return DOMNode 
     */
    private function getSubjectNode($subject) {

        $query = "//" . RDF_PREFIX . ":" . RDF_DESCRIPTION . "[@" . RDF_PREFIX . ":";
        $query.= (!Check::isBlankNode($subject)) ? RDF_ABOUT : RDF_ID;
        $query.= "='" . $subject->getUri() . "']";

        $result = $this->xpath->query($query);

//        echo "\n".$query ." ". $result->length ."\n";

        if ($result->length > 0)
            return $result->item(0);

        $node = $this->dom->createElementNS(RDF_NS, RDF_PREFIX . ":" . RDF_DESCRIPTION);

        if (!Check::isBlankNode($subject))
            $node->setAttribute(RDF_PREFIX . ":" . RDF_ABOUT, $subject->getUri());
        else
            $node->setAttribute(RDF_PREFIX . ":" . RDF_ID, $subject->getUri());

        return $node;
    }

    /**
     * Adds the properties to the node
     *
     * @param DOMNode $node
     * @param Resource $predicate
     * @param Resource|LiteralNode $object 
     * @return DOMNode same as imput node, but with the new properties
     */
    private function addProperties($node, $predicate, $object) {

        $child = $this->dom->createElement($this->getPrefix($predicate->getNamespace()) . ":" . $predicate->getName());

        if ($object instanceof LiteralNode) {
            $child->nodeValue = $object->getLiteral();
            $child->setAttribute(RDF_PREFIX . ":" . RDF_DATATYPE, RDFS_NS . $object->getDatatype());

            $lang = $object->getLanguage();

            if (!empty($lang)) {
                $child->setAttribute(XML_PREFIX . ":" . XML_LANG, $lang);
            }
        }

        if ($object instanceof Resource) {

            if ($object instanceof BlankNode) {
                if ($object->getUri() != null)
                    $child->setAttribute(RDF_PREFIX . ":" . RDF_ID, $object->getUri());
            } else {
                $child->setAttribute(RDF_PREFIX . ":" . RDF_RESOURCE, $object->getUri());
            }
        }

        $node->appendChild($child);
        return $node;
    }

    /**
     * Returns the prefix for a namespace
     *
     * @param string $namespace
     * @return string 
     */
    private function getPrefix($namespace) {
        $ns = array_flip($this->model->getNamespaces());

        return $ns[$namespace];
    }

}

?>
