var EmbedDialog = {
	
	preInit : function() {
		var url;
		tinyMCEPopup.requireLangPack();
		if (url = tinyMCEPopup.getParam("external_embed_list_url")) {
			document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
		}
	},
	

	
	init : function() {
		var f = document.forms[0];
		// Get the selected contents as text and place it in the input
		f.embedcode.value = tinyMCEPopup.editor.selection.getContent({format : 'html'});
		
		// get the Embed list and put in as options
		var dom = tinyMCEPopup.dom;
		var lst = dom.get('src_list');
		if (typeof(tinyMCEEmbedList)!='undefined') {
			var l = tinyMCEEmbedList;
			lst.options.length = 0;
			if (l && l.length > 1) {
				lst.options[lst.options.length] = new Option('', '');
				tinymce.each(l, function(o) {
					lst.options[lst.options.length] = new Option(o[0], o[1]);
				});
			}
			else {
				dom.remove(dom.getParent('src_list', 'p'));
			}
		}
		else {
			dom.remove(dom.getParent('src_list', 'p'));
		}
	},



	insert : function() {
		// Insert the contents from the input into the document
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, document.forms[0].embedcode.value);
		tinyMCEPopup.close();
	}
		
};


EmbedDialog.preInit();
tinyMCEPopup.onInit.add(EmbedDialog.init, EmbedDialog);
