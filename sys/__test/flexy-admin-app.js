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
 * Globale flexyAdmin angular module
 * 
 * @module: flexyAdmin
 * @global
 */
var flexyAdmin = angular.module( 'flexyAdmin', [
  // Angular JS
  'ngRoute',
  
  // Angular Modules
  'http-auth-interceptor',
  'angular-toArrayFilter',
  'angular-loading-bar',
  'ui.bootstrap',
  'ui.bootstrap.showErrors',
  // Grid
  'smart-table',
  'ui.sortable',
  // Form
  'schemaForm',
  'schemaForm-tinymce',
  
  // flexyAdmin Modules
  'flexyMenu',
  'flexyBlocks',
  ]
);


/**
 * SOME SPARE GLOBALS as a constant
 */

flexyAdmin.constant('flexyAdminGlobals',{
  base_url      : 'admin/__test',
  api_base_url  : '__api/',
  sys_folder    : 'sys/__test/',
  log_prefix    : 'FA ',


  /**
   * Form fields
   */
  form_field_types : {
    
    // DEFAULT TYPE
    '[default]' : {
      'data-type'   : 'string',
      'format'      : 'string',
      'type'        : 'string',
      'readonly'    : false,
    },
    
    // SPECIAL FIELDS
    '[id]' : {
      'readonly'    : true,
      'type'        : 'hidden',
    },
    '[order]' : {
      'readonly'    : true,
      'type'        : 'hidden',
    },
    '[self_parent]' : {
      'readonly'    : true,
    },
    '[uri]' : {
      'readonly'    : true,
      'type'        : 'hidden',
    },
    
    // TYPES (determined by prefix)
    'email' : {
      'type' : 'email',
    },
    'txt' : {
      'format' : 'html',
      'type'   : 'wysiwyg',
    },
    'stx' : {
      'type' : 'textarea',
    },
    
  },
  
});



/**
 * ROUTING
 */

flexyAdmin.config( function($routeProvider){
  $routeProvider
    .when('/home',{
      controller  : '',
      templateUrl : 'flexy-ui/flexy-home.html',
    })
    .when('/logout',{
      controller  : 'flexyLogoutController',
      template    : '',
    })
    .when('/help/:page',{
      controller  : '',
      templateUrl : 'flexy-help/help.html',
    })
    .when('/grid/:table',{
      controller  : '',
      templateUrl : 'flexy-grid/flexy-grid.html'
    })
    .when('/form/:table/:id',{
      controller  : '',
      templateUrl : 'flexy-form/flexy-form.html'
    })
    .when('/plugin/:plugin',{
      controller  : '',
      templateUrl : 'flexy-plugin/plugin.html',
    })
    .otherwise({
      redirectTo: '/home'
    });
});



/**
 * Handles state of the app:
 * - if angular is ready show the app
 * - checks login status and responds to the auth events, see: https://github.com/witoldsz/angular-http-auth
 */
flexyAdmin.controller('stateController', ['$scope','flexyAuthService', function($scope,flexyAuthService) {
  'use strict';

  // state variables
  $scope.isLoggedIn = false;
  
  // check auth, wich will transmit the proper events
  flexyAuthService.check().then(
    function(success) {
      $scope.isLoggedIn = false;
      if (flexyAuthService.loggedIn()) {
        $scope.isLoggedIn = true;
      }
    },
    function(error) {}
  );
  
  // EVENT RESPONSE
  $scope.$on('event:auth-loginRequired', function() {
    $scope.isLoggedIn = false;
    angular.element(document.querySelector('#login')).removeClass('hidden');
  });
  $scope.$on('event:auth-loginConfirmed', function() {
    $scope.isLoggedIn = true;
    angular.element(document.querySelector('#login')).addClass('hidden');
  });
  
  // Angular is ready, so show all
  angular.element(document.querySelector('body')).removeClass('hidden');
}]);



flexyAdmin.controller('WizardController', ['flexyAdminGlobals','$scope', function($flexyAdminGlobals,$scope) {
  'use strict';

  /**
   * WIZARD PARAMS
   */
  var self=this;
  $scope.uris = {
    
    edit_user : $flexyAdminGlobals.base_url+'#/form/cfg_users/current'
    
  };
  
}]);
