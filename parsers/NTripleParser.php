<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com>
 *
 * @name        NTripleParser.php
 * @version     2011-08-31
 * @package     parsers
 * @access      public
 *
 * Description  Parser for N-Triple. Not whole gramma is possible. 
 *              For more info see: http://www.w3.org/2001/sw/RDFCore/ntriples/
 *
 * -----------------------------------------------------------------------------
 */
class NTripleParser implements IParser {

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
     * Transforms the model in to a string in nt notation
     *
     * @param Model $model
     * @return Model
     * @throws APIException
     */
    public function transform($file, &$model) {

        if (!Check::isModel($model))
            throw new APIException(API_ERROR_MODEL);
        
        $handle = fopen($file, "r");

        if ($handle) {

            while (($line = fgets($handle, 4096)) !== false) {
                
                if (strpos($line, "#") === false)
                    $model->add($this->saveAsStatements($line));
            }

//            if (!feof($handle)) {
//                throw new APIException(API_ERROR . "Unexpected fgets() fail");
//            }

            fclose($handle);
        }

        return $model;
    }

    /**
     * Since N-Tripple notation has only statements as lines, this functions 
     * transforms a line into a statement
     *
     * @param string $line
     * @return Statement 
     */
    public function saveAsStatements($line) {

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

            $subjectTXT = substr($subjectTXT, 1, -1);
            $subject = new Resource($subjectTXT);
        } else {

            $subjectTXT = substr($subjectTXT, 2);
            $subject = new BlankNode($subjectTXT);
        }

        // predicate

        $predicateTXT = substr($predicateTXT, 1, -1);
        $predicate = new Resource($predicateTXT);

        // object

        $pos = strpos($objectTXT, "_:");

        if ($pos === false) {

            $pos = strpos($objectTXT, "\"");

            if ($pos === false) {

                $objectTXT = substr($objectTXT, 1, -1);
                $object = new Resource($objectTXT);
            } else {

                $objPieces1 = explode("^^", $objectTXT);

                $type = (Check::isArray($objPieces1)) ? substr($objPieces1[1], 1, -1) : "";
                $literal = (Check::isArray($objPieces1)) ? $objPieces1[0] : $objectTXT;

                $objPieces2 = explode("@", $literal);

                $literal = (Check::isArray($objPieces2)) ? $objPieces2[0] : $literal;
                $language = (Check::isArray($objPieces2)) ? $objPieces2[1] : "";

                $literal = substr($literal, 1, -1);

                $object = new LiteralNode($literal, $type, $language);
            }
        } else {

            $objectTXT = substr($objectTXT, 2);
            $object = new BlankNode($objectTXT);
        }

        return new Statement($subject, $predicate, $object);
    }

}

?>
