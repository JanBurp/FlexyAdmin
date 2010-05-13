/*
(c) 2008, Joern Schou-Rode <jsr@malamute.dk>

This work as-is we provide.
No warranty, express or implied.
We've done our best,
to debug and test.
Liability for damages denied.

Permission is granted hereby,
to copy, share, and modify.
Use as is fit,
free or for profit.
On this notice these rights rely.

(c) 2009, Jdb, Jan den Besten, changes made for FlexyAdmin:
- callback function, after filtering is ready.
- starts filtering when more than 2 keys are entered
(c) 2010, Jdb, Jan den Besten, changes made for FlexyAdmin:
- add class 'filtered' when filtered out


*/

(function($) {
  // Extend all jQuery objects with the filterable method.
  $.fn.filterable = function(options, callback) {													// Jdb added callback parameter
    var o = $.extend({}, $.fn.filterable.defaults, options);

    return this.each(function() {
      var target = $(this);

      // Create the query div, and insert it into the DOM.
      var div = $('<div class="' + o.queryCss + '">');
      switch (o.queryPosition) {
        case 'before': div.insertBefore(target); break;
        case 'after': div.insertAfter(target); break;
      }

      // Create the query input field, possibly with a label in front if it.
      var txt = $('<input type="text" />').appendTo(div);
      if (o.queryLabel) div.prepend('<label>' + o.queryLabel + '</label>');

      // Define the filtering function.
      var fn = function(key) {
				// change class of search field, to show its busy
				$('input.filter').addClass('busy');
        var query = txt.val().toLowerCase();
				var qlen = query.length;
				if (qlen>2 || qlen==0) {
	        target.find(o.affects).each(function() {
	          var item = $(this);
	          if (item.text().toLowerCase().indexOf(query) >= 0)
							item.show().removeClass('filtered');
	          else
							item.hide().addClass('filtered');
	        });
					// Jdb: Call callback function if defined
					if (callback!=undefined && callback!='') {
						callback.call(this,key.keyCode);
					}
				}
				// remove busy class
				$('input.filter').removeClass('busy');
      };

      // Attach the function to the input text field (onKeyUp) or to a button (onClick).
      if (o.queryButton)
				$('<input type="button" value="' + o.queryButton + '" />').appendTo(div).click(fn);
      else
				txt.keyup(fn);
    });
  };

  // Declare default options.
  $.fn.filterable.defaults = {
    affects: '> *',
    queryLabel: '',
    queryButton: '',
    queryPosition: 'before',
    queryCss: 'ui-filterable-query',
		callback: ''
  };

})(jQuery);
