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
 */

flexyAdmin.directive('flexyFileField', function() {
  return {
    restrict  : 'E',
    template  :'<div class="btn btn btn-primary action-new"><span class="glyphicon glyphicon-folder-open"></span>&nbsp;&nbsp;<span translate="UPLOADER_BROWSE"></span><input class="hidden" type="file" nv-file-select="" uploader="uploader" multiple  /></div>',
    replace   :true,
    
    link: function (scope, element, attrs, ngModel) {
      var input = element.find('input');
      input.bind('change', function(event){
        scope.$evalAsync(function () {
          if(attrs.preview) {
            var reader = new FileReader();
            reader.onload = function (e) {
              scope.$evalAsync(function() {
                scope[attrs.preview]=e.target.result;
              });
            };
            reader.readAsDataURL(event.target.files[0]);
          }
        });
      }).bind('click',function(e) {
        e.stopPropagation();
      });
      element.bind('click',function(e){
        e.preventDefault();
        input[0].click();
      });        
    },
  };
});