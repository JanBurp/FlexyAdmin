function doForm() {

	//
	// Make sure media fields with selects are good height
	//
	// $('.form_field select').each(function(){
	// 	selHeight=$(this).outerHeight();
	// 	imgList=$(this).parent('.form_field:first').children('ul.multiple:first');
	// 	if (imgList.length>0) { selHeight+=$(imgList).outerHeight(); }
	// 	$(this).parent('.form_field:first').css({height:selHeight});
	// });
	$('.form_field.image_dragndrop').each(function(){
		selHeight=$(this).children('ul.choices').outerHeight();
		selHeight+=$(this).children('ul.values').outerHeight();
		$(this).css({height:selHeight+4});
	});	

	//
	// conditional formfield showing
	//
	if (typeof(formFieldWhen)!="undefined") {
		var fields=$('.form_field');
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
					$('.form_field.'+when.field).add('#'+name).addClass('hidden');
					switch (when.operator) {
						case '=':
							if (val==when.value) $('.form_field.'+when.field).add('#'+name).removeClass('hidden');
							break;
						case '>':
							if (val>when.value) $('.form_field.'+when.field).add('#'+name).removeClass('hidden');
							break;
						case '<':
							if (val<when.value) $('.form_field.'+when.field).add('#'+name).removeClass('hidden');
							break;
					}
					if ( ! $('.form_field.'+when.field).hasClass('hidden')) $('.form_field.'+when.field).css({'min-height':'27px'});
				});
			}
		});
	}
	

	//
	// Nice Select styling #BUSY: also the multiple and media
	//
	$('.form_field select:not(.multiple)').multiselect({header:false,multiple:false,selectedList:4,height:'auto'}).bind('multiselectclick', function(event,ui){
		if ($(event.target).hasClass('image_dropdown')) {
			var values = $(event.target).multiselect("getChecked").map(function(){return this.value;}).get();
			update_image_dropdown(event.target,values);
		}
	});
	$('.form_field select.multiple:not(.image_dropdown)').multiselect({header:false,selectedList:4,height:'auto',noneSelectedText:''});
	$('.form_field select.multiple.image_dropdown').multiselect({header:false,height:'auto',minWidth:'auto',selectedList:false,selectedText:'',noneSelectedText:''}).bind('multiselectclick', function(event,ui){
		// make sure optgroups are all same checked/unchecked
		var checked=ui.checked;
		var text=ui.text;
		// find same
		var inputs=$(event.target).siblings('div.ui-multiselect-menu').find('input[title="'+text+'"]');
		inputs.attr('checked',checked);
		// get values
		var allvalues = $(event.target).multiselect("getChecked").map(function(){return this.value;}).get();
		// remove double values
		var values = new Array();
		for (i=0;i<allvalues.length;i++) { if ( $.inArray(allvalues[i],values)==-1 ) values.push(allvalues[i]); }
		update_image_dropdown(event.target,values);
	});
	// styling of multiple
	$('.form_field.image_dropdown.multiple ul.values').css({width:392,'float':'left',position:'relative','z-index':10});
	$('.form_field.image_dropdown.multiple button.ui-multiselect').css({width:460,height:38,'float':'right','z-index':0,'margin-top':-42});

		
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
			$(this).ColorPicker({
				onBeforeShow: function () {
					self=this;
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
	$("form input.password").after('<span class="button">'+lang('form_random_password')+'</span>');
	$("form span.button").click(function() {
		pwd=randomPassword(10);
		$(this).prev("input.password").attr("value",pwd);
	});
	
	
	//
	// Remove double selections (last updates / ordered by name)
	//
	$("div.image_dropdown select.medias optgroup:first option:selected").attr('selected','');
	
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
	function update_image_dropdown(select,values) {
		var list=$(select).prevAll('ul.values');
		// var select=$(field).find('select');
		var options=$(select).find('option');
		var multiple=$(select).hasClass('medias');
		var path=$(select).attr("path")+"/";
		// remove old thumb & clean value
		$(list).find('li').remove();
		if (multiple) $(select).prevAll('input').attr('value','');
		// show new thumb
		if (typeof(values)=='undefined') {
			var medias=$(select).find('option:selected');
			values =  new Array;
			$(medias).each(function(){
				values.push($(this).attr("value"));
			});
		}
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
	$('div.media ul.choices').add('div.medias ul.choices').add('div.medias ul.values').click(function(e){
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
			$(obj).parent('.form_field:first').children('input:first').attr('value',value);
		}
	}
	
	// dragndrop ordering of medias
	$('div.medias ul').sortable({
		connectWith: 'div.medias ul',
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
				$(this).parent('.form_field').children('input:first').attr('value',value);
			}
		}
	});	
	
};