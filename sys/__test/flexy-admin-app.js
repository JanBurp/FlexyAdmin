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
      controller  : 'flexyHelpController',
      template    : '',
    })
    .when('/grid/:table',{
      controller  : '',
      templateUrl : 'flexy-grid/flexy-grid.html'
    })
    .when('/form/:table/:id',{
      controller  : '',
      templateUrl : 'flexy-form/flexy-form.html'
    })
    .otherwise({
      redirectTo: '/home'
    });
});





/**
 * Taken from demo of: https://github.com/witoldsz/angular-http-auth
 * 
 * Hide/shows login-form if needed
 * Show app if angular is ready
 */
flexyAdmin.directive('flexyAuthenticate', ['flexyAuthService',function(flexyAuthService) {
  'use strict';

  var login = angular.element(document.querySelector('#login'));
  var container  = angular.element(document.querySelector('#container'));

  function hide_all() {
    login.addClass('hidden');
    container.addClass('hidden');
  }
  function show_login() {
    login.removeClass('hidden');
    container.addClass('hidden');
  }
  function hide_login() {
    login.addClass('hidden');
    container.removeClass('hidden');
  }
  
  return {
    restrict: 'C',
    link: function(scope, elem, attrs) {
      hide_all();
      flexyAuthService.check().then(
        function(success) {
          if (flexyAuthService.loggedIn()) {
            hide_login();
          }
          else {
            show_login();
          }
        },
        function(error) {
          show_login();
        }
      );
      
      // EVENT RESPONSE
      scope.$on('event:auth-loginRequired', function() {
        show_login();
      });
      scope.$on('event:auth-loginConfirmed', function() {
        hide_login();
      });

      // READY LOADING ANGULAR
      elem.removeClass('hidden');
    }
  };
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
