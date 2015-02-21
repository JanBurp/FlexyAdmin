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


flexyAdmin.controller('FormController', ['flexyAdminGlobals','$scope','$routeParams','$http', function($flexyAdminGlobals, $scope,$routeParams,$http) {
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
    
    $http.post('get_form',{'table':$scope.table,'where':$scope.id,'config':['table_info','field_info']}).success(function(result){
      
      // keep items in Scope
      $scope.form_data.fields=result.data.fields;
      $scope.form_data.table_info=result.config.table_info;
      $scope.form_data.field_info=result.config.field_info;

      // Create Schema properties
      $scope.schema.properties = {};
      // Start tabs
      var tabs = {};
      tabs[$scope.form_data.table_info.ui_name]=[];
      
      angular.forEach( $scope.form_data.fields, function(value, key) {

        // Default field
        var field = angular.copy( $flexyAdminGlobals.form_field_types['[default]'] );
        // Fieldname
        var fieldname='['+key+']';
        if (angular.isDefined($flexyAdminGlobals.form_field_types[fieldname])) {
          field = angular.extend( field, $flexyAdminGlobals.form_field_types[fieldname] );
        }
        // Field type according to prefix
        var prefix = key.prefix();
        if (angular.isDefined($flexyAdminGlobals.form_field_types[prefix])) {
          field = angular.extend( field, $flexyAdminGlobals.form_field_types[prefix] );
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
        field.tab = $scope.form_data.table_info.ui_name; // default tab
        if ( angular.isDefined( $scope.form_data.field_info[key].info.str_fieldset) ) {
          if ($scope.form_data.field_info[key].info.str_fieldset!=="") {
            field.tab = $scope.form_data.field_info[key].info.str_fieldset;
          }
        }
        if ( angular.isUndefined( tabs[field.tab] )) tabs[field.tab]=[]; // new tab
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
    }).error(function(data){
      $log.log('AJAX error -> Form');
    });
  };
  
  callServer();
  
}]);