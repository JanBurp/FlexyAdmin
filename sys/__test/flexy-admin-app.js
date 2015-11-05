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
  'dialogs.main',
  'cfp.loadingBar',
  'pascalprecht.translate',
  
  'ui.bootstrap',
  'ui.bootstrap.showErrors',
  // Grid
  'smart-table',
  'as.sortable',
  
  // Form
  'schemaForm',
  'froala',
  'schemaForm-froala',
  
  'angularFileUpload',
  
  
  // flexyAdmin Modules
  'flexyMenu',
  // 'flexyMedia',
  'flexyBlocks',
  
  ]
);


/**
 * FROALA config
 */
flexyAdmin.value('froalaConfig', {
  inlineMode: false,
  placeholder: 'Enter Text Here'
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
    // Table
    .when('/table/:table',{                      
      controller  : 'TableController',
      templateUrl : 'flexy-table/flexy-table-route.html'
    })
    .when('/media/:path',{
      controller  : 'MediaController',
      templateUrl : 'flexy-media/flexy-media-route.html'
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
flexyAdmin.controller('stateController', ['$scope','flexyAuthService','$location', function($scope,flexyAuthService,$location) {
  'use strict';
  
  // First check if mocking is on
  // TODO!!
  

  // state variables
  $scope.isLoggedIn = false;
  
  // check auth, wich will transmit the proper events and show/hide login dialog
  flexyAuthService.check().then(
    function(success) {
      $scope.isLoggedIn = flexyAuthService.loggedIn();
      // Show login if needed
      if (!$scope.isLoggedIn) loginShow();
    },
    function(error) {}
  );
  
  // EVENT RESPONSE
  $scope.$on('event:auth-loginRequired', function() {
    $scope.isLoggedIn = false;
  });
  $scope.$on('event:auth-loginCancelled', function() {
    $scope.isLoggedIn = false;
    $location.path('/');
  });
  $scope.$on('event:auth-loginConfirmed', function() {
    $scope.isLoggedIn = flexyAuthService.loggedIn();
    $location.path('/'); // prevents hidden data (container will hide)
  });
  
  // Angular is ready, so show all
  angular.element(document.querySelector('body')).removeClass('hidden');
}]);



/**
 * Special forms?
 */
flexyAdmin.controller('WizardController', ['flexySettingsService','$scope', function(settings,$scope) {
  'use strict';

  /**
   * WIZARD PARAMS
   */
  var self=this;
  $scope.uris = {
    
    edit_user : settings.item('base_url')+'#/form/cfg_users/current'
    
  };
  
}]);
