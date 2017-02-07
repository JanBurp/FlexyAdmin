/**

FlexyAdmin test plugin 'flexytest'

 */

/*global tinymce:true */

tinymce.PluginManager.add('flexytest', function(editor, url) {

  // Add a button that opens a window
  editor.addButton('flexytest', {
    text: 'FlexyTest',
    icon: 'music',
    onclick: function() {
      // Open window
      editor.windowManager.open({
        title: 'FlexyTest plugin',
        body: [
          {
            type: 'textbox',
            name: 'title',
            label: 'Title'
          }
        ],
        onsubmit: function(e) {
          // Insert content when the window form is submitted
          editor.insertContent('FlexyTest: ' + e.data.title);
        }
      });
    }
  });

  // Adds a menu item to the tools menu
  editor.addMenuItem('flexytest', {
    text: 'FlexyTest',
    context: 'tools',
    onclick: function() {
      // Open window with a specific url
      editor.windowManager.open({
        title: 'TinyMCE site',
        url: 'http://www.tinymce.com',
        width: 800,
        height: 600,
        buttons: [{
          text: 'Close',
          onclick: 'close'
        }]
      });
    }
  });
});
