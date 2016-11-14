/**
 * Tools
 */

export default {
  name: 'jdb',

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
  moveMultipleArrayItems : function(array, from, many, to) {
    // Make sure from and to are >0 and <array.length
    var len = array.length;
    from = (from<0) ? 0 : from;
    from = (from>len) ? len : from;
    to = (to<0) ? 0 : to;
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
  },
  
  
  /**
   * jdb.serializeJSON
   * 
   * Maakt normale POST data (string) van meegegeven Object
   */
  serializeJSON : function(data) {
    var serializeString='';
    if ( !_.isUndefined(data) ) {
      // sort the keys, so the returned string has always same order of keys
      var keys = Object.keys(data).sort();
      // Loop the keys
      for (var i = 0; i < keys.length; i++) {
        var key=keys[i];
        if (serializeString!=='') serializeString+='&';
        // array
        if (_.isArray(data[key])) {
          data[key].forEach(function(el,index) {
            if (serializeString!=='') serializeString+='&';
            serializeString += encodeURIComponent(key) + '[]=' + encodeURIComponent(el);
          });
        }
        // object
        if (_.isObject(data[key])) {
          _.forEach(data[key], function(el,index) {
            if (serializeString!=='') serializeString+='&';
            serializeString += encodeURIComponent(key) + '['+index+']=' + encodeURIComponent(el);
          });
        }
        // normal
        else {
          serializeString += encodeURIComponent(key) + '=' + encodeURIComponent(data[key]);
        }
      }
    }
    return serializeString;
  },
  
  encodeURL : function(string) {
    return encodeURIComponent(_.escape(string));
  },
  
  
};
