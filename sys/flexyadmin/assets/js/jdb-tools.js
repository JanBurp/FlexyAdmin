/**
 * Tools
 */

export default {
  name: 'jdb',
  
  
  
  
  /**
   * jdb.getUrlQueryPart(part)
   * 
   * Geeft deel van de URL query
   * 
   * @param string het gevraagde deel
   * @return mixed waarde
   */
  getUrlQueryPart : function( field ) {
    var href = window.location.href;
    var reg = new RegExp( '[?&]' + field + '=([^&#]*)', 'i' );
    var string = reg.exec(href);
    return string ? string[1] : null;
  },
  
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
  indexOfProperty : function(array,property,value) {
    var index = false;
    var i=0;
    do {
      if ( !_.isUndefined(array[i]) && !_.isUndefined(array[i][property]) && array[i][property]===value) {
        index=i;
      }
      i++;
    } while (index===false && i<array.length);
    return index;
  },
  

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
          if (!_.isUndefined(data[key])) serializeString += encodeURIComponent(key) + '=' + encodeURIComponent(data[key]);
        }
      }
    }
    return serializeString;
  },
  
  encodeURL : function(string) {
    return encodeURIComponent(_.escape(string));
  },
  
  /**
   * Post een vueform als normale form
   * https://stackoverflow.com/questions/133925/javascript-post-request-like-a-form-submit#133997
   */
  submitWithPost : function(action, params, method) {
    method = method || "post"; // Set method to post by default, if not specified.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", action);
    // fields
    var addField = function( key, value ){
      var hiddenField = document.createElement("input");
      hiddenField.setAttribute("type", "hidden");
      hiddenField.setAttribute("name", key);
      hiddenField.setAttribute("value", value );
      form.appendChild(hiddenField);
    }; 
    for(var key in params) {
      if( params.hasOwnProperty(key) ) {
        addField( key, params[key] ); 
      }
    }
    document.body.appendChild(form);
    form.submit();
  },
  
  
  /**
   * jdb.complementColor()
   * 
   * Geeft de tegenovergesteld kleur
   * 
   * @param string color
   * @return string
   */
  complementColor : function(color) {
    color = parseInt(color.replace('#',''),16);
    var complement = '#'+('000000' + ((0xFFFFFF ^ color).toString(16))).slice(-6);
    return complement;
  },
  

  /**
   * jdb.createUUID()
   * 
   * Geeft unieke identifier
   * 
   * @param string uuid
   * @return string
   */
  createUUID : function() {
    var d = new Date().getTime();
    if(window.performance && typeof window.performance.now === "function"){
      d += performance.now(); //use high-precision timer if available
    }
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
      var r = (d + Math.random()*16)%16 | 0;
      d = Math.floor(d/16);
      return (c=='x' ? r : (r&0x3|0x8)).toString(16);
    });
    return uuid;
  },
  

  /**
   * jdb.isJsonString()
   * 
   * Test of string JSON is
   * 
   * @param string uuid
   * @return boolena or []
   */
  isJsonString: function(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return JSON.parse(str);
  },
  
  
  /**
   * jdb.stripHTML()
   * 
   * Verwijderd alle tags van meegegeven string
   * @return String
   */
  stripHTML : function(s) {
    return s.replace(/(<([^>]+)>)/ig,"");
  },
  
  
  vueLog : function() {
    for (var i = 0; i < arguments.length; i++) {
      console.log(JSON.stringify(arguments[i], null, 2));
    }
  },
  
  
};
