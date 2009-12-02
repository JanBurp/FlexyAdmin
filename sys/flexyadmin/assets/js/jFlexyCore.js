$(document).ready(function() {

	// define
	keyEnter=13;
	keyUp=38;
	keyDown=40;
	keyBackspace=8;
	keyDelete=48;

	// Check modes
	dialog=$("#ui");
	Popup=$("#popup");
	isForm=$("#content").hasClass("form");
	isGrid=$("#content").hasClass("grid");
	isGridAction=$(".grid").hasClass("actionGrid");
	// isTree=$("#content").hasClass("tree");
	isFile=$("#content").hasClass("filemanager");
	if (!isGrid && isFile)	{	isGrid=$("#content").hasClass("list"); }
	if (isFile)							{ isThumbs=$("#content").hasClass("icons");}
	isSortable=false;
	//

	if (isGrid || isFile) doGrid();
	if (isForm) 					doForm();

	//
	// Vertical Text
	//
	$('.verticalText').each(function(){
		w=$(this).width();
		if (w<30) {
			$(this).flipv().addClass('verticalShow');
		}
	});

	//
	// IMG zoom
	//
	showAutoZoom();
	
	//
	// Help
	//
	showHelpItems();
	

});



//
// Functions for confirm
//
function confirm_dialog(uri,name,id) {
	showName='';
	for (x in name) {
		showName+=" + '"+name[x]+"'";
	}
	showName=showName.substr(3);
	dialog.html(langp("dialog_delete_sure",showName));
	$(dialog).dialog({
		title:lang("dialog_title_confirm"),
		modal:true,
		width:500,
		buttons: ({ cancel	: function(){	$(dialog).dialog("destroy"); },
								yes			: function(){
														$('.ui-dialog .ui-dialog-buttonpane').add('.ui-dialog a').hide();
														$('.ui-dialog .ui-dialog-content').append("<img src='"+site_url("sys/flexyadmin/assets/icons/wait.gif")+"' align='right' />");
														for(x in id) {
															uri+=':'+id[x];
														}
														// console.log(uri);
														location.replace(uri+"/confirmed/confirm");
													}
						 }),
		close: function(){$(dialog).dialog("destroy");}
	});
	changeButt("cancel",lang("dialog_cancel"));
	changeButt("yes",lang("dialog_yes"));
}
function clean_message() {$("#message").html("");}

function showHelpItems() {
	var ShowDelay;
	var HideDelay;
	$("span.help").children().removeAttr("title");
	$("span.help").mouseenter(function() {
		obj=$(this);
		helpName=get_subclass("help_",$(this));
		ShowDelay=setTimeout( function() {		
			helpTxt=$("#help_messages span#help_"+helpName).html();
			// helpTxt=$(obj).children("span.hide").html();
			html=helpTxt;
			$(Popup).html(html).fadeIn(150);
			HideDelay=setTimeout( function(){
				$(Popup).fadeOut(1000);
			},5000 );
		},1000);
	}).mouseout(function() {
		clearTimeout(ShowDelay);
		$(Popup).fadeOut(150);
	}).mousemove(function(e){
		clearTimeout(HideDelay);
    $(Popup).css({left:e.pageX+8,top:e.pageY+18});
  });
};

function showAutoZoom() {
	var ShowDelay;
	var HideDelay;
	$('img.zoom').add('.flash .zoom').not('.thumbs img.zoom').not('.thumbs .flash .zoom').mouseenter(function(){
		obj=$(this);
		ShowDelay=setTimeout( function() {		
			zoomThumb=$(obj).clone().addClass('autoZoom');
			// check size
			$('body').append(zoomThumb);
			widthThumb=$('.autoZoom').width();
			heightThumb=$('.autoZoom').height();
			$('.autoZoom').remove(); // size now determined, clone can be removed
			// name and place
			imgName=$(zoomThumb).attr('alt');
			imgName=imgName.substr(imgName.lastIndexOf('/')+1);
			// place it
			offsetThumb=$(obj).offset();
			leftThumb=offsetThumb.left-(widthThumb/2)+13 + 'px';
			$('body').append('<div class="zoomThumb"><p>'+imgName+'</p></div>');
			if ($(obj).parent('li').parent('ul.values').length>0) {
				topThumb=offsetThumb.top-(heightThumb+34) + 'px';
				$('div.zoomThumb').append(zoomThumb);
			}
			else {
				topThumb=offsetThumb.top+30 + 'px';
				$('div.zoomThumb').prepend(zoomThumb);
			}
			// set and show
			$('.zoomThumb').css({left:leftThumb, top:topThumb}).fadeIn(150).mouseleave(function(){
				$('.zoomThumb').fadeOut(150,function(){
					$(this).remove();
				});
			})
		},200);
	}).mouseleave(function() {
		clearTimeout(ShowDelay);
		$('.zoomThumb').fadeOut(150,function(){
			$(this).remove();
		});
	});
	// fullsize zoom
	$('img.zoom').add('.flash .zoom').not('form ul li img.zoom').not('form ul li .flash .zoom').fullsize({triggerIcon:false});
	$('form ul li img.zoom').add('form ul li .flash .zoom').fullsize({triggerIcon:false,triggerEvent:'dblclick'});
}

