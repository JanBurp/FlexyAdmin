/*!
 * jQuery clearInput: for a nice compact input field.
 * Clears the inputfield it has focus.
 * Author: Jan den Besten
 */

(function($) {
	$.fn.clearInput = function(opts) {
		return this.each(function() {
			$(this).focus(function(){
				$(this).val('');
			}).focusout(function(){
				if ($(this).val()=='') {
					$(this).val($(this).attr('empty_value'));
				}
			});
		});
	};
})(jQuery);
