/**
 * FlexyAdmin (c) Jan den Besten
 * www.flexyadmin.com
 * 
 * @author: Jan den Besten
 * @copyright: Jan den Besten
 * @license: n/a
 * 
 * $Author$
 * $Date$
 * $Revision$
 * $HeadURL$ 
 */


/**
 * flexy-blocks
 * 
 * Maakt van elk karakter in een tekst binnen een element een span met die letter
 */

var flexyBlocks = angular.module( 'flexyBlocks', []);

flexyBlocks.directive("flexyBlocks", function() {
  'use strict';
  
  var blocks = function(scope, element, attributes) {
    element.addClass('flexy-blocks');
    var text = element.text();
    var html = '';
    angular.forEach(text,function(value,key){
      var charClass=value;
      var btnStyle="btn-default";
      if (value==' ') {
        value="&nbsp;";
        charClass='space';
        btnStyle='btn-default';
      }
      html += '<span class="flexy-block btn '+btnStyle+' char_'+charClass+'">'+value+'</span>';
    });
    element.html(html);
  };
  return {
    restrict: "A",
    link: blocks
  };
});
