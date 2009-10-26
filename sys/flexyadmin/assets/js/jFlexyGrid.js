function doGrid() {

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
															uploadFile=$('.ui-dialog input.filemanager').val();
															// prevent the 'fakedisk' in name of IE8
															backslash=uploadFile.lastIndexOf('\\');
															if (backslash>0) uploadFile=uploadFile.substr(backslash+1);
															$('.ui-dialog .ui-dialog-buttonpane').add('.ui-dialog a').add('.ui-dialog form').hide();
															$('.ui-dialog .ui-dialog-content').prepend("Uploading '<i>"+uploadFile+"</i>' <img src='"+site_url("sys/flexyadmin/assets/icons/wait.gif")+"' align='right' />");
															$("form.upload").submit();
															// $(dialog).dialog("destroy");
															// alert('Hups');
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

	//
	// File & Grid (Life filter/search field)
	//
	if (isGrid || isFile ) {

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
		
		if ($.browser.safari)
			$("table.grid .self_parent").add("table.grid .uri").addClass('hiddenCell').show();
		else
			$("table.grid .self_parent").hide();
		
		
		//
		// Filter/Search rows
		//
		filter=$("table.grid");
		if (filter.length>0) {
			// keep table width
			w=$(filter).width();
			$(filter).width(w);

			// set filter
			if (isFile && !isGrid) {
				affects='tbody tr .file';
			}
			else {
				affects='tbody tr';
			}

			$(filter).filterable({ affects: affects, queryCss:'filter' }, function(keyCode){
				// reset rows
				rowsEvenOdd();
				
				// make selected text bold
				s=$("input.filter").attr("value");
				if (s.length==2) {
					$('table.grid:not(.thumbs) tbody td:not(.id):not(.order):not(.thumb):not(.media):not(.medias):not(.edit):not(.b):not(.url)').each(function(){
						t=$(this).html();
						t=t.replace(new RegExp('<(/|)b>','g'),'');
						$(this).html(t);
					});
				}
				if (s.length>=3) {
					$('table.grid:not(.thumbs) tbody td:not(.id):not(.order):not(.thumb):not(.media):not(.medias):not(.edit):not(.b):not(.url)').each(function(){
						t=$(this).html();
						t=t.replace(new RegExp('<(/|)b>','g'),'');
						if (s!='') {t=t.replace(new RegExp('('+s+')','gi'),regBoldReplace);}
						$(this).html(t);
					});
				}
				
				// check keypresses for special actions
				switch (keyCode) {
					case keyEnter:
						if (!$('.grid').hasClass('files')) {
							select=$('.grid: tr.current');
							if (select.length==0) {
								select=$('.grid tbody tr:first');
							}
							id=get_id(select);
							table=get_table(select);
							url=site_url('admin/show/form/'+table+':'+id);
							location.href=url;
						}
						break;

					case keyUp:
						setCurrent('up');
						break;
					case keyDown:
						setCurrent('down');
						break;

					default:
						setCurrent('first');
				}
								
			});

			// place filter input on other place
			filter_input=$("div.filter input");
			$(filter_input).addClass("filter").attr({title:'search / filter'});
			$("tr.caption tr").append('<td class="filter">');
			$("td.filter").html(filter_input);
			$("td.filter input").wrap('<span class="help '+config.help_filter+'"></span>');
		}

		//
		// Selecting multiple
		// 

		// init buttons
		if (isFile && isThumbs) {
			delButton=$('table.grid thead div.delete');
			delButtons=$('table.grid tbody div.file div.toolbar span div.delete');
			selButton=$('table.grid thead div.select');
			selButtons=$('table.grid tbody div.file div.toolbar span div.select');
		}
		else {
			delButton=$('table.grid thead div.delete');
			delButtons=$('table.grid tbody tr div.delete');
			selButton=$('table.grid thead div.select');
			selButtons=$('table.grid tbody tr div.select');
		}

		// set state of delete selection button
		function lightDeleteButton() {
			var active=0;
			if (isFile && isThumbs)
				active=$('table.grid tbody div.file.selected').length;
			else
				active=$('table.grid tbody tr.selected').length;
			if (active>0)
				$(delButton).removeClass('inactive');
			else
				$(delButton).addClass('inactive');
		}

		// toggle state of Select buttons
		$(selButtons).click(function(){
			if ($(this).hasClass('selected')) {
				$(this).removeClass('selected');
				$(this).parents('.selected').removeClass('selected');
			}
			else {
				$(this).addClass('selected');
				if (isFile && isThumbs)
					$(this).parents('div.file').addClass('selected');
				else
					$(this).parents('tr').addClass('selected');
			}
			lightDeleteButton();
		});

		// Select or Deselect all
		$(selButton).click(function(){
			$(selButtons).toggleClass('selected');
			if (isFile && isThumbs)
				$('.grid tbody div.file').toggleClass('selected');
			else
				$('.grid tbody tr').toggleClass('selected');
			lightDeleteButton();
		});
		


		//
		// Delete Confirm Dialog
		//
		
		$("table.grid thead div.delete").add('table.thumbs thead div.delete').addClass('inactive').add(delButtons).click(function () {
			if ($(this).hasClass('item')) {
				// deselect others
				$(selButtons).removeClass('selected');
				$('.grid .selected').removeClass('selected');
				// select current
				if (isFile && isThumbs) {
					$(this).parents('.toolbar').find('.select').addClass('selected');
					$(this).parents('.file').addClass('selected');
				}
				else {
					$(this).parents('td').find('.select').addClass('selected');
					$(this).parents('tr').addClass('selected');
				}
				lightDeleteButton();
			}
			clean_message();
			var id = new Array();
			var name = new Array();
			nr=0;
			if (isFile && isThumbs)
				selected=$('table.thumbs tbody div.file.selected');
			else
				selected=$('table.grid tbody tr.selected');
			if (selected.length>0) {
				$(selected).each(function(){
					id[nr]=get_id($(this));
					name[nr]=get_name($(this));
					if (name[nr]=="") name[nr]="id:"+id[nr];
					nr++;
				});
				if (isFile) {
					if (isThumbs)
						path=$('table.grid tbody div.file div.path:first').text();
					else
						path=$('table.grid tr td.thumb div.path:first').text();
					uri=site_url('admin/filemanager/confirm/'+path);
				}
				else {
					table=get_table($('table.grid tbody tr:first'));
					uri=site_url('admin/edit/confirm/'+table);
				}
				confirm_dialog(uri,name,id);
			}
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
		if ($("table.grid .order").length>0) {
			// change ui: remove normal order arrows, and place one item
			help=$("table.grid tbody td.order:first span.help");
			help='help_'+get_subclass("help_",$(help));
			$("table.grid tbody td.order").empty().append('<span class="help '+help+'"><div class="icon order" title="order"></div></span>');
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
				forceHelperSize: true,
				forcePlaceholderSize: true,
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
							rowsEvenOdd();
						}
					});
				}
			});
			// reset style
			$("table.grid tbody").attr("style","");
			isSortable=false;
		}
		
		//
		// Make sure columns can be sortable if ordering by drag 'n drop is not on.
		//
		else {
			isSortable=true;	}
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
			rowsEvenOdd();
			    });
	}

};



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
