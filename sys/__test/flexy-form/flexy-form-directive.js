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


flexyAdmin.directive('flexyForm', ['flexySettingsService','flexyApiService','flexyFormService','$routeParams', function(settings,api,flexyForm,$routeParams) {
  'use strict';
  
  return {
    restrict:     'E',
    templateUrl:  'flexy-form/flexy-form.html',
    replace:      true,
    scope:        {},
    
    /**
     * DIRECTIVE LINK
     */
    link: function($scope, element, attrs) {
      
      /**
       * The table and item
       */
      $scope.table    = attrs.table;
      $scope.id       = attrs.id;
      
      $scope.base_url = settings.item('base_url');

      /**
       * UI Name
       */
      $scope.ui_name = '';

      /**
       * SCHEMA
       */
      $scope.schema = {};
      $scope.form = ["*"];
      $scope.model = {};
      
      /**
       * LOAD REQUESTED DATA
       */
      flexyForm.load( $scope.table, $scope.id ).then(function(response) {

        // table ui_name
        $scope.ui_name = settings.item( 'settings','table',$scope.table,'table_info','ui_name' );

        $scope.schema = response.schemaform.schema;
        $scope.form   = response.schemaform.form;
        $scope.model  = response.data;
      });
      
      /**
       * LINK METHODS
       */
      
      
      /**
       * RESET FORM
       */
      // $scope.resetForm = function(form) {
      //   // reset each field
      //   angular.forEach( $scope.form_data.fields, function(value, key) {
      //     $scope.model[key]=value;
      //   });
      //   // pristine form
      //   form.$setPristine();
      // };


      /**
       * SAVE FORM
       */
      $scope.saveForm = function(form,back_to_table) {
        // First we broadcast an event so all fields validate themselves
        $scope.$broadcast('schemaFormValidate');
        // Then we check if the form is valid
        if (form.$valid) {
          flexyForm.save( $scope.model, $scope.table, $scope.id ).then(function(response){
            if (angular.isDefined(back_to_table) && back_to_table===true) {
              window.history.back();
            }
          });
        }
      };
  
      /**
       * SUBMIT FORM
       */
      $scope.submitForm = function(form) {
        $scope.saveForm(form,true);
      };
      
      
    },
    
  };
}]);