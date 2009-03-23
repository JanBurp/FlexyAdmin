$(document).ready(function() {

	// Check modes
	isForm=$("#content").hasClass("form");
	isGrid=$("#content").hasClass("grid");
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
						ext=get_ext(media);
						if (ext=='swf' || ext=='flv') {
							$("p.image_dropdown select.medias").before(flash(src,32,32));
						}
						else {
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
			dialog.html('<form class="upload" method="post" action="admin/filemanager/upload/'+path+'" enctype="multipart/form-data">'+
									'<input type="file" name="file" value="" class="filemanager" /></form>');
			$(dialog).dialog({
				title:"Upload a file to '"+path+"'",
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
		});
		//
		// Make sure columns can be sortable
		//
		isSortable=isGrid;
	}


	//
	// File & Grid (Life filter/search field)
	//
	if (isGrid || isFile) {

		//
		// Filter/Search rows
		//
		if (isFile && !isGrid) {
			filter=$("table.grid");
			$(filter).filterable({ affects: 'tbody tr .file' });
		}
		else {
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
		}

		//
		// Delete Confirm Dialog
		//
		dialog=$("#ui");
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
			url="admin/ajax/edit/"+cell.table+"/"+cell.id+"/"+cell.field+"/"+value;
			// ajax request
			$(this).css('cursor','wait');
			$.post(url,"",function(data) {
					if (data!="") {
						// error
						alert(data);
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
			// make sortable
			items=$("table.grid tbody");
			$(items).sortable({
				axis:'y',
				cursor:'move',
				start:function() {
					// set width of helper cells same as width of grid cells
					$("table.grid tbody tr:first td").each(function() {
						w=$(this).width()+"px";
						nr=get_nr($(this));
						$("tr.ui-sortable-helper td.nr"+nr).css({ width:w });
					});
				},
				update:function() {
					// prepare ajax request
					//ser=$(items).sortable("serialize",{attribute:"class",expression:" id(.+)[ ]",key:"id[]"});
					ser=serialize("table.grid tbody tr");
					table=$("table.grid").attr("class");
					table=table.replace("grid","");
					table=$.trim(table);
					url="admin/ajax/order/"+table;
					// ajax request
					$.post(url,ser,function(data) {
							if (data!="") {
								// error
								alert(data);
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

});




//
// Functions for confirm
//

function confirm_dialog(obj,item) {
	dialog.html("Delete <em>'"+item+"'</em><br/><strong>Are you sure?</strong>");
	$(dialog).dialog({
		title:"Confirm",
		modal:true,
		buttons: ({ cancel	: function(){	$(dialog).dialog("destroy"); $(obj).attr({"href":href}); },
								yes			: function(){
														$(dialog).dialog("destroy");
														location.replace(href+"/confirm");
													}
						 }),
		close: function(){$(dialog).dialog("destroy"); $(obj).attr({"href":href});}
	});
}
function clean_message() {$("#message").html("");}

function zoom_dialog(obj) {
	src=$(obj).attr('src');
	w=$(obj).attr('zwidth');
	h=$(obj).attr('zheight');
	ext=get_ext(src);
	if (ext=="swf" || ext=="flc") {
		dialog.html(flash(src,w,h));
	}
	else {
		dialog.html('<a href="javascript:close_dialog()"><img src="'+src+'" width="'+w+'" height="'+h+'" alt="'+src+'" /></a>');
	}
	$(dialog).dialog({
		title:src.substr(src.lastIndexOf("/")+1)+" ("+w+"x"+h+")",
		modal:true,
		width: w+'px',
		heigth: h+'px',
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


//
// Functions for obtaining table,id,field,nr information
//
function get_cell(obj){c=obj.attr("class").split(" ");i=String(c[1]);i=i.replace("id","");return{'table':c[0],'id':i,'field':c[2]};}
function get_table(obj){c=get_cell(obj);return c.table;}
function get_field(obj){c=get_cell(obj);return c.field;}
function get_id(obj) {
	classes = obj.attr("class").split(" ");
	id=jQuery.grep(classes, function (a) {
		return (a.indexOf("id")==0);
	});
	id=String(id);
	id=id.replace("id","");
	return id;
}
function get_nr(obj) {
	classes = obj.attr("class").split(" ");
	id=jQuery.grep(classes, function (a) {
		return (a.indexOf("nr")>=0);
	});
	id=String(id);
	id=id.replace("nr","");
	return id;
}
function get_subclass(sub,obj) {
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
function serialize(sel) {
	s="";
	sel=$(sel);
	$(sel).each(function() {
		i=get_id($(this));
		s+="&id[]="+i;
	});
	return s.substr(1);
}

function get_ext(s){s=String(s);a=s.split(".");return a[a.length-1];}

function flash(swf,w,h) {
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
