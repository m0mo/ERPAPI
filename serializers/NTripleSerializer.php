<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        RDFXMLSerializer.php
 * @version     2011-08-11
 * @package     serializers
 * @access      public
 * 
 * Description  Serializer for RDF/XML
 * 
 * -----------------------------------------------------------------------------
 */
class NTripleSerializer implements ISerializer {

    /**
     * The content that will be written into the output file
     *
     * @var string 
     */
    private $content;

    /**
     * Constructor
     */
    function __construct() {
        
    }

    /**
     * Saves the model in nt notation in to a file
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
     * Returns the model as a string in nt notation
     * 
     * @param type $model
     * @return type 
     * @throws APIException
     */
    public function serializeToString($model) {
        
        $cont = $this->transform($model);
        $this->content = null;
        $this->model = null;
        
        return $cont;
    }

    /**
     * Transforms the model in to a string in nt notation
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

        foreach ($model->getStatements() as $statement) {

            $subject = $statement->getSubject();
            $predicate = $statement->getPredicate();
            $object = $statement->getObject();

            if ($subject instanceof BlankNode) {
                $subjectTXT = '_:' . $subject->getUri();
            } else {
                $subjectTXT = '<' . $subject->getUri() . '>';
            }

            $predicateTXT = '<' . $predicate->getUri() . '>';

            if ($object instanceof BlankNode) {
                $objectTXT = '_:' . $object->getUri();
            } else if ($object instanceof Resource) {
                $objectTXT = '<' . $object->getUri() . '>';
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

}

?>