// function zoom_dialog(obj) {
	// var src,w,h,ext;
	// src=$(obj).attr('src');
	// w=$(obj).attr('zwidth');
	// h=$(obj).attr('zheight');
	// imgRatio=w/h;
	// // set sizes not bigger than screen
	// scrW=$("body").outerWidth()-50;
	// scrH=$("body").outerHeight()-100;
	// if ((w<scrW) && (h<scrH)) {
	// 	dw=w;
	// 	dh=h;
	// }
	// else {
	// 	if (w>scrW && h>scrH) {
	// 		if (scrW/w < scrH/h) {
	// 			dw=scrW;
	// 			dh=dw/imgRatio;
	// 		}
	// 		else {
	// 			dh=scrH;
	// 			dw=dh*imgRatio;
	// 		}
	// 	}
	// 	else {
	// 		if (w>scrW) {
	// 			dw=scrW;
	// 			dh=dw/imgRatio;
	// 		}
	// 		else {
	// 			dh=scrH;
	// 			dw=dh*imgRatio;
	// 		}
	// 	}	
	// }
	// // what file type?
	// ext=get_ext(src);
	// if (ext=="swf" || ext=="flc") {
	// 	dialog.html(flash(src,dw,dh));
	// }
	// else {
	// 	// is it a cached thumb?
	// 	i=src.indexOf("_thumbcache");
	// 	if (i>=0) {
	// 		src=src.substr(i+12); // 11 = length of '_thumbcache'
	// 		src=pathdecode(src);
	// 	}
	// 	dialog.html('<a href="javascript:close_dialog()"><img src="'+src+'" width="'+dw+'" height="'+dh+'" alt="'+src+'" /></a>');
	// }
	// $(dialog).dialog({
	// 	title:src.substr(src.lastIndexOf("/")+1)+" ("+w+"x"+h+")",
	// 	modal:true,
	// 	width: dw+'px',
	// 	heigth: dh+'px',
	// 	position: 'center',
	// 	closeOnEscape:true,
	// 	// dialogClass:'zoom',
	// 	resizable:false,
	// 	scrollable:false,
	// 	close: function() {$(dialog).dialog("destroy"); }
	// });
// }
function close_dialog() {
	$(dialog).dialog("destroy");
}

function ajaxError(error) {
	dialog.html(error);
	$(dialog).dialog({
		title:"Error",
		modal:true,
		width:500,
		buttons: ({ ok	: function(){	$(dialog).dialog("destroy"); $(obj).attr({"href":href});}	 	}),
		close: function(){$(dialog).dialog("destroy"); $(obj).attr({"href":href});}
	});
}


function changeButt(id,s) {
	var h;
	$("div.ui-dialog-buttonpane").children().each(function(){
		h=$(this).html();
		$(this).html(h=h.replace(id,s));
	});
}

function lang(line) {
	return $("div#ui_messages span#"+line).html();
}
function langp(line,p) {
	var s;
	s=lang(line);
	return s.replace(/%s/g,p);
}


