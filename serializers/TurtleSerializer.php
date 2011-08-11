<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        TurtleSerializer.php
 * @version     2011-08-11
 * @package     serializers
 * @access      public
 * 
 * Description  Serializer for Turtle
 * 
 * -----------------------------------------------------------------------------
 */
class TurtleSerializer {
    
     /**
     * The content that will be written into the output file
     *
     * @var string 
     */
    private $content;
    
    /**
     * The model to serialize
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
     * Saves the model in turtle notation in to a file
     *
     * @param string $file
     * @param Model $model
     * @return bool 
     * @throws APIException
     */
    public function serialize($file, $model) {

        if (!Check::isString($file))
            throw new APIException(API_ERROR_STRING);

        $this->transform($model);

        $handle = fopen($file, 'w');

        fwrite($handle, $this->content);
        fclose($handle);

        $this->content = null;
        $this->model = null;
        
        return true;
    }

    /**
     * Returns the model as a string in turtle notation
     * 
     * @param type $model
     * @return string 
     * @throws APIException
     */
    public function serializeToString($model) {
        
        $cont = $this->transform($model);
        $this->content = null;
        $this->model = null;
        
        return $cont;
    }

    /**
     * Transforms the model in to a string in turtle notation
     *
     * @param Model $model
     * @return string 
     * @throws APIException
     */
    public function transform($model) {

        if (!Check::isModel($model))
            throw new APIException(API_ERROR_MODEL);

        if ($model->isEmpty())
            throw new APIException(API_ERROR . "The model is empty and cant be serialized");
        
        $this->model = $model;
        
        foreach ($model->getNamespaces() as $prefix => $ns) {
           $this->content.= "@prefix ".$prefix.":". "<".$ns."> .\n";
        }

        foreach ($model->getStatements() as $statement) {

            $subject = $statement->getSubject();
            $predicate = $statement->getPredicate();
            $object = $statement->getObject();

            if ($subject instanceof BlankNode) {
                $subjectTXT = '_:' . $subject->getURI();
            } else {
                
                $prefix = $this->getPrefix($subject->getNamespace());
                
                $subjectTXT = ($prefix) ? $prefix.":".$subject->getName() : '<' . $subject->getUri() . '>';
            }

            $prefix = $this->getPrefix($predicate->getNamespace());
            $predicateTXT = ($prefix) ? $prefix.":".$predicate->getName() : '<' . $predicate->getUri() . '>';

            if ($object instanceof BlankNode) {
                $objectTXT = '_:' . $object->getId();
            } else if ($object instanceof Resource) {
                $prefix = $this->getPrefix($object->getNamespace());
                $objectTXT = ($prefix) ? $prefix.":".$object->getName() : '<' . $object->getUri() . '>';
            } else {
                $objectTXT = '"' . $object->getLiteral() . '"';

                if ($object->hasLanguage())
                    $objectTXT.='@' . $object->getLanguage();

                if ($object->hasDatatype())
                    $objectTXT.='^^<' . $object->getDatatype() . ">";
            }

            $this->content.= $subjectTXT . ' ' . $predicateTXT . ' ' . $objectTXT . ' .';
            $this->content.= "\n";
        }

        return $this->content;
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
