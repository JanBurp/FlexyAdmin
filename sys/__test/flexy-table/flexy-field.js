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

flexyAdmin.directive('flexyField', ['flexySettingsService',function(settings) {
  'use strict';
  
  return {
    restrict:     'E',
    templateUrl:  'flexy-table/flexy-field.html',
    replace:      true,
    scope:        {
      table: "@",
      id:    "@",
      field: "@",
      value: "@",
    },
    
    /**
     * DIRECTIVE LINK
     */
    link: function(scope, element, attrs ) {
      
      /**
       * Table/Path & id (not needed yet)
       */
      scope.table = attrs.table;
      scope.path  = attrs.path;
      scope.id    = attrs.id;
      
      /**
       * Value
       */
      scope.value       = attrs.value;
      
      /**
       * Field & type
       */
      scope.field       = attrs.field;
      scope.field_type  = attrs.field.prefix();
      
      // foreign keys
      if (['id'].indexOf(scope.field_type)>=0 && scope.field!=='id') {
        scope.field_type = 'foreign_key';
        var options = settings.item(['settings','table',scope.table,'field_info',scope.field,'options','data']);
        var option = jdb.assocArrayItem( options,'value',scope.value );
        scope.name = option.name;
      }
      
      // booleans (different types)
      if (['b','is','has','bool'].indexOf(scope.field_type)>=0) {
        scope.field_type = 'bool';
      }
      
      // color (add complement color for text)
      if (scope.field_type=='rgb') {
        scope.inverse_color = jdb.colorComplement( scope.value );
      }
      
      // path for media types
      if (['media','medias'].indexOf(scope.field_type)>=0) {
        scope.path = settings.item( ['settings','table', scope.table, 'field_info', scope.field, 'path' ] );
      }
      if (scope.path==='' && ['file'].indexOf(scope.field_type)>=0) {
        scope.field_type='str';
      }
      
    },
    
  };
  
}]);
