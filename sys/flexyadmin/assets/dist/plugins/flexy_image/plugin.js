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
      if (width>1040) width=1040;
      if (height>640) height=640;

      // Path
      var path = 'pictures'; 

      // Geselecteerde afbeelding?
      var selected = tinymce.activeEditor.selection.getContent();
      var selectedImage = false;
      if (selected!=='') {
        var matches = selected.match(/<img.*?src="([^"]*?)" alt="([^"]*?)"/);
        if (matches) {
          var src = matches[1].split('/');
          src.shift();
          selectedImage = {
            'path': src.shift(),
            'src' : src.join('/'),
            'alt' : matches[2],
          };
          // console.log(selectedImage);
        }
      }
      var url = encodeURI('_admin/editor/image?path='+path+'&selected='+JSON.stringify(selectedImage));
      // console.log(url);
      
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
            var selectedImg = innerDoc.querySelector('#src');
            var alt = innerDoc.querySelector('#alt').getAttribute("value");

            if ( selectedImg!==null ) {
              var src = selectedImg.getAttribute("data-src");
              if (alt=='') alt = src;
              var src = '_media/'+path+'/'+src;

              var img = '<img src="'+src+'" alt="'+alt+'" />';
              console.log(img);
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