//
// Functions for obtaining table,id,field,nr information
//
function get_cell(obj){var i;c=obj.attr("class").split(" ");i=String(c[1]);i=i.replace("id","");return{'table':c[0],'id':i,'field':c[2]};}
function get_table(obj){var c;c=get_cell(obj);return c.table;}
function get_field(obj){var c;c=get_cell(obj);return c.field;}
function get_id(obj) {
	var id,classes;
	classes = obj.attr("class").split(" ");
	id=jQuery.grep(classes, function (a) {
		return (a.indexOf("id")==0);
	});
	id=String(id);
	id=id.replace("id","");
	return id;
}
function get_nr(obj) {
	var id,classes;
	classes = obj.attr("class").split(" ");
	id=jQuery.grep(classes, function (a) {
		return (a.indexOf("nr")>=0);
	});
	id=String(id);
	id=id.replace("nr","");
	return id;
}
function get_subclass(sub,obj) {
	var s,classes;
	classes = obj.attr("class").split(" ");
	s=jQuery.grep(classes, function (a) {
		return (a.indexOf(sub)>=0);
	});
	s=String(s);
	s=s.replace(sub,"");
	return s;
}
function get_name(obj) {
	var s;
	s=$('td.str:first',obj).text();
	if (s=='') s=$('td.name:first',obj).text();
	if (s=='') s=$('div.name:first',obj).text();
	s=stripTags(s);
	return trim(s);
}

//
// Other functions
//

function stripTags(s) {
	return s.replace(/<\/?[^>]+>/gi, '');
}

function trim(s) {
  s=s.replace(/^\s+/,'');
  s=s.replace(/\s+$/,'');
  return s;
}

function randomPassword(length) {
	var chars,pass,x,i;
  chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
  pass = "";
  for(x=0;x<length;x++) {
     i = Math.floor(Math.random() * 62);
     pass += chars.charAt(i);
  }
  return pass;
}

function serialize(sel) {
	var s,sel,i;
	s="";
	sel=$(sel);
	$(sel).each(function() {
		i=get_id($(this));
		s+="&id[]="+i;
	});
	return s.substr(1);
}

function rowsEvenOdd() {
	if ($('.grid tbody tr .file').length==0) {
		$("table.grid tbody tr").removeClass("oddrow").removeClass("evenrow");
		$("table.grid tbody tr:visible:odd").addClass("oddrow");
		$("table.grid tbody tr:visible:even").addClass("evenrow");
	}
}

function setCurrent(c) {
	if (!$('.grid').hasClass('files')) {
		current=$('.grid tbody tr.current').removeClass('current');
		oldIndex=$('.grid tbody tr:visible').index(current);
		switch (c) {
			case 'first':
				current=$('.grid tbody tr:visible:first').addClass('current');
				break;
			case 'down':
				index=oldIndex+1;
				len=$('.grid tbody tr:visible').length-1;
				if (index>=len) index=len;
				current=$('.grid tbody tr:visible:eq('+index+')').addClass('current');
				break;
			case 'up':
				index=oldIndex-1;
				if (index<0) index=0;
				current=$('.grid tbody tr:visible:eq('+index+')').addClass('current');
				break;
			default:
				current=$('.grid tbody tr:visible:eq('+c+')').addClass('current');
		}
	}
	// newIndex=$('.grid tbody tr:visible').index(current);
	// return newIndex;
}

function get_ext(s){var a,s;s=String(s);a=s.split(".");return a[a.length-1];}

function pathdecode(s) { s=s.replace(/___/g,"/"); return s; }
function pathencode(s) { s=s.replace(/\//g,"___"); return s; }
function site_url(s) {
	if (s==undefined)
		s=config.site_url;
	else
		s=config.site_url+s;
	return s;
}

// regex callback, makes bold
function regBoldReplace(all,match) {return '<b>'+match+'</b>';}

function cachedThumb(src) {	return src;}

function flash(swf,w,h) {
	var attr,f;
	attr='width="'+w+'" height="'+h+'"';
	f='<object class="flash" data="#swf#" #attr# classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" >' +
		'<param name="allowScriptAccess" value="sameDomain" />' +
		'<param name="movie" value="#swf#" />' +
		'<param name="quality" value="high" />' +
		'<param name="bgcolor" value="#ffffff" />' +
		'<embed class="flash" src="#swf#" quality="high" bgcolor="#ffffff" #attr# allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer" />' +
		'</object>';
	f=f.replace(/#swf#/g,swf);
	f=f.replace(/#attr#/g,attr);
	return f;
}
