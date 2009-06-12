$(document).ready(function() {

	// Check modes
	dialog=$("#ui");
	Popup=$("#popup");
	isForm=$("#content").hasClass("form");
	isGrid=$("#content").hasClass("grid");
	isTree=$("#content").hasClass("tree");
	isFile=$("#content").hasClass("filemanager");
	if (!isGrid && isFile)	{	isGrid=$("#content").hasClass("list"); }
	if (isFile)							{ isThumbs=$("#content").hasClass("icons");}
	isSortable=false;
		

	//
	// Form
	//

	if (isForm) {

		//
		// Datepicker dialog
		//
		$("form input.date").datepicker({ dateFormat: 'yy-mm-dd' });

		//
		// Password create button
		//
		$("form input.password").after('<span class="button">'+lang('form_random_password')+'</span>');
		$("form span.button").click(function() {
			pwd=randomPassword(10);
			$(this).prev("input.password").attr("value",pwd);
		});
	
	
		//
		// Media dropdown
		//
		options=$("p.image_dropdown select.media option");
		if (options.length>0) {
			path=$("p.image_dropdown select.media").attr("path")+"/";

			$("select.media").change(function() {
				media=$("select.media option:selected").attr("value");
				// remove old thumb
				$("p.image_dropdown.media img").remove();
				$("p.image_dropdown.media object").remove();
				// show new thumb
				src=path+media;
				ext=get_ext(media);
				if (ext=='swf' || ext=='flv') {
					$("p.image_dropdown select.media").before(flash(src,32,32));
				}
				else {
					src=cachedThumb(src);
					$("p.image_dropdown select.media").before('<img class="media" src="'+src+'" />');
				}
			});
		}

		// Multiple media dropdown
		options=$("p.image_dropdown select.medias option");
		if (options.length>0) {
			path=$("p.image_dropdown select.medias").attr("path")+"/";

			$("select.medias").change(function() {
				// remove old thumbs
				$("p.image_dropdown.medias img").remove();
				$("p.image_dropdown.media object").remove();
				// show new thumbs
				medias=$("select.medias option:selected");
				if (medias.length>0) {
					$(medias).each(function() {
						src=path+$(this).attr("value");
						ext=get_ext(src);
						if (ext=='swf' || ext=='flv') {
							$("p.image_dropdown select.medias").before(flash(src,32,32));
						}
						else {
							src=cachedThumb(src);
							$("p.image_dropdown select.medias").before('<img class="media" src="'+src+'" />');
						}
					});
				}
			});
		}

	}




	//
	// File
	//
	if (isFile) {

		//
		// Upload button and dialog
		//
		$(".upload").click(function() {
			path=get_subclass("path_",$(this));
			dialog.html('<form class="upload" method="post" action="'+site_url("admin/filemanager/upload/"+path)+'" enctype="multipart/form-data">'+
									'<input type="file" name="file" value="" class="filemanager" /></form>');
			$(dialog).dialog({
				title:langp("dialog_title_upload"),
				modal:true,
				width:400,
				buttons: ({ cancel	: function(){	$(dialog).dialog("close"); },
										upload	: function(){
																$(dialog).dialog("destroy");
																$("form.upload").submit();
															}
								 }),
				close: function(){$(dialog).dialog("destroy"); }
			});
			changeButt("cancel",lang("dialog_cancel"));
			changeButt("upload",lang("dialog_upload"));
		});
		//
		// Make sure columns can be sortable
		//
		isSortable=isGrid;
	}


	//
	// File & Grid (Life filter/search field)
	//
	if (isGrid || isFile || isTree ) {

		//
		// Make sure grid is minimal 600px width
		if ($("table.grid").width() < 600) {
			$("table.grid").css({width:"600px"});
		}

		//
		// if 'self_parent' is there hide that column, add parent_id class to row, and visual nodes to next (str) column
		//
		$("table.grid td.self_parent").each(function(){
			html=$(this).html();
			if (html.length>0) {
				// get parent_id
				parent_id=html.substring(1,html.indexOf(" ")-1);
				$(this).parent("tr").addClass("parent_id_"+parent_id);
				// create new html
				if (html.indexOf("/")<0)
					newHtml='<span class="emptynode">&nbsp;</span>';
				else {
					newHtml='<span class="emptynode">&nbsp;</span>'+html;
					newHtml=newHtml.replace(/[^\/]*\//g,'<span class="emptynode">&nbsp;</span>');
					newHtml=newHtml.substr(0,newHtml.lastIndexOf('>')+1);
				}
			}
			next=$(this).parent("tr").children("td.str:first");
			if (html.length>0) {			
				newHtml=newHtml+'<span class="lastnode">&nbsp;</span>'+$(next).html();
			}
			else {
				newHtml=$(next).html();
			}
			$(next).html(newHtml);
		});
		$("table.grid .self_parent").hide();
		
		
	
		//
		// Filter/Search rows
		//
		if (isFile && !isGrid) {
			filter=$("table.grid");
			$(filter).filterable({ affects: 'tbody tr .file' });
		}
		else {
			if (isTree) {
				filter=$("table.grid");
				$(filter).filterable({ affects: 'tbody li' });
			}
			if (isGrid) {
				filter=$("table.grid");
				$(filter).filterable({ affects: 'tbody tr' });
			}
		}
		if (filter.length>0) {
			// keep table width
			w=$(filter).width();
			$(filter).width(w);
			// place filter input on other place
			filter_input=$("div.ui-filterable-query input");
			$(filter_input).addClass("filter").attr({title:'search / filter'});
			$("tr.caption tr").append('<td class="filter">');
			$("td.filter").html(filter_input);
			$("td.filter input").wrap('<span class="help '+config.help_filter+'"></span>');
		}

		//
		// Delete Confirm Dialog
		//
		if (isFile) {
			// add events to remove buttons (filemanager view)
			remove=$(".grid a.delete");
			remove.click(function () {
				clean_message();
				href=$(this).attr("href");
				$(this).removeAttr("href");
				if (isThumbs) {
					paths=href.split("/");
					file=paths[paths.length-1];
				}
				else {
					cell=$(this).parent(".edit");
					file=get_id($(cell));
				}
				confirm_dialog(this,file);
			});
		}
		else {
			// add events to delete buttons (grid view)
			buttons=$(".grid a.delete");
			buttons.click(function () {
				clean_message();
				id=get_id($(this));
				name=$(".grid tr.id"+id+" td.str").html();
				if (name=="") name="id:"+id;
				href=$(this).attr("href");
				$(this).removeAttr("href");
				confirm_dialog(this,name);
			});
		}

		//
		// img Zoom Popup Dialog
		//
		$('img.zoom').click(function() {
			zoom_dialog($(this));
		});
		$('.flash .zoom').click(function() {
			zoom_dialog($(this));
		});

	}


	//
	// Grid only (Edit Bool fields & Order editing)
	//
	if (isGrid && !isFile) {

		//
		// Editable boolean fields
		//
		bFields=$(".grid td.b");
		$(bFields).click(function() {
			obj=this;
			cell=get_cell($(this));
			id=cell.id;
			if ($(this).children("div:first").hasClass("no")) {	value="1"; } else {	value="0"; }
			url=site_url("admin/ajax/edit/"+cell.table+"/"+cell.id+"/"+cell.field+"/"+value);
			// ajax request
			$(this).css('cursor','wait');
			$.post(url,"",function(data) {
					if (data!="") {
						ajaxError(data);
					}
					else {
						// change the status
						html=$(obj).html();
						if (value=="1")
							html=html.replace(/no/g,"yes");
						else
							html=html.replace(/yes/g,"no");
						$(obj).html(html);
						$(obj).css('cursor','pointer');
					}
				});
		})

		//
		// Setting order by drag 'n drop
		//
		order=$("table.grid .order");
		if (order.length>0) {
			// change ui: remove normal order arrows, and place one item
			$("table.grid tbody td.order").empty().append('<div class="icon order" title="order"></div>');
			$("table.grid thead th.order").empty();

			// set width and height of cells!
			$("table.grid tbody tr:first td").each(function() {
				if ($(this).css("display")=="none") w=0; else	w=$(this).width()+"px";
				nr=get_nr($(this));
				$("table.grid tbody tr td.nr"+nr).css({ width:w });
			});
			
			// make sortable
			items=$("table.grid tbody");
			startPos=0;
			$(items).sortable({
				// axis:'y',
				grid: [25, 1],
				handle:'td.order',
				cursor:'move',
				appendTo:"body",
				start: function(event,ui) {
					// hide all branches if any
					id=get_id($(ui.item));
					if (hide_branches(id)) {
						$(ui.helper).children("td.str:first").addClass("foldednode");
					}
					// remove current
					$("table.grid tbody tr").removeClass("current");
				},
				sort:function(event,ui){
					if (startPos==0) {
						startPos=ui.position.left;
					}
				},
				stop: function(event,ui){
					// show the branches again
					show_branches(id);
					$(ui.item).children().removeClass("foldednode");
				},
				update:function(event,ui) {
					table=$("table.grid").attr("class");
					table=table.replace("grid","");
					table=$.trim(table);
					id=get_id($(ui.item));
					
					endPos=ui.position.left;
					shifted=(endPos-startPos)/25;
					
					// check if there are branches
					if ($("table.grid tr td.self_parent").length>0) {
						// check if dropped on another branch
						nextRow=$(ui.item).next("tr");
						if (nextRow.length>0) {
							newParentId=get_subclass("parent_id_",$(nextRow));
							if (newParentId=="") newParentId=0;
						}
						else
							newParentId=0;

						// Check if shifted to another level under a branch?
						if ((newParentId==0) && (shifted>0)) {
							prevRow=$(ui.item).prev("tr");
							if (prevRow.length>0) {
								newParentId=get_id($(prevRow));
								if (newParentId=="") newParentId=0;
							}
						}
						
						if (newParentId>=0) {
							// Yes, it has been moved to another branch
							// Set parent_id in table
							parentId=get_subclass("parent_id_",$(ui.item));
							$("table.grid tbody tr#"+id).removeClass("parent_id_"+parentId);
							if (newParentId>0) $("table.grid tbody tr#"+id).addClass("parent_id_"+newParentId);
							if (shifted>0)
								shiftNodes(id,1);
							else {
								// Count Current nodes
								SpanNodes=$("table.grid tbody tr#"+id+" td.str:first").children(".emptynode");
								// Count Next nodes
								nextRow=$("table.grid tbody tr#"+id).next("tr");
								nextSpanNodes=$(nextRow).children("td.str:first").children(".emptynode");
								if (SpanNodes.length!=nextSpanNodes.length) {
									shiftNodes(id,nextSpanNodes.length-SpanNodes.length,newParentId);
								}
							}
							// Set own parent with AJAX request
							url=site_url("admin/ajax/edit/"+table+"/"+id+"/self_parent/"+newParentId);
							$.get(url,"",function(data) {
										if (data!="") {
											ajaxError(data);
										}
									});							
						}
					}
					
					// show the branches again
					show_branches(id);
					$(ui.item).addClass("current");
					
					// prepare ajax request to re-order the table in the database
					ser=serialize("table.grid tbody tr");
					url=site_url("admin/ajax/order/"+table);
					// ajax request
					$.post(url,ser,function(data) {
							if (data!="") {
								ajaxError(data);
							}
							else {
								// reorder the order classes
								$("table.grid tbody tr").removeClass("oddrow").removeClass("evenrow");
								$("table.grid tbody tr:odd").addClass("oddrow");
								$("table.grid tbody tr:even").addClass("evenrow");
							}
						});
				}
			});
			// reset style
			$("table.grid tbody").attr("style","");
		}

		//
		// Make sure columns can be sortable if ordering by drag 'n drop is not on.
		//
		else { isSortable=true;	}
	}

	//
	// Sortable columns in Grid or File (list) modes.
	//

	if (isSortable) {
		grid=$("table.grid");
		$(grid).tablesorter();
		$(grid).bind("sortStart",function() {
			$(grid).css("cursor","wait");
    });
		$(grid).bind("sortEnd",function() {
			$(grid).css("cursor","default");
			// reorder the order classes
			$("table.grid tbody tr").removeClass("oddrow").removeClass("evenrow");
			$("table.grid tbody tr:odd").addClass("oddrow");
			$("table.grid tbody tr:even").addClass("evenrow");
    });
	}



	//
	// Help
	//

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




});




//
// Functions for confirm
//

function confirm_dialog(obj,item) {
	dialog.html(langp("dialog_delete_sure",item));
	$(dialog).dialog({
		title:lang("dialog_title_confirm"),
		modal:true,
		width:500,
		buttons: ({ cancel	: function(){	$(dialog).dialog("destroy"); $(obj).attr({"href":href}); },
								yes			: function(){
														$(dialog).dialog("destroy");
														location.replace(href+"/confirm");
													}
						 }),
		close: function(){$(dialog).dialog("destroy"); $(obj).attr({"href":href});}
	});
	changeButt("cancel",lang("dialog_cancel"));
	changeButt("yes",lang("dialog_yes"));
}
function clean_message() {$("#message").html("");}

function zoom_dialog(obj) {
	var src,w,h,ext;
	src=$(obj).attr('src');
	w=$(obj).attr('zwidth');
	h=$(obj).attr('zheight');
	imgRatio=w/h;
	// set sizes not bigger than screen
	scrW=$("body").outerWidth()-50;
	scrH=$("body").outerHeight()-100;
	if ((w<scrW) && (h<scrH)) {
		dw=w;
		dh=h;
	}
	else {
		if (w>scrW && h>scrH) {
			if (scrW/w < scrH/h) {
				dw=scrW;
				dh=dw/imgRatio;
			}
			else {
				dh=scrH;
				dw=dh*imgRatio;
			}
		}
		else {
			if (w>scrW) {
				dw=scrW;
				dh=dw/imgRatio;
			}
			else {
				dh=scrH;
				dw=dh*imgRatio;
			}
		}	
	}
	// what file type?
	ext=get_ext(src);
	if (ext=="swf" || ext=="flc") {
		dialog.html(flash(src,dw,dh));
	}
	else {
		// is it a cached thumb?
		i=src.indexOf("_thumbcache");
		if (i>=0) {
			src=src.substr(i+12); // 11 = length of '_thumbcache'
			src=pathdecode(src);
		}
		dialog.html('<a href="javascript:close_dialog()"><img src="'+src+'" width="'+dw+'" height="'+dh+'" alt="'+src+'" /></a>');
	}
	$(dialog).dialog({
		title:src.substr(src.lastIndexOf("/")+1)+" ("+w+"x"+h+")",
		modal:true,
		width: dw+'px',
		heigth: dh+'px',
		position: 'center',
		closeOnEscape:true,
		dialogClass:'zoom',
		resizable:false,
		scrollable:false,
		close: function() {$(dialog).dialog("destroy"); }
	});
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


function hide_branches(parent_id) {
	var id;
	var hidden=false;
	$("table.grid tbody tr.parent_id_"+parent_id).each(function(){
		hidden=true;
		id=get_id($(this));
		hide_branches(id);
		$(this).hide().addClass("hidden_branch");
	});
	return hidden;
}
function show_branches(id) {
	$("table.grid tbody tr.hidden_branch").clone().show().removeClass("hidden_branch").addClass("current").insertAfter("table.grid tbody tr#"+id);
	$("table.grid tbody tr.hidden_branch").remove();
}

function shiftNodes(id,add,newParentId) {
	var n,empty,last;
	if (add<0) {
		for (n=add; n<0; n++) {
			$("table.grid tbody tr#"+id+" td.str:first span.emptynode:first").remove();
			$("table.grid tbody tr.hidden_branch").each(function(){
				$(this).children("td").children("span.emptynode:first").remove();
			});
		}
		empty=$("table.grid tbody tr#"+id+" td.str:first span.emptynode").length;
		if (empty==0) $("table.grid tbody tr#"+id+" td.str:first span.lastnode").remove();
	}
	if (add>0) {
		emptyNode="<span class=\"emptynode\">&nbsp;</span>";
		last=$("table.grid tbody tr#"+id+" td.str span.lastnode").length;
		if (last==0) {
			$("table.grid tbody tr#"+id).addClass("parent_id_"+newParentId);
			$("table.grid tbody tr#"+id+" td.str:first").prepend("<span class=\"lastnode\">&nbsp;</span>");
		}
		for (n=0; n<add; n++) {
			$("table.grid tbody tr#"+id+" td.str span.lastnode").before(emptyNode);
			$("table.grid tbody tr.hidden_branch").each(function(){
				$(this).children("td.str").children("span.lastnode").before(emptyNode);
			});
		}
	}
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

//
// Other functions
//
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

function get_ext(s){var a,s;s=String(s);a=s.split(".");return a[a.length-1];}

function pathdecode(s) { s=s.replace(/__/g,"/"); return s; }
function pathencode(s) { s=s.replace(/\//g,"__"); return s; }
function site_url(s) {
	if (s=="")
		s=config.site_url;
	else
		s=config.site_url+s;
	return s;
}


function cachedThumb(src) {
	return src;
}

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
