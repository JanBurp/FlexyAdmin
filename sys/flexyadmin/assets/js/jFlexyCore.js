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
	isGrid=($("#content").hasClass("grid")); // && !$('.grid').hasClass('res_menu_result'));
	isGridAction=$(".grid").hasClass("actionGrid");
  // isTree=$("#content").hasClass("tree");
	isFile=$("#content").hasClass("filemanager");
	if (!isGrid && isFile)	{	isGrid=$("#content").hasClass("list"); }
	if (isFile)							{ isThumbs=$("#content").hasClass("icons");}
	isSortable=false;
	//

	if (isGrid || isFile) doGrid();

  // Message & error boxes
  $('#messages').delay(5000).fadeOut(2000);
  $('#errors').each(function(){
    var html=$('#errors').html();
    info_dialog(html);
  }).hide();

	// plugin to prepare html form for ui-tabs
	(function($) {
		$.fn.formTabs = function(opts) {
			return this.each(function() {
				var fieldsets=$(this).find('fieldset:not(.formbuttons):not(.flexyFormButtons)');
				var tablist='<ul>';
				fieldsets.each(function(){
					// add tab
					var title=$(this).find('legend').html();
					var set_id='tab_'+$(this).attr('id');
					tablist+='<li><a href="#'+set_id+'">'+title+'</a></li>';
					// div around fieldset
					$(this).find('legend').remove();
					$(this).replaceWith('<div id="'+set_id+'">'+$(this).html()+'</div>');
				});
				tablist+='</ul>';
				// put buttons fieldset before tabpanes
				var buttons=$(this).find('.formbuttons').add('.flexyFormButtons');
				$(this).prepend(buttons);
				// add ul
				$(this).prepend(tablist);
			});
		};
	})(jQuery);
	$('#content form').formTabs().tabs();

	if (isForm) doForm();

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

  //
  // Submenu
  //
  $('#subitems').each(function(){
    var headers=$(this).children('h1');
    var menu='<ul class="submenu">';
    $(headers).each(function(){
      var uri=$(this).html();
      uri=uri.toLowerCase().replace(/ /g,'_');
      $(this).addClass(uri);
      $(this).next('.content').addClass(uri);
      menu+='<li class="'+uri+'">'+$(this).html()+'</li>';
    });
    menu+='</ul>';
    $(this).before(menu);
    $('.submenu li').click(function(){
      $('.submenu li').removeClass('current');
      var uri=$(this).attr('class');
      $('#subitems>*').hide();
      $('#subitems .'+uri).show();
      $(this).addClass('current');
      window.history.pushState("", "", "admin/help/"+uri);
    });
    var page=$(this).attr('page');
    if (page=='') {
      $('.submenu li:first').trigger('click');
    }
    else {
      $('.submenu li.'+page).trigger('click');
    }
  });
  
  
  // //
  // // Help Dialog
  // //
  // $('#menu a.API_help').click(function(){
  //   var href=$(this).attr('href');
  //   $('#footer').after('<div id="help"></div>');
  //   $('div#help').load(href+' #content',function(){
  //     help_dialog();
  //   });
  //   return false;
  // });
  
	//
	// some styling
	//
	$('.after_form').each(function(){
		var fh=$('fieldset.formfields').height();
		var mh=$('#menu').height();
		$(this).css({'top':-(-fh+mh-30)});
	});

});



