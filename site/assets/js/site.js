
// Called after DOM is ready
$(document).ready(function() {
});


// Called after all images and external files are loaded (and image sizes are known)
$(window).load(function() {
});


// Called after the browser window is resized.
$(window).resize(function() {
  // Note: Code in a resize handler should never rely on the number of times the handler is called.
  // Depending on implementation, resize events can be sent continuously as the resizing is in progress
  // (the typical behavior in Internet Explorer and WebKit-based browsers such as Safari and Chrome),
  // or only once at the end of the resize operation (the typical behavior in some other browsers such as Opera).
  // 
  // Use this only if the @media query of css is not enough
});



//
// Minimal jQuery plugin template, for more comments see template.js (which has essentially the same template as the one here)
//

(function($) {
  var defaults = {
    // Default options here
  };
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






