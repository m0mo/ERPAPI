<?php

/**
 * --------------------------------------------------------------------
 * ERP API
 * --------------------------------------------------------------------
 *
 * Serializer for JSON
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com>
 * @name        JsonParser.php
 * @version     2011-08-12
 * @package     serializers
 * @access      public
 *
 * --------------------------------------------------------------------
 */
class JsonSerializer implements ISerializer {

    /**
     * The content that will be written into the output file
     *
     * @var string
     */
    private $content;

    /**
     * constructor
     */
    function __construct() {
        
    }

    /**
     * Saves the model in json notation in to a file
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

        return true;
    }

    /**
     * Returns the model as a string in json notation
     *
     * @param Model $model
     * @return string
     * @throws APIException
     */
    public function serializeToString($model) {

        $cont = $this->transform($model);
        $this->content = null;

        return $cont;
    }

    /**
     * Transforms the model in to a string in json notation
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

        // create the root json object (root object)
        $this->content = '{';

        $subjects = array();
        
        foreach ($model->getStatements() as $statement)
            $subjects[$statement->getSubject()->getUri()][] = $statement;

        ksort($subjects);
        $i = 0;
        foreach ($subjects as $key => $statements) {

            $this->content.= ($i != 0) ? "," : "";
            $i++;
            
            $subject = $statements[0]->getSubject();

            if ($subject instanceof BlankNode)
                $this->content.= '"_:' . $this->jsonEscape($key) . '":';
            else
                $this->content.= '"' . $this->jsonEscape($key) . '":';

            $this->content.= '{';
            
            $predicates = array();

            foreach ($statements as $statement)
                $predicates[$statement->getPredicate()->getUri()][] = $statement->getObject();

            ksort($predicates);

            $j = 0;
            foreach ($predicates as $key => $objects) {

                $this->content.= ($j != 0) ? "," : "";
                $j++;
                $this->content.= '"' . $this->jsonEscape($key) . '":';

                // create a json array (value array)
                $this->content.= '[';

                $k = 0;
                foreach ($objects as $object) {

                    $this->content.= ($k != 0) ? "," : "";
                    $k++;
                    $this->content.= '{';

                    if ($object instanceof BlankNode) {
                        $this->content.= '"value":"_:' . $this->jsonEscape($object->getId()) . '",';
                        $this->content.= '"type":"bnode"';
                    } else if ($object instanceof LiteralNode) {
                        $this->content.= '"value":"' . $this->jsonEscape($object->getLiteral()) . '",';
                        $this->content.= '"type":"literal"';

                        if ($object->hasLanguage())
                            $this->content.= ',"lang":"' . $this->jsonEscape($object->getLanguage()) . '"';

                        if ($object->hasDatatype())
                            $this->content.= ',"datatype":"' . $this->jsonEscape($object->getDatatype()) . '"';
                    
                        
                    } else {
                        $this->content.= '"value":"' . $this->jsonEscape($object->getUri()) . '",';
                        $this->content.= '"type":"uri"';
                    }
                    $this->content.= '}';
                }
                $this->content.= ']';
            }
            $this->content.= '}';
        }
        $this->content.= '}';

        return $this->content;
    }

    /**
     * This functions escapes some characters for json
     *
     * @param string $param
     * @return string escaped
     */
    private function jsonEscape($param) {

        $from = array("\\", "\r", "\t", "\n", '"', "\b", "\f", "/");
        $to = array('\\\\', '\r', '\t', '\n', '\"', '\b', '\f', '\/');
        return str_replace($from, $to, $param);
    }

}

?>
