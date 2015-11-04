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
 * Maakt van gegeven object een serialized string:
 * 
 *    { table: 'test', where: 10 }
 * 
 * Wordt:
 * 
 *    table=test&where=10
 *
 * @param object data het JSON object
 * @return string
 */
jdb.serializeJSON = function(data) {
  'use strict';
  var serializeString='';
  
  if (angular.isDefined(data)) {
    // sort the keys, so the returned string has always same order of keys
    var keys = Object.keys(data).sort();
    // Loop the keys
    for (var i = 0; i < keys.length; i++) {
      var key=keys[i];
      if (serializeString!=='') serializeString+='&';
      // array
      if (angular.isArray(data[key])) {
        data[key].forEach(function(el,index) {
          if (serializeString!=='') serializeString+='&';
          serializeString += encodeURIComponent(key) + '[]=' + encodeURIComponent(el);
        });
      }
      // normal
      else {
        serializeString += encodeURIComponent(key) + '=' + encodeURIComponent(data[key]);
      }
    }
  }
  
  return serializeString;
};


/**
 * jdb.deleteItems(obj,items)
 * 
 * Verwijderd meerdere elementen van een array of object gespecificeerd door een array van keys
 * 
 * @param obj obj Het aan te passen object
 * @param array items Array van keys (strings) die verwijderd moeten worden
 * @return obj Opgeschoond object
 */
jdb.deleteItems = function(obj,items) {
  items.forEach(function(el,index){
    delete obj[el];
  });
  return obj;
};


/**
 * jdb.indexOfProperty(array,property,value)
 * 
 * Vind de key van een array van objecten waar de waarde van een property van een object gelijk is aan value
 * 
 * @param array array De array van objecten waarin gezocht wordt
 * @param string property Naam van de property
 * @param mixed value De waarde waarnaar gezocht wordt
 * @return integer index De index van het gevonden object, of -1 als niet is gevonden.
 */
jdb.indexOfProperty = function(array,property,value) {
  var index = false;
  var i=0;
  do {
    if ( angular.isDefined(array[i]) && angular.isDefined(array[i][property]) && array[i][property]===value) {
      index=i;
    }
    i++;
  } while (index===false && i<array.length);
  return index;
};


/**
 * jdb.moveMultipleArrayItems(array,from,many,to)
 * 
 * Verplaatst items in een array
 * 
 * @param array array De originele array
 * @param int from Vanaf welke index
 * @param int many Hoeveel items
 * @param int to Naar welke index
 */
jdb.moveMultipleArrayItems = function(array, from, many, to) {
  // Make sure from and to are >0 and <array.length
  from = (from<0) ? 0 : from;
  to = (to<0) ? 0 : to;
  var len=array.length;
  from = (from>len) ? len : from;
  to = (to>len) ? len : to;
  var newArray = array.slice(); // copy
  // Als from=to, dan hoeft er niets te gebeuren
  if (from!==to) {
    var removedItems = newArray.splice(from,many);
    // if (to>from) to-=many;  // Als de items naar hogere index gaan, vershuif `to` mee.
    for (var i = 0; i < many; i++) {
      newArray.splice(to+i, 0, removedItems[i]);
    }
  }
  return newArray;
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
String.method('decodePath', function() {
  'use strict';
  return this.replace(/\//g,"___");
});


/**
 * String.encodePath()
 * 
 * Vervangt '___' door '/'
 * Voor de conversie van paths in een URL
 * @return String
 */
String.method('encodePath', function() {
  'use strict';
  return this.replace(/___/g,"/");
});


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
 * jdb.randomString()
 * 
 * Geeft een willekeurige string van gegeven lengte
 * 
 * @param int len[8] Lengte van willekeuruge string
 * @param string chars["0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz"] Mogelijke karakters
 * @return string
 */
jdb.randomString = function(len,chars) {
  'use strict';
  if (angular.isUndefined(len))   len   = 8;
  if (angular.isUndefined(chars)) chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
  var randomstring = '';
  for (var i=0; i < len ; i++) {
    var rnum = Math.floor(Math.random() * chars.length);
    randomstring += chars.substring(rnum,rnum+1);
  }
  return randomstring;
};

/**
 * jdb.randomInt()
 * 
 * Geeft een willekeurige integer tussen min en max waarde
 * 
 * @param int min
 * @param int max
 * @return int
 */
jdb.randomInt = function(min, max) {
  return Math.floor(Math.random() * (max - min)) + min;
};


/**
 * Geeft complement van kleur (string)
 */
jdb.colorComplement = function(color) {
  color = '0x'+color.replace('#','');
  color = parseInt(color,16);
  var complement = '#'+(0xFFFFFF ^ color).toString(16);
  return complement;
}


/**
 * jdb.assocArrayItem()
 * 
 * Geeft een item van een associatieve array terug
 * 
 * @param array de array
 * @param string key de key waarop gezocht moet worden
 * @param mixed value waarde van de key waarop gezocht wordt
 */
jdb.assocArrayItem = function( array, key, value ) {
  var item = {};
  angular.forEach( array, function( row,nr ) {
    if (row[key]===value) item = row;
  });
  return item;
};


/**
 * jdb.firstArrayItem()
 * 
 * Geeft eerste item van een array
 * 
 * @param array arr
 * @return mixed
 */
jdb.firstArrayItem = function( arr ) {
  for(var i in arr) return arr[i];
};