//
// Functions for confirm
//
function confirm_dialog(uri,name,id) {
	showName='';
	if (name.length>1)
		showName+=langp('dialog_delete_more',name.length);
	else {
		for (x in name) {showName+=" + '"+name[x]+"'";}
		showName=showName.substr(3);
	}
	dialog.html(langp("dialog_delete_sure",showName));
	$(dialog).dialog({
		title:lang("dialog_title_confirm"),
		modal:true,
		width:500,
		buttons: ({
								cancel	: function(){	$(dialog).dialog("destroy"); },
								yes			: function(){
														$('.ui-dialog .ui-dialog-buttonpane').add('.ui-dialog a').hide();
														$('.ui-dialog .ui-dialog-content').append("<img src='"+site_url("sys/flexyadmin/assets/icons/wait.gif")+"' align='right' />");
														var value='';
														for(x in id) {value+=':'+id[x];}
														value=value.substr(1);
														$('.ui-dialog .ui-dialog-content').append('<form method="POST" id="confirmform" action="'+uri+'"><input type="hidden" name="items" value="'+value+'" /><input name="confirm" value="confirmed" type="hidden" /></form>');
														$('#confirmform').submit();
													}
						 }),
		close: function(){$(dialog).dialog("destroy");}
	});
	changeButt("cancel",lang("dialog_cancel"));
	changeButt("yes",lang("dialog_yes"));
}

function info_dialog(info) {
  dialog.html(info);
	$(dialog).dialog({
    title:lang("dialog_title_warning"),
		modal:true,
    minWidth:500,
    buttons: ({ ok  : function(){  $(dialog).dialog("destroy"); } }),
    close: function(){$(dialog).dialog("destroy");}
	});
	changeButt("ok",lang("dialog_ok"));
}

// function help_dialog() {
//   dialog.html($('#help #content').html());
//   $(dialog).dialog({
//     title:"Help",
//     modal:true,
//     minWidth:550,
//     height:650,
//     create:function(){
//       $('.ui-dialog-content h1').click(function(){
//         var content=$(this).next('.content');
//         $('.ui-dialog-content .content').not(content).slideUp().removeClass('open');
//         if (!$(content).hasClass('open')) $(content).slideDown().addClass('open');
//       });
//     },
//     close: function(){$(dialog).dialog("destroy");}
//   });
// }

function clean_message() {$("#message").html("");}

function showHelpItems() {
	var ShowDelay;
	var HideDelay;
	$("span.help").children().removeAttr("title");
	$("span.help").mouseenter(function() {
		var obj=$(this);
		var helpName=get_subclass("help_",obj);
		if (helpName!='') {
			ShowDelay=setTimeout( function() {		
				var helpTxt=$("#help_messages span#help_"+helpName).html();
				var html=helpTxt;
				$(Popup).html(html).fadeIn(150);
				HideDelay=setTimeout( function(){
					$(Popup).fadeOut(1000);
				},5000 );
			},1000);
		}
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
			if (widthThumb<100) widthThumb=100;
			if (widthThumb>250) widthThumb=250; // max width
			heightThumb=$('.autoZoom').height();
			if (heightThumb<100) heightThumb=100;
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
	$('img.zoom').add('.flash .zoom').not('form ul li img.zoom').not('form ul li .flash .zoom').fullsize({triggerIcon:false,forceTitleBar:true});
	$('form ul li img.zoom').add('form ul li .flash .zoom').fullsize({triggerIcon:false,triggerEvent:'dblclick',forceTitleBar:true});
}

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
	$("div.ui-dialog-buttonset button").each(function(){
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
function get_class(obj,nr){var i;c=obj.attr("class").split(" ");return c[nr];}
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

function get_prefix(s,sep) {
	if (sep==undefined) sep='_';
	var a=s.split(sep);
	return a[0];
}

function stripTags(s) {
	return s.replace(/<\/?[^>]+>/gi, '');
}

function trim(s) {
  s=s.replace(/^\s+/,'');
  s=s.replace(/\s+$/,'');
  return s;
}

function randomPassword(length) {
  var s='';
  while (s.length<length) {
    s=s+(((1+Math.random())*0x10000)|0).toString(36);
  }
  return s.substr(0,length);
  // };
  // return (S4()+S4()+S4()+S4()+S4()+S4()+S4()+S4());
  // 
  // // var chars,pass,x,i;
  // //   chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
  // //   pass = "";
  // //   for(x=0;x<length;x++) {
  // //      i = Math.floor(Math.random() * 62);
  // //      pass += chars.charAt(i);
  // //   }
  // var randomstring = Math.random().toString(36).slice(-length);
  // return randomstring;
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
