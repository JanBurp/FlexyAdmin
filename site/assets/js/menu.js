$(document).ready(function() {
	// set the timing
	delay=150;
	speed=350;

	/* keep everything below as is */
	var UnfoldDelay;
	$("li:not(.active)").mouseenter(function() {
		item=$(this);
		UnfoldDelay=setTimeout( function() {
			$(item).children("ul").slideDown(speed);
		},delay);
	}).mouseleave(function() {
		clearTimeout(UnfoldDelay);
		$(this).children("ul").slideUp(speed);
	});
});
