tinymce.PluginManager.add('flexy_test', function(editor, url) {

  editor.addButton('flexy_test', {
    icon: 'image',
    title: 'Kies een afbeelding', // TODO: lang
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
      }
      console.log(selectedImage);
      
      // Open window with a specific url
      editor.windowManager.open({
        title: 'Kies een afbeelding', // TODO: lang
        url: '_admin/editor',
        id : 'flexy_grid',
        width: width,
        height: height,
        buttons: [
        {
          text: 'Annuleer', // TODO: lang
          onclick: 'close',
        },
        {
          text: 'Selecteer', // TODO: lang
          onclick: function(e) {
            var iframe = document.querySelector('iframe[src="_admin/editor"]');
            var innerDoc = iframe.contentDocument || iframe.contentWindow.document;
            
            var selectedImg = innerDoc.querySelector('tbody tr.is-selected img:first-child');
            if ( selectedImg!==null ) {
              console.log(selectedImg);
              var src = selectedImg.getAttribute("src").replace('/thumb/','/');
              var alt = selectedImg.getAttribute("alt");
              var img = '<img src="'+src+'" alt="'+alt+'" />';
              editor.insertContent(img);
            }
            
            editor.windowManager.close();
          }
        }
      ]
      }, {
        selected: selectedImage,
      });
    }
  });
});
