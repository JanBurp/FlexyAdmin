tinymce.PluginManager.add('flexy_image', function(editor, url) {

  editor.addButton('flexy_image', {
    icon: 'image',
    title: 'Insert/edit image',
    onclick: function() {

      // Omvang popup
      var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
      var height = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
      if (width>1180) width=1180;
      if (height>720) height=720;
      height -= 100;

      // Path
      var path = 'pictures'; 

      // Geselecteerde afbeelding?
      var selected = tinymce.activeEditor.selection.getContent();
      var selectedImage = false;
      if (selected!=='') {
        var matches = selected.match(/<img.*?src="([^"]*?)" alt="([^"]*?)"/);
        if (matches) {
          var src = matches[1].split('/');
          var srcL = src.length;
          selectedImage = {
            'path': src[srcL-2],
            'src' : src[srcL-1],
            'alt' : matches[2],
          };
        }
      }
      var url = encodeURI('_admin/load/editor/image?path='+path+'&selected='+JSON.stringify(selectedImage));
      
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
            var altInput = innerDoc.querySelector('#alt');

            if ( selectedImg!==null ) {
              var src = selectedImg.getAttribute("data-src");
              var alt = altInput.value;
              if (alt=='') alt = src;

              var img = '<img src="_media/'+path+'/'+src+'" alt="'+alt+'" />';
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
