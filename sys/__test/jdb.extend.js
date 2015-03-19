/**
 * Extending standaard JavaScript Object prototypes en een aantal globale functies
 * 
 * @author: Jan den Besten
 * @copyright: Jan den Besten
 * @license: n/a
 * 
 * $Author$
 * $Date$
 * $Revision$
 * $HeadURL$ 
 */

/*jshint -W083 */


 /**
  * This is a convenient way to add methods to an object prototype.
  * see: 'JavaScript the Good Parts', page 4
  * @author Dougles Crockford
  */
Function.prototype.method = function(name,func) {
  'use strict';
  this.prototype[name]=func;
  return this;
};



var jdb = {};

/**
 * jdb.serializeJSON()
 *
 *
 */
jdb.serializeJSON = function(data) {
  'use strict';
  var serializeString='';
  for (var key in data) {
    if (serializeString) serializeString+='&';
    // array
    if (angular.isArray(data[key])) {
      data[key].forEach(function(el,index) {
        serializeString += encodeURIComponent(key) + '[]=' + encodeURIComponent(el) + '&';
      });
    }
    // normal
    else {
      serializeString += encodeURIComponent(key) + '=' + encodeURIComponent(data[key]);
    }
  }
  return serializeString;
};




/**
 * String.striptags()
 * 
 * Verwijderd alle tags van het String object
 * @return String
 */
// String.method('striptags', function() {
//   'use strict';
//   return this.replace(/(<([^>]+)>)/ig,"");
// });


/**
 * String.htmlentities()
 * 
 * Vervangt alle tags (<>) met HTML entities, handig als je HTML-code wilt tonen
 * @return String
 */
// String.method('htmlentities', function() {
//   'use strict';
//   return this.replace(/</g,"&lt;").replace(/>/g,"&gt;");
// });


/**
 * String.prefix([String char="_"])
 * 
 * Geeft de prefix van een string
 * @return String
 */
String.method('prefix', function(char) {
  'use strict';
  char = char || "_";
  return this.substr(0, this.indexOf(char)) ;
});

/**
 * String.suffix([String char="_"])
 * 
 * Geeft de prefix van een string
 * @return String
 */
String.method('suffix', function(char) {
  'use strict';
  char = char || "_";
  return this.substr(0, this.lastIndexOf(char)) ;
});




/**
 * String.decodePath()
 * 
 * Vervangt '/' door '___'
 * Voor de conversie van paths in een URL
 * @return String
 */
// String.method('decodePath', function() {
//   'use strict';
//   return this.replace(/\//g,"___");
// });


/**
 * String.encodePath()
 * 
 * Vervangt '___' door '/'
 * Voor de conversie van paths in een URL
 * @return String
 */
// String.method('encodePath', function() {
//   'use strict';
//   return this.replace(/___/g,"/");
// });


/**
 * String.addPart()
 * 
 * Voegt een string toe aan een andere string gescheiden door een karakter
 * 
 * @part    String De string die aan String moet worden geplakt
 * @adder   String[':'] Karakter wat tussen de strings komt
 * @return  String
 */
// String.method('addPart', function (part,adder) {
//   'use strict';
//   var s=this;
//   if (angular.isUndefined(adder)) adder=',';
//   if (s.length>0) s+=adder;
//   s+=part;
//   return s;
// });


/**
 * String.random()
 * 
 * Geeft een willekeurige string van gegeven lengte
 * @param int len Lengte van willekeuruge string
 * @param string chars["0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz"] Mogelijke karakters
 * @return String
 */
// String.method('random', function(len,chars) {
//   'use strict';
//   if (angular.isUndefined(chars)) chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
//   var randomstring = '';
//   for (var i=0; i < len ; i++) {
//     var rnum = Math.floor(Math.random() * chars.length);
//     randomstring += chars.substring(rnum,rnum+1);
//   }
//   return randomstring;
// });