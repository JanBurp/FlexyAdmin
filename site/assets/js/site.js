$(document).ready(function() {
});

$(window).load(function() {
});

$(window).resize(function() {
});

//
// Minimal jQuery plugin template, for more comments see template.js (which has essentially the same template as the one here)
//
(function($) {
  var defaults = {};
  var opts;
  var self;
  var methods = {
    init : function( options ) {
      self=this;
   		opts = $.extend({}, defaults, options);
      //
      // (Initialise) code here...
      //
      return self;
    }
    // Put more methods here
  };
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






