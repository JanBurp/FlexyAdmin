function doForm() {
	
	// conditional formfield showing
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
	// Make sure media fields with selects are good height
	//
	$('.form_field select').each(function(){
		selHeight=$(this).outerHeight();
		imgList=$(this).parent('.form_field:first').children('ul.multiple:first');
		if (imgList.length>0) { selHeight+=$(imgList).outerHeight(); }
		$(this).parent('.form_field:first').css({height:selHeight});
	});
	$('.form_field.image_dragndrop').each(function(){
		selHeight=$(this).children('ul.choices').outerHeight();
		selHeight+=$(this).children('ul.values').outerHeight();
		$(this).css({height:selHeight+4});
	});		
		

	//
	// Timepicker and Datepicker dialog
	//
	$("form input.date").datepicker({ dateFormat: 'yy-mm-dd' });
	$("form input.time").datepicker({ dateFormat: 'yy-mm-dd', duration:'', showTime:true, constrainInput:false,time24h:true });

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
	options=$("div.image_dropdown select.media option");
	if (options.length>0) {
		path=$("div.image_dropdown select.media").attr("path")+"/";

		$("select.media").change(function() {
			media=$("select.media option:selected").attr("value");
			// remove old thumb
			$("div.image_dropdown.media ul li").remove();
			// show new thumb
			src=path+media;
			ext=get_ext(media);
			if (ext=='swf' || ext=='flv') {
				$("div.image_dropdown ul").append('<li>'+flash(src,32,32)+'</li>');
			}
			else {
				src=cachedThumb(src);
				$("div.image_dropdown ul").append('<li><img class="media" src="'+src+'" /></li>');
			}
		});
	}

	// Multiple media dropdown
	options=$("div.image_dropdown select.medias option");
	if (options.length>0) {
		path=$("div.image_dropdown select.medias").attr("path")+"/";

		$("select.medias").change(function() {
			// remove old thumbs & clean value
			$("div.image_dropdown.medias ul li").remove();
			$(this).parent('.form_field').children('input:first').attr('value','');
			// show new thumbs & change value
			medias=$("select.medias option:selected");
			value='';
			if (medias.length>0) {
				$(medias).each(function() {
					src=$(this).attr("value");
					if (value=='') value=src; else value=value+'|'+src;
					src=path+src;
					ext=get_ext(src);
					if (ext=='swf' || ext=='flv') {
						$("div.image_dropdown ul.values").append('<li>'+flash(src,25,25)+'</li>');
					}
					else {
						src=cachedThumb(src);
						$("div.image_dropdown ul.values").append('<li><img class="media" src="'+src+'" /></li>');
					}
				});
				$(this).parent('.form_field').children('input:first').attr('value',value);
			}
		});
	}
		
	// Media selecting

	// Media select by click
	$('div.media ul.choices li').click(function(){
		src=$(this).children('img:first').attr('src');
		$(this).parent('ul').parent('div.media').children('ul.values').empty();
		$(this).parent('ul').parent('div.media').children('ul.values').append('<li><img class="zoom" src="'+src+'" alt="'+src+'" title="'+src+'" /></li>');
		src=pathdecode(src);
		src=src.substr(src.lastIndexOf('/')+1);
		$(this).parent('ul').parent('div.media').children('input:first').attr('value',src);
	});
	$('div.media ul.values†').click(function(){
		$(this).empty();
		$(this).parent('div.media').children('input:first').attr('value','');
	});

	

	// Medias selecting and removing by click
	// function resetMediaValues(thisMedia) {
	// 	values='';
	// 	$(thisMedia).children('ul.values:first li').each(function(){
	// 		src=$(this).children('img:first').attr('src');
	// 		if (src!=undefined) {
	// 			src=src.substr(src.lastIndexOf('/')+1);
	// 			values+='|'+src;
	// 		}
	// 	});
	// 	values=values.substr(1);
	// 	$(thisMedia).children('input:first').attr('value',values);			
	// };
	// function bindMediasClick() {
	// 	$('div.medias ul li').unbind('click');
	// 	$('div.medias ul.values li').unbind('dblclick');
	// 	// medias click 'n drop selecting
	// 	$('div.medias ul.choices li').click(function(){
	// 		thisMedia=$(this).parent('ul').parent('div.medias');
	// 		$(thisMedia).children('ul.values').append($(this).clone());
	// 		resetMediaValues($(thisMedia));
	// 		bindMediasClick();
	// 	});
	// 	// dblclick removing
	// 	$('div.medias ul.values li').dblclick(function(){
	// 		thisMedia=$(this).parent('ul.values').parent('div.medias');
	// 		$(this).remove();
	// 		resetMediaValues(thisMedia);
	// 	});
	// };
	// bindMediasClick();

	// dragndrop ordering of medias
	$('div.medias ul').sortable({
		connectWith: 'div.medias ul',
		update: function(event,ui) {
			if ($(this).hasClass('values')) {
				value='';
				$(this).children('li').each(function(){
					src=$(this).children('img:first').attr('src');
					if (src!=undefined) {
						src=pathdecode(src);
						src=src.substr(src.lastIndexOf('/')+1);
						value+='|'+src;
					}
				});
				value=value.substr(1);
				$(this).parent('.form_field').children('input:first').attr('value',value);
			}
		}
	});


	// Ordered Lists selecting and removing by click
	// function resetListValues(thisList) {
	// 	values='';
	// 	$(thisList).children('li').each(function(){
	// 		id=$(this).attr('id');
	// 		values+='|'+id;
	// 	});
	// 	values=values.substr(1);
	// 	$(thisList).parent('.ordered_list').children('input:first').attr('value',values);		
	// };
	// function bindListClick() {
	// 	$('div.ordered_list ul li').unbind('click');
	// 	// medias click 'n drop selecting
	// 	$('div.ordered_list ul.list_choices li').click(function(){
	// 		thisChoices=$(this).parent('ul.list_choices');
	// 		thisValues=$(thisChoices).parent('div.ordered_list').children('ul.list_values');
	// 		$(thisValues).append($(this).clone());
	// 		resetListValues(thisValues);
	// 		bindListClick();
	// 	});
	// 	$('div.ordered_list ul.list_values li').dblclick(function(){
	// 		thisValues=$(this).parent('ul.list_values');
	// 		$(this).remove();
	// 		resetListValues(thisValues);
	// 	});
	// };
	// bindListClick();


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