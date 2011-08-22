The Easy RDF for PHP (ERP) API was developed during my masters thesis on the 
Technical University of Vienna. It's aim is to provide an easy-to-use interface 
for manipulation and creation of RDF documents.

Inspiration for creating this API came from the ARC (http://github.com/semsol/arc2) 
and RAP (http://www4.wiwiss.fu-berlin.de/bizer/rdfapi/) API, but also the Jena 
(http://jena.sourceforge.net/) had great influence.

This API allows two ways of interacting with RDF:
    (1) As a simple list of statements (known from RAP)
    (2) A more OOM approach (similar to Jena)

Using the list-approach it is possible to simply create a RDF document. The 
OOM-approach allows the view of resources as they have properties, thus creating 
a graph of relations between other resources.

Copyright (C) 2011 Alexander Aigner

This API is published under the GNU General Public License, which should have 
been provide along with this API. If not, see <http://www.gnu.org/licenses/>.