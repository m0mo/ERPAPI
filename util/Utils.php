<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        Utils.php
 * @version     2011-08-10
 * @package     util
 * @access      public
 * 
 * Description  This class offers static functions for all kinds of stuff
 * 
 * -----------------------------------------------------------------------------
 */
class Utils {

    /**
     * Returns the namespace of the uri
     *
     * @param string $uri
     * @return string 
     * @throws APIException
     */
    public static function getNamespace($uri) {
        
        if (!Check::isUri($uri))
            throw new APIException(API_ERROR_URI);
        
        $l = self::getNamespaceEnd($uri);
                
        return ($l > 1) ? substr($uri, 0, $l) : "";
    }

    /**
     * Returns the name of the uri
     *
     * @param string $uri
     * @return string 
     * @throws APIException
     */
    public static function getName($uri) {
        
        if(!Check::isString($uri))
            throw new APIException(API_ERROR_STRING);
        
        if (Check::isUri($uri))
            return substr($uri, self::getNamespaceEnd($uri));
        
        if (Check::isPrefixAndName($uri))
            return substr (strpos ($uri, ":")+1, $uri);
        
        return $uri;
    }

    /**
     * Returns the position of the end of the namespace
     *
     * @param string $uri
     * @return integer 
     * @throws APIException
     */
    public static function getNamespaceEnd($uri) {
        
        if (!Check::isUri($uri))
            throw new APIException(API_ERROR_URI);
        
        $l = strlen($uri) - 1;

        do {
            $c = substr($uri, $l, 1);
            if ($c == '#' || $c == ':' || $c == '/')
                break;
            $l--;
        } while ($l >= 0);

        $l++;
        return $l;
    }

}

?>
