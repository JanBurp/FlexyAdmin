$(document).ready(function() {
	// menu plaatjes
	homesrc=$("#titel img").attr('src');
	$("#menu li").hover(function(){
		uri=get_uri($(this));
		src=$("#menu_images img.image_"+uri).attr("src");
		if (src!="") 
			$("#titel img").attr('src','site/assets/menu_pictures/'+src);
	},function(){
		uri=get_uri($(this));
		$("#titel img").attr('src',homesrc);
	});
});

function get_uri(obj){var i;c=obj.attr("class").split(" ");return c[0];}