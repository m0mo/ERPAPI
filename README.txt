The ERP API was developed during my masters thesis on the Technical University 
of Vienna. It's aim is to provide an easy-to-use interface for manipulation and 
creation of RDF documents.

Inspiration for this API I got from the ARC and RAP API, but also the Jena API 
(for Java) had great influence on designing the usage.

This API allows two ways of interacting with resources:
    (1) As a simple list of statements (known from RAP)
    (2) A more OOM approach (similar to Jena)

Using the list-approach it is possible to simply create a RDF document. The 
OOM-approach allows the view of resources as they have properties, creating a 
graph of relations between other resources.

Copyright (C) 2011  Alexander Aigner

This API is published under the GNU General Public License, which should have 
been provide along with this program. If not, see <http://www.gnu.org/licenses/>.