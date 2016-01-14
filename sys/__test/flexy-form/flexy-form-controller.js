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


flexyAdmin.controller('FormController', ['flexySettingsService','flexyApiService','$scope','$routeParams', function(settings,api,$scope,$routeParams) {
  'use strict';

  /**
   * GLOBAL FORM PARAMS
   */
  var self=this;
  $scope.table          = $routeParams.table;
  $scope.id             = $routeParams.id;
  $scope.original_data  = {};

  /**
   * FORM DATA
   */
  $scope.form_data = {
    'table_info'      : {},
    'field_info'      : {},
    'fields'          : [],
  };  
  
  /**
   * SCHEMA
   */
  $scope.schema = {
    type: "object",
    properties: {}
  };
  $scope.form = [];
  $scope.model = {};
  
  /**
   * RESET FORM
   */
  $scope.resetForm = function(form) {
    // reset each field
    angular.forEach( $scope.form_data.fields, function(value, key) {
      $scope.model[key]=value;
    });
    // pristine form
    form.$setPristine();
  };
  
  /**
   * SUBMIT FORM
   */
  $scope.submitForm = function(form) {
    // First we broadcast an event so all fields validate themselves
    $scope.$broadcast('schemaFormValidate');
    // Then we check if the form is valid
    if (form.$valid) {
      alert('yes!');
    }
  };
  
  
  /**
   * LOAD FROM SERVER
   */
  var callServer = function(tableState) {
    
    api.row( {table:$scope.table, 'where': $scope.id} ).then(function(response) {
      
      // keep items in Scope
      $scope.form_data.fields = response.data;

      // Create Schema properties
      $scope.schema.properties = {};
      // Start tabs
      var tabs = {};
      var table_ui_name = settings.item('settings','table', $scope.table, 'table_info', 'ui_name' );
      tabs[table_ui_name] = [];
      
      angular.forEach( $scope.form_data.fields, function(value, key) {

        // Default field
        var field = angular.copy( settings.item('form_field_types','[default]') );
        // Fieldname
        var fieldname='['+key+']';
        if ( settings.has_item('form_field_types',fieldname) ) {
          field = angular.extend( field, settings.item('form_field_types',fieldname) );
        }
        // Field type according to prefix
        var prefix = key.prefix();
        if ( settings.has_item('form_field_types',prefix)) {
          field = angular.extend( field, settings.item('form_field_types',prefix) );
        }
        // Name, Value etc.
        if (angular.isDefined($scope.form_data.field_info[key])) field.title = $scope.form_data.field_info[key].ui_name;
        field.default     = value;
        // -> schema
        $scope.schema.properties[key] = {
          type    : field['data-type'],
          title   : field.title,
          default : field['default']
        };
        
        // Tabs & items in tabs
        field.tab = table_ui_name; // default tab
        // if ( angular.isDefined( $scope.form_data.field_info[key].info.str_fieldset ) ) {
        //   if ($scope.form_data.field_info[key].info.str_fieldset!=="") {
        //     field.tab = $scope.form_data.field_info[key].info.str_fieldset;
        //   }
        // }
        if ( angular.isUndefined( tabs[field.tab] )) tabs[field.tab]=[]; // new tab
        
        // Add this field to its tab
        tabs[field.tab].push({
          'key'       : key,
          'type'      : field.type,
          'readonly'  : field.readonly,
        });

      });
      
      // -> create Tabs in form
      var form_tabs = {
        type: "tabs",
        tabs: [],
      };
      angular.forEach( tabs, function(tab_items, tab_title) {
        form_tabs.tabs.push({
          title: tab_title,
          items: tab_items
        });
      });
      $scope.form.push(form_tabs);
      
    });

  };
  
  callServer();
  
}]);