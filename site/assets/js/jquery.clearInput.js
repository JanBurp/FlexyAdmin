/*!
 * jQuery clearInput: for a nice compact input field.
 * Clears the inputfield when it has focus (and its the placeholder).
 * Author: Jan den Besten
 */

(function($) {
	$.fn.clearInput = function(opts) {
    // remove buttons
    var obj=$(this).not('.button');
    // traverse
		return obj.each(function() {
      // init
      $(this).addClass('clearInput');
      if ($(this).attr('value')==$(this).attr('empty_value') || $(this).attr('value')=='') {
				$(this).val($(this).attr('empty_value'));
        $(this).addClass('empty_value');
      }
      // focus events
			$(this).focus(function(){
				if ($(this).val()==$(this).attr('empty_value')) {
          $(this).val('');
        }
        $(this).select();
			}).focusout(function(){
				if ($(this).val()=='') {
					$(this).val($(this).attr('empty_value'));
          $(this).addClass('empty_value');
				}
        else {
          $(this).removeClass('empty_value');
        }
			});
      // Make sure all values are cleaned after submit
      $(this).parents('form').submit(function(){
        $(this).find('.clearInput').each(function(){
          if ($(this).val()==$(this).attr('empty_value')) $(this).val('');
        });
      });
		});
	};
})(jQuery);
