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


'use strict';

flexyAdmin.controller('FormController', ['flexyAdminGlobals','$scope','$routeParams','$http', function($flexyAdminGlobals, $scope,$routeParams,$http) {

  /**
   * GLOBAL FORM PARAMS
   */
  var self=this;
  $scope.table  = $routeParams.table;
  $scope.id     = $routeParams.id;

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
   * LOAD FROM SERVER
   */
  var callServer = function(tableState) {
    
    $http.post('get_form',{'table':$scope.table,'where':$scope.id}).success(function(result){

      // keep items in Scope
      $scope.form_data=result.data;

      // Create Schema properties
      $scope.schema.properties = {};
      angular.forEach( $scope.form_data.fields, function(value, key) {

        // Default field
        var field = angular.copy( $flexyAdminGlobals.form_field_types.default );
        // Field type according to prefix
        var prefix = key.prefix();
        if (angular.isDefined($flexyAdminGlobals.form_field_types[prefix])) {
          field = angular.extend( field, $flexyAdminGlobals.form_field_types[prefix] );
        }
        // Name, Value etc.
        field.title       = $scope.form_data.field_info[key].ui_name;
        field.default     = value;
        // -> schema
        $scope.schema.properties[key] = {
          type    : field['data-type'],
          title   : field['title'],
          default : field['default']
        };
        // -> form
        $scope.form.push({
          'key' : key,
          'type': field['type']
        });
      });
      
      // Buttons
      $scope.form.push({
        type: "submit",
        title: "Invoeren",
      });
      
    }).error(function(data){
      $log.log('AJAX error -> Form');
    });
  };
  
  callServer();
  
  
  $scope.onSubmit = function(form) {
    // First we broadcast an event so all fields validate themselves
    $scope.$broadcast('schemaFormValidate');
    // Then we check if the form is valid
    if (form.$valid) {
      alert('yes!');
    }
  }
  
}]);