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

/*jshint -W069 */

flexyAdmin.directive('flexyFieldThumb', [function() {
  'use strict';
  
  return {
    restrict:     'E',
    templateUrl:  'flexy-grid/flexy-field-thumb.html',
    replace:      true,
    scope:        {},
    
    /**
     * DIRECTIVE LINK
     */
    link: function(scope, element, attrs ) {

      scope.path    = attrs.path;
      scope.files   = attrs.file.split('|');
      scope.thumbs  = [];
      for (var i = 0; i < scope.files.length; i++) {
        scope.thumbs[i] = {src:'',alt:''};
        if (scope.files[i]!=='') {
          var src = 'site/assets/'+scope.path+'/'+scope.files[i];
          scope.thumbs[i] = {
            thumb:'site/assets/_thumbcache/'+src.decodePath(),
            src:src,
            alt:scope.files[i]
          };
        }
      }
      
    },
    
  };
  
}]);
