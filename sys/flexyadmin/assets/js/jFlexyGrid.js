function doGrid() {

	//
	// Upload button and dialog (one file upload)
	//
  if (oldIE || !config.multiple_upload) {
    $(".upload").click(function() {
      path=get_subclass("path_",$(this));
      dialog.html('<form class="upload" method="post" action="'+site_url("admin/filemanager/upload/"+path)+'" enctype="multipart/form-data">'+
                  '<input type="file" name="file" value="" class="filemanager" /></form>');
      $(dialog).dialog({
        title:langp("dialog_title_upload"),
        modal:true,
        width:400,
        buttons: ({ cancel  : function(){  $(dialog).dialog("close"); },
                    upload  : function(){
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
  }
  
  else {
    //
    // pupload, mutlipe upload
    //
    $(".upload").click(function() {
      var path=get_subclass("path_",$(this));
      // 1- First show dialog
      dialog.html('<div id="uploader"></div>');
      $(dialog).dialog({
        title:langp("dialog_title_upload"),
        modal:true,
        width:600,
        height:340,
        resizable:false,
        open: function() {
          // 2- Init uploader
          var uploader=$("#uploader").plupload({
            // General settings
            runtimes : 'html5,html4',
            url : site_url("admin/filemanager/upload/"+path+'/ajax'),
            max_file_size : '10mb',
            // chunk_size : '1mb',
            // unique_names : true,
            filters : [ {title : "--", extensions : config.file_types} ],
        		preinit : {
        			Init: function() {
                var txt=$('.plupload_header_text').html();
                txt+='<br/>'+langp('dialog_minimal_size');
                $('.plupload_header_text').html(txt);
                $('.plupload_header_content').append('<label>'+langp('dialog_prefix')+'</label><input class="plupload_prefix" name="plupload_prefix" value="">');
        			},
        		},
            init : {
              BeforeUpload: function(up,file) {
                var prefix = $('.plupload_prefix').val();
                if (prefix!=='') {
                  up.settings.multipart_params = {'prefix':prefix};
                }
              },
              FileUploaded: function(up, file, info) {
                response=info.response;
                response=$.parseJSON(response);
                if (response.error!='') {
                  var div=$('.plupload_message.ui-state-error');
                  if (div.length==0) {
                    $('.plupload_header_content').append('<div class="plupload_message ui-state-error"></div>');
                    div=$('.plupload_message.ui-state-error');
                  }
                  $(div).append('<p>'+response.error+'</p>');
                  file.status=4;
                }
                // else {
                //   // no error, show thumb
                //   var thumb=response.thumb;
                //   console.log(thumb);
                // }
              },
              UploadComplete: function(obj,files) {
                // window.location.reload();
              }
            }
          });
          // webkit hack to make sure upload button works
          $('#uploader_browse').click(function(){
            $('#uploader > div.plupload').css({'z-index':'10000'});
            $('#uploader > div.plupload').trigger('click');
          });
        },
        close: function(){
          $(dialog).dialog("destroy");
          window.location.reload();
        }
      });
    });
    
  }
  
  
	
	//
	// Make sure columns can be sortable
	//
	var isSortable=isGrid;

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
    function make_tree(first) {
      $('table.grid .emptynode').remove();
      $('table.grid .lastnode').remove();
      var treeDepth=0;
      var parentLevels=new Array();
      $("table.grid tbody tr").each(function(){
        var row=this;
  			var html=$('td.self_parent',row).html();
        if (first===true) {
          var parent_id=html.substring(1,html.indexOf(" ")-1);
        }
        else {
          var parent_id=get_subclass("parent_id_",$(row));
          if (parent_id=="") parent_id=0;
          parent_id=parseInt(parent_id);
        }
        
        var title=$('td.str:first',row);
        var strTitle=$(title).html();
        strTitle=strTitle.split('/');
        strTitle=strTitle[strTitle.length-1];

        if (first) $(this).addClass("parent_id_"+parent_id);
        
        
        if (parent_id!=0) {
          if (typeof(parentLevels[parent_id])!=="undefined") {
            treeDepth=parentLevels[parent_id];
          }
          else {
            treeDepth++;
            parentLevels[parent_id]=treeDepth;
          }
          // create new html
          var newHtml='';
          for (var i=0; i<treeDepth-1; i++) {
            newHtml+='<span class="emptynode">&nbsp;</span>';
          };
          newHtml+='<span class="lastnode">&nbsp;</span>' + strTitle;
    			$(title).html(newHtml);
  			}
        else {
          treeDepth=0;
        }
        
  		});
    }
    if ($("table.grid tr td.self_parent").length>0) make_tree(true);
		
		if ($.browser.safari)
			$("table.grid .self_parent").add("table.grid .uri").addClass('hiddenCell').show();
		else
			$("table.grid .self_parent").hide();
		
		
		//
		// Filter/Search rows
		//
    
		var filter=$("table.grid:first");
		if (filter.length>0 && !isGridAction && !$(filter).hasClass('home')) {
      
      // multiple filter
      if ( $('.extended_search').length>0) {
        
        extended_search_show_existing();
        extended_search_bind_actions();
        
        // Vul huidige zoektermen in en laat zien
        function extended_search_show_existing() {
          var json=$('table.grid').attr('data-search');
          if (json!=='' && json.substr(0,1)==='{') {
            var search=JSON.parse(json);
            var new_row = $('.extended_search td .extended_search_row').clone();
            $.each(search,function(index,item){
              var child=Number(index)+1;
              if (index>0) $('.extended_search td').append(new_row.clone());
              var row=$('.extended_search td .extended_search_row:nth-child('+child+')');
              $('.extended_search_and select',row).val(item.and);
              $('.extended_search_field select',row).val(item.field);
              if (item.settings.exact===true) $('.extended_search_equal select',row).val('exact');
              if (item.settings.word===true) $('.extended_search_equal select',row).val('word');
              $('.extended_search_term input',row).val(item.term);
            });
          }
        }
        // Zorg dat alle knoppen werken en dat een verandering een nieuwe zoekresultaat in gang zet
        function extended_search_bind_actions() {
          $('.extended_search *').unbind();
          // add new row
          var new_row = $('.extended_search .extended_search_row:first').clone();
          $('.extended_search .extended_search_plus').click(function(){
            $('.extended_search td').append(new_row.clone());
            extended_search_bind_actions();
          });
          // remove row
          $('.extended_search .extended_search_remove').click(function(){
            $(this).parent('.extended_search_row').remove();
          });
          // submit
          $('.extended_search_submit').click(function(){
            extended_search_submit();
          });
          $('.extended_search').keypress(function(event){
            if (event.which == 13 ) extended_search_submit();
          });
        }
        // Submit
        function extended_search_submit() {
          var search = extended_search_create_search_json();
          var url=$(filter).attr('url')+'/0/order/'+$(filter).attr('order')+'?search='+search;
          location.href=url;
        }
        // Zet zoekform om in json
        function extended_search_create_search_json() {
          var search={};
          var nr=0;
          $('.extended_search_row').each(function(){
            var and = $('.extended_search_and select',this).val();
            var field = $('.extended_search_field select',this).val();
            var equal = $('.extended_search_equal select',this).val();
            var term = $('.extended_search_term input',this).val();
            search[nr] = {"field":field,"term":term,"and":and,"settings":{}};
            if (equal==='exact') search[nr]['settings']['exact'] = true;
            if (equal==='word') search[nr]['settings']['word'] = true;
            nr++;
          });
          var json=JSON.stringify(search);
          $('input.filter').val(json);
          return json;
        }
        
      }

			if ($(filter).hasClass('pagination')) {
        
				// add filter input
				var search=$(filter).attr('data-search');
        
        if ($('.extended_search').length>0) {
          $("tr.caption:first tr td:first").append('<span class="filter"><span class="help '+config.help_filter+'"><input class="filter" type="text" value="'+search+'"/></span><img class="extended_search_button" src="sys/flexyadmin/assets/icons/action_add.gif"></span>');
        }
        else {
          $("tr.caption:first tr td:first").append('<span class="filter"><span class="help '+config.help_filter+'"><input class="filter" type="text" value="'+search+'"/></span>');
        }
        
        var filterBox=$('input.filter');
        $('.extended_search_button').click(function(){
          $('tr.extended_search').removeClass('hidden');
          $('.grid .filter').addClass('hidden');
        });
        if (search.substr(0,1)==='{') {
          $('tr.extended_search').removeClass('hidden');
          $('.grid .filter').addClass('hidden');
        }
				// bind action
				$(filterBox).keypress(function(e){
          var search=$(this).val();
          if (search.length>0) {
            $('.extended_search_button').removeClass('hidden');
          }
          else {
            $('.extended_search_button').addClass('hidden');
          }
          if (e.which==keyEnter) {
  					var url=$(filter).attr('url')+'/0/order/'+$(filter).attr('order')+'?search='+search;
            location.href=url;
          }
				});
			}
			else {
				// keep table width
				w=$(filter).width();
				$(filter).width(w);
				// set filter
				if (isFile && !isGrid) {affects='tbody tr .file';} else {affects='tbody tr';}

				$(filter).filterable({ affects: affects, queryCss:'filter' }, function(keyCode){
					// reset rows
					rowsEvenOdd();
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
        var filter_input=$("div.filter:first input");
        $(filter_input).addClass("filter").attr({title:'search / filter'});
        $("tr.caption:first tr td:first").append('<span class="filter">');
        $("span.filter:first").html(filter_input);
        $("span.filter:first input").wrap('<span class="help '+config.help_filter+'"></span>');
			}
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
				selected=$('table.thumbs tbody div.file.selected:not(.filtered)');
			else
				selected=$('table.grid tbody tr.selected:not(.filtered)');
			if (selected.length>0) {
				$(selected).each(function(){
					id[nr]=get_id($(this));
					name[nr]=get_name($(this));
					if (name[nr]=="") name[nr]="id:"+id[nr];
					nr++;
				});
				if (isFile) {
					var path='';
					if (isThumbs)
						path=$('table.grid tbody div.file div.path').first().text();
					else
						path=$('table.grid tbody tr td.thumb div.path').first().text();
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
	// Grid only (Edit Fields & Order editing)
	//
	if (isGrid && !isFile) {
    
    // Iets doen met geselecteerde gebruikers
    if ( $('.grid table.cfg_users').length>0 ) {
      
      var button=$('a.button.selected_users');
      var href=$(button).attr('href');
      $(button).click(function(e){
        e.preventDefault();
        var selected=$('table.grid tbody tr.selected:not(.filtered)');
        var url = '';
        $(selected).each(function(index,el){
          var user_id = Number( $(el).attr('id') );
          if (user_id>0) {
            if (url==='') url=href+'?';
            url += 'users[]='+user_id+'&';
          }
        });
        if (url!=='') {
          location.href=url;
        }
        return false;
      });
    }
    

		//
		// Editable Fields
		//
    var gridEl=$('.grid table');
    if ($(gridEl).hasClass('editable')) {

      // EDIT Boolean fields
      $('td.editable.b',gridEl).unbind('click').click(function() {
        var obj=this;
        var cell=get_cell($(this));
        var id=cell.id;
        var value=0;
        if ($(this).children("div:first").hasClass("no")) value="1";
        // var url=site_url("admin/ajax/edit/"+cell.table+"/"+cell.id+"/"+cell.field+"/"+value);
        var url=site_url("admin/ajax/edit/");
        var data={'table':cell.table,'id':cell.id,'field':cell.field,'value':value};
        // ajax request
        $(this).css('cursor','wait');
        $.post(url,data,function(data) {
          // console.log(data);
          if (data.error) {
            ajaxError(data.error);
          }
          else {
            var html=$(obj).html();
            if (value=="1")
              html=html.replace(/no/g,"yes");
            else
              html=html.replace(/yes/g,"no");
            $(obj).html(html);
          }
          $(obj).css('cursor','pointer');
        },'json');
      });
      
      // EDIT NORMAL fields
      $('td.editable:not(.b)').attr('contenteditable','true').unbind('focusin').focusin(function(){
        var obj=this;
        var old_value=$(this).text();
        $(this).unbind('focusout').focusout(function(){
          var new_value=$(this).text();
          if (new_value!=old_value) {
            var cell=get_cell($(this));
            var url=site_url("admin/ajax/edit");
            var data={'table':cell.table,'id':cell.id,'field':cell.field,'value':new_value};
            // ajax request
            $(obj).css('cursor','wait');
            $.post(url,data,function(data) {
              if (data.error) {
                ajaxError(data.error);
                $(obj).text(old_value);
              }
              else {
                if (data.validation_errors) {
                  info_dialog(data.validation_errors);
                  $(obj).text(old_value).focus();
                }
                else {
                  new_value=data.new_value;
                  $(obj).text(new_value);
                }
              }
              $(obj).css('cursor','pointer');
            },'json');
          }
        });
      });
    }

		//
		// Setting order by drag 'n drop
		//
		if ($("table.grid .order").length>0) {
			// change ui: remove normal order arrows, and place one item
			help=$("table.grid tbody td.order:first span.help");
      if (help.length>0) {
  			help='help_'+get_subclass("help_",$(help));
  			$("table.grid tbody td.order").empty().append('<span class="help '+help+'"><div class="icon order" title="order"></div></span>');
  			$("table.grid thead th.order").empty();
      }
 				
			// set width and height of cells!
			$("table.grid tbody tr:first td").each(function() {
				if ($(this).css("display")=="none") w=0; else	w=$(this).width()+"px";
				nr=get_nr($(this));
        // $("table.grid tbody tr td.nr"+nr).css({ width:w });
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
					table=$("table.grid").data("table");
					id=parseInt(get_id($(ui.item)));
					
					endPos=ui.position.left;
					shifted=(endPos-startPos)/25;
          
					// check if there are branches
					if ($("table.grid tr td.self_parent").length>0) {
						
						// check if dropped on another branch
						nextRow=$(ui.item).next("tr");
						if (nextRow.length>0) {
							newParentId=get_subclass("parent_id_",$(nextRow));
							if (newParentId=="")
                newParentId=0;
              else
                newParentId=parseInt(newParentId);
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
            
						// Set parent_id in row
            var row=$("table.grid tbody tr#"+id);
						parentId=get_subclass("parent_id_",$(ui.item));
            if (parentId=="")
              parentId=0;
            else
              parentId=parseInt(parentId);

            var c=$(row).attr('class');
            // console.log(c);
            c=c.replace(/parent_id_\d*/g, '')
            c+=' parent_id_'+newParentId;
            $(row).attr('class',c);
            // console.log('NEW',parentId,newParentId,c);

						if (newParentId>=0) {
							// Yes, it has been moved to another branch
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
              
							// Set own parent (and reset order)
							var url=site_url("admin/ajax/edit/");
              var data={'table':table,'id':id,'field':'self_parent','value':newParentId};
							$.post(url,data,function(data) {
                if (data.error) ajaxError(data.error);
							},'json');
						}
            
            
  					// show the branches again
  					show_branches(id);
            $(ui.item).addClass("current");
  
            make_tree();
  				}
					
					// prepare ajax request to re-order the table in the database
					var url=site_url("admin/ajax/order/");
          var ser=serialize("table.grid tbody tr");
          var data={'table':table,'ids':ser};
					// ajax request
					$.post(url,data,function(data) {
            if (data.error)
              ajaxError(data.error);
            else
              rowsEvenOdd();
            // location.reload();
					},'json');
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
			isSortable=true;
		}
	}
	
  
	// replace pagination links with current order & search field
	$("table.grid").find('tfoot .pagination a').each(function(){
		grid=$("table.grid");
		var order=$(grid).attr('order');
    var search=$(grid).attr('data-search');
		if (order=='') order='name';
    var base_url=$(this).attr('href');
    var lastslash=base_url.lastIndexOf('/offset')+7;
    var offset=base_url.substr(lastslash+1);
    if (offset==='') offset=0;
    base_url=base_url.substr(0,lastslash);
		var url=base_url+'/'+offset+'/order/'+order+'?search='+search;
		$(this).attr('href',url);
	});
  
  
	//
	// Sortable columns in Grid or File (list) modes / Pagination
	//

	if (isSortable) {
		grid=$("table.grid");
		if ($(grid).hasClass('pagination')) {
			// sort with pagination needs to reload page with another sort field
			$(grid).find('tr.heading th').each(function(){
				if (!$(this).hasClass('edit')) {
					$(this).addClass('header').click(function(){
						if (isFile)
							var field=get_class($(this),0);
						else
							var field=get_class($(this),1);
						if ($(this).hasClass('headerSortDown')) field='_'+field;
						// ok now reload the page, starting from page 0
            var search=$(grid).attr('data-search');
						var url=$(grid).attr('url')+'/0/order/'+field+'?search='+search;
            location.href=url;
					});
				}
			});
		}
		else {
			// live sorting, first find ordered col
      var order=$(grid).attr('order');
      if (order!='') {
        var desc=false;
        if (order.substr(0,1)=='_') {
          order=order.substr(1);
          desc=true;
        }
        var sortcol=$(grid).find('tr.heading th.'+order).index();
        if (sortcol>0) $(grid).tablesorter({sortList:[[sortcol,desc]]});
      }
      // if ($(grid).find('tbody tr').length>0) {
      //   var cols=$(grid).find('tr.heading th');
      //   var sortcol=0;
      //   var desc=0;
      //   for (var i=0; i<cols.length; i++) {
      //     if ( ($(cols[i]).hasClass('headerSortUp')) || ($(cols[i]).hasClass('headerSortDown')) ) {
      //       sortcol=i;
      //       if ($(cols[i]).hasClass('headerSortUp')) desc=1;
      //     }
      //   };
      //         console.log(sortcol,desc);
      //         $(grid).tablesorter({sortList:[[sortcol,desc]]});
      // }
			else
        $(grid).tablesorter();
			
			$(grid).bind("sortStart",function() {
				$(grid).css("cursor","wait");
			});
			$(grid).bind("sortEnd",function() {
				$(grid).css("cursor","default");
				rowsEvenOdd();
			});
		}
	}


	//
	// ActionGrid
	//
	if (isGridAction) {
		totalActions=$('.actionGrid tbody tr').length;
		actionNr=0;
		$('.actionGrid thead table tr:first td:first').before('<td>&nbsp;</td>').after('<td id="actionCounter"></td>');
		$('#actionCounter').html(actionNr/totalActions*100+' %');
		$('.actionGrid tbody tr:first').each(function(){
			doAction($(this));
		});
	}
	
	function doAction(obj) {
		$(obj).children('td:first').html('<div class="icon wait" /></div>');
		uri=$(obj).children('td.uri').html();
		if (uri!='') {
			$.ajax({
				type: "POST",
				url: uri,
				async:'false',
        dataType: 'json',
				success: function(data){
          if (data.error) {
            ajaxError(data.error);
          }
					else {
            if (data._message) {
              $(obj).children('td:nth-child(2)').html(data._message);
            }
						$(obj).children('td:first').html('<div class="icon yes" /></div');
						$(obj).next('tr:first').each(function(){
							actionNr++;
							$('#actionCounter').html(((actionNr+1)/totalActions*100).toFixed(1)+' %');
							doAction($(this));
						});
					}
				},
				error: function() {
					$(obj).children('td:first').html('<div class="icon no" /></div');
					$(obj).next('tr:first').each(function(){
						actionNr++;
						$('#actionCounter').html(((actionNr+1)/totalActions*100).toFixed(1)+' %');
						doAction($(this));
					});
				}
				
			});
		}
	}


};

function get_current_grid_page_uri() {
	var grid=$('table.grid');
	return '/offset/'+$(grid).attr('offset')+ '/order/'+$(grid).attr('order')+ '?search='+$(grid).attr('data-search');
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

