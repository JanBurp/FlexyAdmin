Array.prototype.unique = function(){
	var r = new Array();
	o:for(var i = 0; i < this.length; i++) {
		for(var x = 0; x < r.length; x++) {
			if(r[x]==this[i]) {
				continue o;	
			}
		}
		r[r.length] = this[i];
	}
	return r;
};


function doForm() {


	// prevent leaving form page when not saved
	(function($) {
		$.prevent_leaving_unsaved_page = function(options) {
			var saved=true;
			$('input').add('textarea').change(function(){ saved=false; });
			$('form').submit(function(){ saved=true; });
			// Other ways of leaving the page have standard browsers dialog
			$(window).bind('beforeunload', function(){ 
				if (!saved) return lang('dialog_beforeunload');
			});

		};
	})(jQuery);
	$.prevent_leaving_unsaved_page();


	//
	// conditional formfield showing
	//
	if (typeof(formFieldWhen)!="undefined") {
		var fields=$('.flexyFormField');
		$(fields).each(function(){
			var name=$(this).attr('class');
			name=name.split(' ');
			name=name[2];
			if (typeof(formFieldWhen[name])!="undefined") {
				var when=formFieldWhen[name];
				// ok found one, bind an action
				$('#'+when.actor).change(function(){
					var val=$(this).val();
					// first hide, and then check if it can be shown
					$('.flexyFormField.'+when.field).add('#'+name).addClass('hidden');
					switch (when.operator) {
						case '=':
							if (val==when.value) $('.flexyFormField.'+when.field).add('#'+name).removeClass('hidden');
							break;
						case '>':
							if (val>when.value) $('.flexyFormField.'+when.field).add('#'+name).removeClass('hidden');
							break;
						case '<':
							if (val<when.value) $('.flexyFormField.'+when.field).add('#'+name).removeClass('hidden');
							break;
					}
					if ( ! $('.flexyFormField.'+when.field).hasClass('hidden')) $('.flexyFormField.'+when.field).css({'min-height':'27px'});
				});
			}
		});
	}
	
	//
	// Nice Select styling
	//
	if (config.form_nice_dropdowns) {
    
    // Set default options for multiselect
    $.ech.multiselect.prototype.options = {
      header:false,
      height:'auto',
      minWidth:'auto',
      selectedList:2,
      noneSelectedText:'-',
      selectedText:'',
      multiple:true
    };
    
    // Set different dropdown multiselect fields
		$('.flexyFormField select:not(.multiple)').multiselect({
      multiple:false,
      click:    function(event,ui){
                  if ($(event.target).hasClass('image_dropdown')) {
                                      var values = $(event.target).multiselect("getChecked").map(function(){return this.value;}).get();
                    update_image_dropdown(event.target,values);
                  }
                }
		});
		$('.flexyFormField select.multiple.dropdown').multiselect({
      create:       function(event,ui){
                      update_dropdown(event.target);
                    },
      click:        function(event,ui){
                			// make sure optgroups are all same checked/unchecked
                      var checked=ui.checked;
                      var text=ui.text;
                      var inputs=$(event.target).parent('.flexyFormField').find('input[title="'+text+'"]');
                      inputs.attr('checked',checked);
                    },
      beforeclose : function(event,ui){
                      update_dropdown(event.target);
                    }
		});
		$('.flexyFormField select.multiple.image_dropdown').multiselect({
      selectedList      :false,
      selectedText      :'',
      click             : function(event,ui){
                      			// make sure optgroups are all same checked/unchecked
                            var checked=ui.checked;
                            var text=ui.text;
                            var inputs=$(event.target).parent('.flexyFormField').find('input[title="'+text+'"]');
                            inputs.attr('checked',checked);
                      			// get values and update dropdown
                            var values = $(event.target).multiselect("getChecked").map(function(){return this.value;}).get();
                      			update_image_dropdown(event.target,values);
                          }
		});
    
		// styling of multiple
		$('.flexyFormField.dropdown button').css({width:460});
    $('.flexyFormField.dropdown.rel button').css({width:430});
		$('.flexyFormField.dropdown .ui-multiselect-menu').css({width:458});
		$('.flexyFormField.dropdown.has_button button').css({width:430});
		$('.flexyFormField.dropdown.has_button .ui-multiselect-menu').css({width:428});
		$('.flexyFormField.image_dropdown.multiple ul.values').css({width:425,position:'relative'});
		$('.flexyFormField.image_dropdown button.ui-multiselect').css({width:420,height:38,'float':'right','margin-top':-42});
	}

		
	//
	// Timepicker and Datepicker dialog
	//
	$("form input.datetime").each(function(){
		var dt=$(this).attr('value');
		var c=$(this).attr('class');
		var id=$(this).attr('id');
		var did=id+'__date';
		var tid=id+'__time';
		dt=dt.split(' ');
		var date=dt[0];
		var time=dt[1];
		$(this).after('<input id="'+tid+'" name="'+tid+'" class="'+c+' time" value="'+time+'">');
		$(this).after('<input id="'+did+'" name="'+did+'" class="'+c+' date" value="'+date+'">');
		$(this).hide();
		$('#'+did).add('#'+tid).change(function(){
			dt=$('#'+did).val()+' '+$('#'+tid).val();
			$('#'+id).attr('value',dt);
		});
		
	});
	$("form input.date").datepicker({ dateFormat: 'yy-mm-dd' });
	$("form input.time").timepicker({ hourCols:6, minDivision:5, hourCaption:lang('ui-timepicker-choose-hours'), minuteCaption:lang('ui-timepicker-choose-minutes'), closeOnFormclick: true });


	//
	// ColorPicker dialog
	//
	$("form input.rgb").each(function(){
		var color=$(this).val();
		$(this).after('<div class="rgbColor" style="background-color:'+color+';" color="'+color+'"></div>');
		$(this).change(function(){
			var show=$(this).next('.rgbColor');
			var color=$(this).val();
			$(show).css({'background-color':color}).attr('color',color);
		});
		$(this).next('.rgbColor').click(function(){
			var self=$(this);
			$(this).ColorPicker({
				onBeforeShow: function () {
					var color=$(this).attr('color');
					$(this).ColorPickerSetColor(color);
				},
				onSubmit: function(hsb, hex, rgb, el) {
					var color='#'+ hex.toUpperCase();
					$(el).css({'background-color':color}).attr('color',color).prev('input.rgb:first').val(color);
					$(el).ColorPickerHide();
				},
				onChange: function (hsb, hex, rgb) {
					var color='#'+ hex.toUpperCase();
					$(self).css({'background-color':color}).prev('input.rgb:first').val(color);
				}
			});
		}).trigger('click');
	});
	
	

	//
	// Password create button
	//
	$("form input.password:not(.matches)").after('<span class="button">'+lang('form_random_password')+'</span>');
	$("form span.button").click(function() {
		pwd=randomPassword(14);
		$(this).prev("input.password").attr("value",pwd);
    $("form input.matches").attr("value",pwd);
	});
	
	
	//
	// Media dropdown
	//
  var mediaFields=$('div.image_dropdown');
  $(mediaFields).each(function(){
    var field=$(this);
    var select=$(field).find('select');
    var options=$(select).find('option');
    if (options.length>0) {
      $(select).change(function() {
        update_image_dropdown(select);
      });
    }
  });
  

  function update_dropdown(select) {
    // update selectlist and counting
    var checkedItems=$(select).multiselect("getChecked");
    var allvalues = checkedItems.map(function(){return this.value;}).get();
    var values=new Array();
		for (i=0;i<allvalues.length;i++) { if ( $.inArray(allvalues[i],values)==-1 ) values.push(allvalues[i]); }
    var textValues='';
    $(checkedItems).each(function(){
      textValues += ' | ' + $(this).attr('title');
    });
    textValues=textValues.substr(3);
    var textSpan=$(select).parent('.flexyFormField').find('button span:not(.ui-icon)');
    $(textSpan).html(textValues);
  }

	function update_image_dropdown(select,values) {
		var list=$(select).prevAll('ul.values');
		var options=$(select).find('option');
		var multiple=$(select).hasClass('medias');
		var path=$(select).attr("path")+"/";

    // get current values (in current order)
    var current_values=[];
    $(list).find('li img').each(function(){
      var src=$(this).attr('src');
      var split=src.lastIndexOf('/');
      if (split>0) src=src.substr(split+1);
      var split=src.lastIndexOf('___');
      if (split>0) src=src.substr(split+3);
      current_values.push(src);
    });
    
    
    if (typeof(values)=='undefined') {
      var values = current_values;
    }
    else {
  		// remove doubles
      values = values.unique();
    
      // add values that are not present, and remove values that need to be removed
      var len=current_values.length;
      var new_values=current_values;
      for (var i=0; i<len; i++) {
        var found=$.inArray(current_values[i],values);
        if (found>=0) {
          // keep value in place by removing the new value
          values.splice(found,1);
        }
        else {
          // remove from current
          new_values.splice(i,1);
        }
      };

      // merge current values with new values
      values=new_values.concat(values);
      values=values.unique();
    }

		// remove old thumb & clean value
		$(list).find('li').remove();
		if (multiple) $(select).prevAll('input').attr('value','');

    // show new thumbs
		var value='';
		for (var i=0;i<values.length;i++) {
			var src=values[i];
			if (multiple && value!='')
				value=value+'|'+src;
			else
				value=src;
			src=path+src;
			var ext=get_ext(src);
			var size=32;
			if (multiple) size=25;
			if (ext=='swf' || ext=='flv') {
				$(list).append('<li>'+flash(src,size,size)+'</li>');
			}
			else {
				src=cachedThumb(src);
				$(list).append('<li><img class="media" src="'+src+'" /></li>');
			}
		}
		$(select).prevAll('input').attr('value',value);
	}

		
	// Media(s) select by click
	$('div.media ul.choices').add('div.medias ul.choices').add('div.medias:not(.image_dropdown) ul.values').click(function(e){
    if (!$(this).hasClass('sorting')) {
  		var target=e.target; // img tag
  		var type='media';
  		if ($(target).parents('div:first').hasClass('medias')) type='medias';
  		var thumbsrc=$(target).attr('src');

  		if (type=='media') {
  			var src=pathdecode(thumbsrc);
  			src=src.substr(src.lastIndexOf('/')+1);
  			$(target).parents('div.media').children('ul.values').empty();
  			$(target).parents('div.media').children('ul.values').append('<li><img class="zoom" src="'+thumbsrc+'" alt="'+src+'" title="'+src+'" /></li>');
  			// change the hidden input value
  			$(target).parents('div.media').children('input:first').attr('value',src);
  		}
  		else {
        // Hier verschil herkennen tussen click en drag 'n drop... Alleen doorgaan als géén drag 'n drop
        var item=$(target).parent('li:first').clone(true);
        var values=$(target).parents('div.medias').children('ul.values');
        if ($(target).parents('ul:first').hasClass('choices'))
          $(target).parents('div.medias').children('ul.values').append(item);
        else
          $(target).parents('div.medias').children('ul.choices').append(item);
        $(target).parent('li:first').remove();
        $('.zoomThumb').hide();
        update_values_list($(values));
  		}
    }
    $(this).removeClass('sorting');
	});

	// Media, empty by a click
	$('div.media ul.values').click(function(){
		$(this).empty();
		$(this).parent('div.media').children('input:first').attr('value','');
	});

	// update the hidden field with the sortable values list
	function update_values_list(obj) {
		if ($(obj).hasClass('values')) {
			var value='';
			$(obj).children('li').each(function(){
				var src=$(this).children('img:first').attr('src');
				if (src!=undefined) {
					src=pathdecode(src);
					src=src.substr(src.lastIndexOf('/')+1);
					value+='|'+src;
				}
			});
			value=value.substr(1);
			$(obj).parent('.flexyFormField:first').children('input:first').attr('value',value);
		}
	}
	
	// dragndrop ordering of medias
	$('div.medias ul').sortable({
		connectWith: 'div.medias ul',
    start: function(event,ui) {
      // make sure clicking can detect if sorting is in progress...
      $(this).addClass('sorting');
    },
		update: function(event,ui) {
			update_values_list($(this));
		}
	});

	// handle empty images with drag n drop
	$('form').submit(function(){
		$('form .image_dragndrop input').each(function(){
			var value=$(this).attr('value');
			if (value=='flexyadmin_empty_image.gif') $(this).attr('value','');
		});
	});


	// Ordering of many type ordered_list
	$('div.ordered_list ul').sortable({
		connectWith: 'div.ordered_list ul',
		update: function(event,ui) {
			if ($(this).hasClass('list_values')) {
				value='';
				$(this).children('li').each(function(){
					id=$(this).attr('id');
					value+='|'+id;
				});
				value=value.substr(1);
				$(this).parent('.flexyFormField').children('input:first').attr('value',value);
			}
		}
	});	
	
};