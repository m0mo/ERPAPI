<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com>
 *
 * @name        TurtleSerializer.php
 * @version     2011-08-12
 * @package     serializers
 * @access      public
 *
 * Description  Parser for Turtle. Not whole grammar is possible.
 *              for more info see: http://www.w3.org/TeamSubmission/turtle/
 *
 * -----------------------------------------------------------------------------
 */
class TurtleParser implements IParser {

    /**
     * The model to parse
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
        
        if (0 == filesize($file))
            throw new APIException(API_ERROR . "File appears to be empty!");


        $this->transform($file, $model);

        return true;
    }

    /**
     * Transforms the model in to a string in turtle notation
     *
     * @param Model $model
     * @return Model
     * @throws APIException
     */
    private function transform($file, &$model) {

        if (!Check::isModel($model))
            throw new APIException(API_ERROR_MODEL);

        $this->model = &$model;

        $handle = fopen($file, "r");

        if ($handle) {

            while (($line = fgets($handle, 4096)) !== false) {
                if (strpos($line, "#") === false)
                    $this->handleLine($line);
            }

//            if (!feof($handle)) {
//                throw new APIException(API_ERROR . "Unexpected fgets() fail");
//            }

            fclose($handle);
        }

        return $this->model;
    }

    /**
     * Handels a line of the file
     *
     * @param string $line
     */
    private function handleLine($line) {



        $pieces = explode(" ", $line);

        if (count($pieces) < 3)
            throw new APIException(API_ERROR . "The content of the file can not be interpreted");

        if (strpos($pieces[0], "@prefix") !== false)
            $this->saveAsNamespace($line);
        else
            $this->saveAsStatements($line);
    }

    /**
     * If a line is identified as a namespace declaration, this function adds it
     * to the model
     *
     * @param string $line
     */
    private function saveAsNamespace($line) {

        $pieces = explode(" ", $line);

        if (count($pieces) != 3 || strpos($pieces[1], ":") === false)
            throw new APIException(API_ERROR . "The content of the file can not be interpreted");

        $pieces = explode(":", $pieces[1], 2);
        
        if (count($pieces) != 2)
            throw new APIException(API_ERROR . "The content of the file can not be interpreted");

        $prefix = $pieces[0];
        $namespace = substr($pieces[1], 1, -1);

        $this->model->addNamespace($prefix, $namespace);
    }

    /**
     * If a line is identified as a statement decleration, this function adds it
     * to the model
     *
     * @param string $line
     */
    private function saveAsStatements($line) {


        $pieces = explode(" ", $line);

        if (count($pieces) != 4)
            throw new APIException(API_ERROR . "The content of the file can not be interpreted");

        $subjectTXT = $pieces[0];
        $predicateTXT = $pieces[1];
        $objectTXT = $pieces[2];
        // $pieces[2] = "." we don't need this
        // subject

        $pos = strpos($subjectTXT, "_:");

        if ($pos === false) {

            $subjectPieces = explode(":", $subjectTXT);
            $subjectTXT = ($subjectPieces[1]) ? $this->model->getNamespace($subjectPieces[0]) . $subjectPieces[1] : substr($subjectTXT, 1, -1);
            $subject = new Resource($subjectTXT);
        } else {

            $subjectTXT = substr($subjectTXT, 2);
            $subject = new BlankNode($subjectTXT);
        }

        // predicate

        $predicatePieces = explode(":", $predicateTXT);
        $predicateTXT = ($predicatePieces[1]) ? $this->model->getNamespace($predicatePieces[0]) . $predicatePieces[1] : substr($predicateTXT, 1, -1);
        $predicate = new Resource($predicateTXT);

        // object

        $pos = strpos($objectTXT, "_:");

        if ($pos === false) {

            $pos = strpos($objectTXT, "\"");

            if ($pos === false) {
                $objPieces = explode(":", $objectTXT);
                $objectTXT = ($objPieces[1]) ? $this->model->getNamespace($objPieces[0]) . $objPieces[1] : substr($objectTXT, 1, -1);
                $object = new Resource($objectTXT);
            } else {

                $objPieces1 = explode("^^", $objectTXT);

                $type = ($objPieces1[1]) ? substr($objPieces1[1], 1, -1) : "";
                $literal = ($objPieces1[1]) ? $objPieces1[0] : $objectTXT;

                $objPieces2 = explode("@", $literal);

                $literal = ($objPieces2[1]) ? $objPieces2[0] : $literal;
                $language = ($objPieces2[1]) ? $objPieces2[1] : "";

                $literal = substr($literal, 1, -1);

                $object = new LiteralNode($literal, $type, $language);
            }
        } else {

            $objectTXT = substr($objectTXT, 2);
            $object = new BlankNode($objectTXT);
        }

        $this->model->add(new Statement($subject, $predicate, $object));
    }

}

?>
