<?php

/**
 * --------------------------------------------------------------------
 * ERP API 
 * --------------------------------------------------------------------
 *
 * The Easy RDF for PHP (ERP) API was developed during my masters thesis on the 
 * Technical University of Vienna. It's aim is to provide an easy-to-use interface 
 * for manipulation and creation of RDF documents.
 * 
 * Inspiration for creating this API came from the ARC (http://github.com/semsol/arc2) 
 * and RAP (http://www4.wiwiss.fu-berlin.de/bizer/rdfapi/) API, but also the Jena 
 * (http://jena.sourceforge.net/) had great influence.
 * 
 * This API allows two ways of interacting with RDF:
 *     (1) As a simple list of statements (known from RAP)
 *     (2) A more OOM approach (similar to Jena)
 * 
 * Using the list-approach it is possible to simply create a RDF document. The 
 * OOM-approach allows the view of resources as they have properties, thus creating 
 * a graph of relations between other resources.
 * 
 * Copyright (C) 2011 Alexander Aigner
 * 
 * This API is published under the GNU General Public License, which should have 
 * been provide along with this API. If not, see <http://www.gnu.org/licenses/>.
 * 
 * @author      Alexander Aigner <alex.aigner (at) gmail.com> 
 * 
 * @name        API.php
 * @version     2011-08-09
 * @access      public
 * 
 * @license     Copyright (C) 2011  Alexander Aigner
 *              The ERP API is free software: you can redistribute it and/or modify
 *              it under the terms of the GNU General Public License as published by
 *              the Free Software Foundation, either version 3 of the License, or
 *              (at your option) any later version.
 * 
 *              This ERP API is distributed in the hope that it will be useful,
 *              but WITHOUT ANY WARRANTY; without even the implied warranty of
 *              MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *              GNU General Public License for more details.
 * 
 *              You should have received a copy of the GNU General Public License
 *              along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * --------------------------------------------------------------------
 */


require_once "util/Autoloader.php";

?>
