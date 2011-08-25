<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        RdfXmlParser.php
 * @version     2011-08-22
 * @package     parsers
 * @access      public
 * 
 * Description  Parser for RDF/XML
 * 
 * -----------------------------------------------------------------------------
 */
class RDFXMLParser implements IParser {

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
     * The path of the xml file
     *
     * @var string
     */
    private $file;
    
    /**
     * Constructor
     */
    function __construct() {
        
    }

        /**
     * Loads an RDFXML-file into the a model an returns it;
     *
     * @param string $file 
     * @param Model $model
     * @return bool
     */
    public function parse($file, &$model) {
        
        if(!Check::isString($file))
            throw new APIException(API_ERROR."The filename need to be a string!");

        if (!file_exists($file))
                throw new APIException (API_ERROR."The file to parse does not exist!");
        
        if (0 == filesize($file))
            throw new APIException(API_ERROR . "File appears to be empty!");
        
        $model = $this->transform($file, $model);
        
        return true;
    }

    /**
     * Transforms a file to a ERP model
     *
     * @return Model
     * @throws APIException
     */
    private function transform($file, &$model) {
        
        $this->file = $file;
        $this->model = $model;
        
        // load the xml file
        $this->initDom();
        
        // instantiate the xpath
        $this->initXPath();
        
        // add namespaces to the model
        $this->addNamespaces();

        $subjectNodes = $this->getSubjectNodes();

        if (empty($subjectNodes))
            throw new APIException(API_ERROR . "Could not parse document");

        foreach ($subjectNodes as $node)
            $this->saveAsStatements($node);

        return $this->model;
    }

    /**
     * Sets up the Dom Document 
     * 
     * @throws APIException
     */
    private function initDom() {
        
        try {

        $dom = new DomDocument();
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;
        $dom->load($this->file);
        $this->dom = $dom;
        
        } catch (Exception $e) {
            throw new APIException($e->getMessage());
        }
    }

    /**
     * Sets up the XPath
     */
    private function initXPath() {
        $xpath = new DOMXpath($this->dom);
        $xpath->registerNamespace(RDF_PREFIX, RDF_NS);

        $this->xpath = $xpath;
    }

    /**
     * Adds namespaces of the docment to the model.
     */
    private function addNamespaces() {

        foreach ($this->xpath->query('namespace::*') as $node) {

            if ($node->prefix != RDF_PREFIX && $node->prefix != XML_PREFIX && $node->prefix != XML_DECLARATION_PREFIX) {
                $this->model->addNamespace($node->prefix, $node->nodeValue);
            }
        }
    }

    /**
     * Returns a list of all nodes of <rdf:Description>
     *
     * @return DOMNodeList
     */
    private function getSubjectNodes() {

        $query = "//" . RDF_PREFIX . ":" . RDF_DESCRIPTION;

        $result = $this->xpath->query($query);

        if ($result->length != 0)
            return $result;
        else
            return null;
    }

    /** 
     * Takes a <rdf:Description> node and saves it as statements
     *
     * @param DOMNode $node 
     */
    private function saveAsStatements($node) {

        // subject
        $subject = null;

        foreach ($node->attributes as $name => $attr) {

            if ($attr->nodeName == RDF_PREFIX . ":" . RDF_ABOUT) {
                $subject = new Resource($attr->nodeValue);
                break;
            } else if ($attr->nodeName == RDF_PREFIX . ":" . RDF_ID) {
                $subject = new BlankNode($attr->nodeValue);
                break;
            } else {
                // ignore
            }
        }

        // predicate & object

        foreach ($node->childNodes as $child) {

            // predicate is resource
            $prefix = $child->prefix;
            $name = $child->localName;
            $ns = $this->model->getNamespace($prefix);

            $predicate = new Resource($ns . $name);

            //object = literal node
            if (!empty($child->nodeValue)) {
                
                $type = STRING;
                
                foreach ($child->attributes as $name => $attr) {

                    if ($attr->nodeName == XML_PREFIX . ":" . XML_LANG)
                        $lang = $attr->nodeValue;
                    if ($attr->nodeName == RDF_PREFIX . ":" . RDF_DATATYPE)
                        $type = $attr->nodeValue;
                }
                
                $object = new LiteralNode($child->nodeValue, $type, $lang);
                
            }
            //object = resource or blank node    
            else {
                
                foreach ($child->attributes as $name => $attr) {

                    if ($attr->nodeName == RDF_PREFIX . ":" . RDF_RESOURCE) {
                        $object = new Resource($attr->nodeValue);
                    }
                    
                    if ($attr->nodeName == RDF_PREFIX . ":" . RDF_ID)
                        $object = new BlankNode($attr->nodeValue);
                }
            }
            
            // TODO: implement way if attribute rdf:resource is not used
            
            $this->model->add(new Statement($subject, $predicate, $object));
            
        }
    }

}

?>
