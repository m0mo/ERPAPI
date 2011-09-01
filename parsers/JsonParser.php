<?php

/**
 * --------------------------------------------------------------------
 * ERP API
 * --------------------------------------------------------------------
 *
 * Parser for Json.
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com>
 * @name        JsonParser.php
 * @version     2011-08-22
 * @package     parsers
 * @access      public
 * 
 * --------------------------------------------------------------------
 */
class JsonParser implements IParser {

    /**
     * Constructor
     */
    function __construct() {
        
    }

    /**
     * Loads a file into the model
     *
     * @param string $file
     * @param Model $model
     * @return bool
     * @throws APIException
     */
    public function parse($file, &$model) {

        if (!Check::isString($file))
            throw new APIException(API_ERROR . "The filename need to be a string!");

        if (!file_exists($file))
            throw new APIException(API_ERROR . "The file to parse does not exist!");


        $this->transform($file, $model);

        return true;
    }

    /**
     * Transforms the file content into a model
     *
     * @param string $file
     * @param Model $model 
     * @throws APIException
     */
    private function transform($file, &$model) {

        if (!Check::isModel($model))
            throw new APIException(API_ERROR_MODEL);

        if (0 == filesize($file)) {
            throw new APIException(API_ERROR . "File appears to be empty!");
        }

        $jsonString = "";

        $handle = fopen($file, "r");

        if ($handle) {

            while (($line = fgets($handle, 4096)) !== false) {
                $jsonString.=$line;
            }

//            if (!feof($handle)) {
//                throw new APIException(API_ERROR . "Unexpected fgets() fail");
//            }

            fclose($handle);
        }

        $jsonModel = json_decode($jsonString, true);

        // throws an excpetion if json model was corrupt
        if (!is_array($jsonModel)) {
            throw new APIException('error in json string');
        }

        foreach ($jsonModel as $s => $props) {

            $subject = (strpos($s, '_:') === 0) ? new BlankNode(substr($s, 2)) : new Resource($s);

            foreach ($props as $p => $os) {

                $predicate = new Resource($p);

                foreach ($os as $o) {

                    switch ($o["type"]) {
                        case "uri":
                            $object = new Resource($o['value']);
                            break;

                        case "bnode":
                            $object = new BlankNode(substr($o['value'], 2));
                            break;

                        case "literal":
                            $datatype = (isset($o['datatype'])) ? $o['datatype'] : '';
                            $language = (isset($o['lang'])) ? $o['lang'] : '';

                            $object = new LiteralNode($o['value'], $datatype, $language);
                            break;
                    }

                    $model->add(new Statement($subject, $predicate, $object));
                }
            }
        }
    }

}

?>
