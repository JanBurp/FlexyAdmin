/**
 * Service voor het plaatsen van berichten in de user interface
 */

flexyAdmin.factory( 'flexyAlertService', ['$rootScope','$timeout', function($rootScope,$timeout) {
  'use strict';
  var flexyAlertService = {};

  // Array van alerts die globaal beschikbaar zijn
  $rootScope.alerts = [];

  /**
   * Voeg alert toe
   */
  flexyAlertService.add = function(type, msg) {
    var timeout = 0;
    $rootScope.alerts.push({
      type: type,
      msg: msg,
      close: function() {
        return flexyAlertService.closeAlert(this);
      }
    });
    if (type==='success') {
      timeout = 2000;
    }
    if (type==='warning' || type=='danger') {
      timeout = 10000;
    }
    if (timeout>0) {
      $timeout(function(){ 
        flexyAlertService.closeAlert(this); 
      }, timeout); 
    }
  };

  /**
   * Verwijder alert
   */
  flexyAlertService.closeAlert = function(index) {
    $rootScope.alerts.splice(index, 1);
  };

  return flexyAlertService;
}]);