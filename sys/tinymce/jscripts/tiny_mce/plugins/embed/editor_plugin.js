(function(){tinymce.PluginManager.requireLangPack('embed');tinymce.create('tinymce.plugins.EmbedPlugin',{init:function(ed,url){ed.addCommand('mceEmbed',function(){ed.windowManager.open({file:url+'/dialog.htm',width:305+parseInt(ed.getLang('embed.delta_width',0)),height:250+parseInt(ed.getLang('embed.delta_height',0)),inline:1},{plugin_url:url,})});ed.addButton('embed',{title:'embed.desc',cmd:'mceEmbed',image:url+'/img/embed.gif'});ed.onNodeChange.add(function(ed,cm,n){cm.setActive('embed',n.nodeName=='IMG')})},createControl:function(n,cm){return null},getInfo:function(){return{longname:'Embed plugin Plus',author:'Chili Pepper Design & Jan den Besten',authorurl:'http://www.jandenbesten.net',infourl:'http://www.jandenbesten.net',version:"1.0"}}});tinymce.PluginManager.add('embed',tinymce.plugins.EmbedPlugin)})();