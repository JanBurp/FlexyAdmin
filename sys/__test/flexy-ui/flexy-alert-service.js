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
  flexyAlertService.add = function(type, msg, timeout) {
    $rootScope.alerts.push({
      type: type,
      msg: msg,
      close: function() {
        return flexyAlertService.closeAlert(this);
      }
    });
    if (timeout) {
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