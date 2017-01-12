//
// jQuery plugin template
// 
// - Replace PLUGIN_NAME with the name of your plugin
// - Create your onw methods, if needed
// - Set your default options if needed
// - Put your main code in the init method
//
// See: http://docs.jquery.com/Plugins/Authoring


(function($) {
  
  // Object with default values of all the options
  var defaults = {
  };
  
  // Object that holds all the options (extending defaults and given options)
  var opts;
  
  // Element(s) where the plugin is called for
  var self;
  
  //
  // All the methods of the plugin, can be called like this: $(element).pluginName('method-name',[options]);
  // Or internal like this: methods.init.apply( self, [options] );
  //
  var methods = {

    // init method, will be called if no methods are given
    init : function( options ) {
      // initialise self and options
      self=this;
   		opts = $.extend({}, defaults, options);
        
      //
      // Put you're initialise code here...
      //

      // return the element
      return self;
    },
    
    // put more methods here if needed
    test : function() {
      
      return self;
    }
    
  };
  
  
  // Decide if a method is called, or just the main (init)
  $.fn.PLUGIN_NAME = function(methodOrOptions) {
     if ( methods[methodOrOptions] ) {
       return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
     } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
       return methods.init.apply( this, arguments );
     } else {
       $.error( 'Method "' +  methodOrOptions + '" does not exist on jQuery.PLUGIN_NAME' );
     }    
   };  
})(jQuery);
