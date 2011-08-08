<?php

/**
 * -----------------------------------------------------------------------------
 * ERP API 
 * -----------------------------------------------------------------------------
 *
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        API.php
 * @version     0.2.0 (Aug 8, 2011)
 * @access      public
 * 
 * Description  The ERP API was developed during my masters thesis on the 
 *              Technical University of Vienna. It's aim is to provide an
 *              easy-to-use interface for manipulation and creation of RDF 
 *              documents.
 * 
 *              Inspiration for this API I got from the ARC and RAP API, but
 *              also the Jena API (for Java) had great influence on designing
 *              the usage.
 * 
 *              This API allows two ways of interacting with resources:
 *                  (1) As a simple list of statements (known from RAP)
 *                  (2) A more OOM approach (similar to Jena)
 * 
 *              Using the list-approach it is possible to simply create a RDF
 *              document.
 * 
 *              The OOM-approach allows the view of resources as they have 
 *              properties, creating a tree (or graph) of relations between
 *              other resources.
 * 
 * -----------------------------------------------------------------------------
 */


require_once "util/Autoloader.php";

?>
