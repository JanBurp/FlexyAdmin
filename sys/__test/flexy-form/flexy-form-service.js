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


flexyAdmin.factory('flexyFormService', ['flexySettingsService','flexyApiService','$q', function(settings,api,$q) {
  'use strict';

  var self = this;
  
  /**
   * Default args
   */
  var default_args = {
    table : '',
    id    : false, 
  };
  
  /**
   * flexyFormService
   */
  var flexy_form_service = {};
  
  
  /**
   * Laad de data met de api
   * 
   * @param string table De gevraagde tabel
   * @param object params Eventuele extra parameters die aan de API meegegeven worden (offset, limit)
   * @return promise met als response data[table]
   */
  flexy_form_service.load = function( table, id ) {
    // args
    var params = {
      table      : table,
      where      : id,
      schemaform : true,
    };
    var args = angular.extend({}, default_args, params );

    // API call
    return api.row( args ).then(function(response){

      // Ui names van de velden in het schema zetten
      angular.forEach( response.schemaform.schema.properties, function(value, key) {
        response.schemaform.schema.properties[key].title = settings.item( 'settings','table', table,'field_info',key,'ui_name');
      });

      // Geef data terug in de promise
      return $q.resolve( response );
    });
  };
  
  
  
  // /**
  //  * Zet form data om in schemaform
  //  */
  // flexy_form_service.to_schemaform = function( table, data, form_ui_name ) {
  //
  //   /**
  //    * Default schema
  //    */
  //   var schema = {
  //     schema : {
  //       type: "object",
  //       properties: {},
  //     },
  //     form : ['*'],
  //     model: {},
  //   };
  //
  //
  //   /**
  //    * Tabs
  //    */
  //   var tabs = {};
  //   if (angular.isUndefined(form_ui_name)) {
  //     form_ui_name = settings.item('settings','table', table, 'table_info', 'ui_name' );
  //   }
  //   tabs[form_ui_name] = [];
  //
  //   /**
  //    * Loop each field
  //    */
  //   angular.forEach( data, function(value, key) {
  //
  //     // Default field type
  //     var field = angular.copy( settings.item('form_schema_properties','[default]') );
  //     // Field type by prefix
  //     var prefix = key.prefix();
  //     if ( settings.has_item('form_schema_properties',prefix)) {
  //       field = angular.extend( field, settings.item('form_schema_properties',prefix) );
  //     }
  //     // Field type by fieldname
  //     var fieldname='['+key+']';
  //     if ( settings.has_item('form_schema_properties',fieldname) ) {
  //       field = angular.extend( field, settings.item('form_schema_properties',fieldname) );
  //     }
  //
  //     // Name, Value etc.
  //     field.default   = value;
  //     field.title     = settings.item( ['settings','table',table, 'field_info', key, 'ui_name'] );
  //     if (angular.isUndefined(field.title)) field.title = '';
  //
  //     // -> schema
  //     schema.schema.properties[key] = {
  //       'title'     : field.title,
  //       'type'      : field.type,
  //       'data-type' : field['data-type'],
  //       'default'   : field['default']
  //     };
  //     schema.model[key] = value;
  //
  //     // Tabs & items in tabs
  //     field.tab = form_ui_name; // default tab
  //     // if ( angular.isDefined( data.field_info[key].info.str_fieldset ) ) {
  //     //   if (data.field_info[key].info.str_fieldset!=="") {
  //     //     field.tab = data.field_info[key].info.str_fieldset;
  //     //   }
  //     // }
  //     if ( angular.isUndefined( tabs[field.tab] )) tabs[field.tab]=[]; // new tab
  //
  //     // Add this field to its tab
  //     tabs[field.tab].push({
  //       'key'       : key,
  //       'type'      : field.type,
  //       'readonly'  : field.readonly,
  //     });
  //
  //   });
  //
  //   // -> create Tabs in form
  //   var form_tabs = {
  //     type: "tabs",
  //     tabs: [],
  //   };
  //   angular.forEach( tabs, function(tab_items, tab_title) {
  //     form_tabs.tabs.push({
  //       title: tab_title,
  //       items: tab_items
  //     });
  //   });
  //
  //   // $scope.form.push(form_tabs);
  //   return schema;
  // };
  
  
  return flexy_form_service;
}]);