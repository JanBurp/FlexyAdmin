/**
 * Met deze jQuery Plugin kun je een Twitter Embedded Timeline stylen
 * Dat kan helaas niet met een eenvoudige stylesheet omdat het in een iframe wordt geplaatst. Vandaar deze jQuery plugin.
 *
 * Gebruik:
 * 
 * $('#twitter').styleTwitter();
 * 
 * $('#twitter').styleTwitter({
 *  remove:['.avatar','.p-nickname','.footer','.load-more'],
 *  style:{
 *      'a' : {'color':'#FF0000'},
 *      '.header' : {'float':'left','min-height':'0px','padding-left':'0px'},
 *      '.timeline .stream' : {'overflow':'hidden'},
 *      '.tweet' : {'padding':'5px 0px','border-bottom':'dotted 1px #FF0000'}
 *     }
 *  });
 * 
 * @package default
 * @author Jan den Besten
 */

(function($) {
  var settings = {
    remove:['.load-more'],
    style:{'.timeline .stream' : {'overflow':'hidden'} },
     scrolltime:5000,
     slidetime:2000,
     checktime:250
  };
  var opts;
  var self;
  var content;
  var methods = {
      init : function( options ) { 
        self=this;
     		opts = $.extend({}, settings, options);
        // hide timeline
        $(self).hide();
        // style timeline when ready
        opts.timer=setInterval(function(){
          opts.iframe=$(self).find('iframe');
          if (opts.iframe.length>0) {
            content=$(opts.iframe);
            content=$(content).contents();
            methods.style.apply(this,arguments);
          }
        },opts.checktime);
        return this;
      },
      style : function() {
        // Remove
        for (var i=0; i < opts.remove.length; i++) {
          $(content).find(opts.remove[i]).remove();
        };
        // Style
        for (key in opts.style) {
          $(content).find(key).css(opts.style[key]);
        }
        // show & scroll
        $(self).show();
        clearInterval(opts.timer);
        // start scrolling
        methods.scroll.apply(this,arguments);
      },
      scroll : function() {
        var h=$(self).height();
        var th=$(content).find('.stream ol').height();
        // scroll when needed
        if (th>h) {
          var item;
          $(content).find('.stream ol li').addClass('visible');
          opts.timer=setInterval(function(){
            // find first visible item, scroll up
            item=$(content).find('.stream ol li.visible:first');
            if (item.length>0) {
              $(item).slideUp(opts.slidetime).removeClass('visible');
            }
            else {
              // reset
              $(content).find('.stream ol li').addClass('visible').show();
            }
          },opts.scrolltime);
        }
      }
      
    };
    $.fn.styleTwitter = function(methodOrOptions) {
     if ( methods[methodOrOptions] ) {
       // call method
       return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
     } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
       // Default to "init"
       return methods.init.apply( this, arguments );
     } else {
       // error
       $.error( 'Method "' +  methodOrOptions + '" does not exist on jQuery.styleTwitter' );
     }    
   };  
})(jQuery);
