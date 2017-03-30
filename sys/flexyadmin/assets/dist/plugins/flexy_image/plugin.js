tinymce.PluginManager.add('flexy_image', function(editor, url) {

  editor.addButton('flexy_image', {
    icon: 'image',
    title: 'Insert/edit image',
    onclick: function() {

      // Omvang popup
      var margin = 200;
      var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
      var height = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
      width -= margin;
      height -= margin;
      
      // Geselecteerde afbeelding?
      var selected = tinymce.activeEditor.selection.getContent();
      var selectedImage = false;
      if (selected!=='') {
        var matches = selected.match(/<img.*?src="([^"]*?)"/);
        selectedImage = matches[1];
        selectedImage = selectedImage.substr( selectedImage.lastIndexOf('/')+1 );
      }
      var url='_admin/editor/image?selected='+selectedImage;
      
      // Open window with a specific url
      editor.windowManager.open({
        title: 'Insert/edit image',
        url: url,
        id : 'flexy_grid',
        width: width,
        height: height,
        buttons: [
        {
          text: 'Ok',
          subtype: 'primary',
          onclick: function(e) {
            var iframe = document.querySelector('iframe[src="'+url+'"]');
            var innerDoc = iframe.contentDocument || iframe.contentWindow.document;
            
            var selectedImg = innerDoc.querySelector('tbody tr.is-selected img:first-child');
            if ( selectedImg!==null ) {
              // console.log(selectedImg);
              var src = selectedImg.getAttribute("src").replace('/thumb/','/');
              var alt = selectedImg.getAttribute("alt");
              var img = '<img src="'+src+'" alt="'+alt+'" />';
              editor.insertContent(img);
            }
            
            editor.windowManager.close();
          }
        },
        {
          text   : 'Cancel',
          onclick: 'close',
        },
      ]
      }, {
        selected: selectedImage,
      });
    }
  });
});
