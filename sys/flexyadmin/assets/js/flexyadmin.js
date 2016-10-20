$(document).ready(function() {
  
  // UI
  $('.flexy-blocks').flexyBlocks();
  

});



/**
 * flexyBlocks
 * @author jan den besten
 */
(function($) {
  var defaults = {
    style: 'btn btn-outline-primary btn-sm',
  };
  var opts;
  var self;
  var methods = {
    init : function( options ) {
      self=this;
   		opts = $.extend({}, defaults, options);
      $.each(self, function(){
        var text = $(this).text();
        var textLen = text.length;
        var html = '';
        for (var i = 0; i < textLen; i++) {
          var char = text.charAt(i);
          var charClass=char;
          var btnStyle = opts.style;
          if (char==' ') {
            char="&nbsp;";
            charClass='space';
            btnStyle='btn-default';
          }
          html += '<span class="flexy-block '+btnStyle+' char_'+charClass+'">'+char+'</span>';
        }
        self.html(html);
      });
      return self;
    },
  };
  $.fn.flexyBlocks = function(methodOrOptions) {
     if ( methods[methodOrOptions] ) {
       return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
     } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
       return methods.init.apply( this, arguments );
     } else {
       $.error( 'Method "' +  methodOrOptions + '" does not exist on jQuery.flexyBlocks' );
     }    
   };  
})(jQuery);
