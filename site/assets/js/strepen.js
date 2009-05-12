$(document).ready(function() {
	img=$("img.strepen");
	w=$(img).width();
	h=$(img).height();
	src=$(img).attr("src");
	$(img).wrap("<div class=\"foto_met_strepen\"></div>");
	$("div.foto_met_strepen").css({
		width:w,
		height:h,
		'background-image':'url('+src+')'
	});
	$(img).remove();
	$("div.foto_met_strepen").append('<div class="de_strepen"></div>');
	$("div.de_strepen").css({width:w,height:h});
});