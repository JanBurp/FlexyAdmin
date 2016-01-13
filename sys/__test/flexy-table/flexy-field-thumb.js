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

flexyAdmin.directive('flexyFieldThumb', ['flexySettingsService',function(settings) {
  'use strict';
  
  return {
    restrict:     'E',
    templateUrl:  'flexy-table/flexy-field-thumb.html',
    replace:      true,
    scope:        {},
    
    /**
     * DIRECTIVE LINK
     */
    link: function(scope, element, attrs ) {
      var assets = settings.item('site_assets');
      scope.path      = attrs.path;
      scope.files     = attrs.file.split('|');
      scope.thumbs    = [];
      
      for (var i = 0; i < scope.files.length; i++) {
        if (scope.files[i]!=='') {
          var src = scope.path+'/'+scope.files[i];
          var ext = src.suffix('.');
          scope.thumbs[i] = {
            thumb: assets+'_thumbcache/'+src.decodePath(),
            src:   assets+src,
            ext:   ext,
            type:  settings.get_file_type(ext),
            alt:   scope.files[i]
          };
        }
      }
    },
    
  };
  
}]);
