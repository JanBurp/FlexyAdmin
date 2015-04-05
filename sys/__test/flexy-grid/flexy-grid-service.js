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


flexyAdmin.factory('flexyGridService', ['flexySettingsService','flexyApiService', function(settings,api) {
  'use strict';
  
  /**
   * Hier wordt de data, per tabel, bewaard, bijvoorbeeld:
   * 
   * [
   *  'tbl_menu : {
   *     args: {},    // parameters waarmee de data zijn verkregen (bijvoorbeeld where)
   *     raw : {},    // ruwe data
   *     info: {},    // info
   *     grid: {}     // data klaar voor het grid
   *  }
   * ...
   * ]
   */
  var data = [];
  
  
  /**
   * Process the raw data so its ready for the grid
   */
  function create_grid_data(data) {
    // TODO
    return data;
  }
  
  
  
  /**
   * flexyGridService API
   */
  var flexy_grid_service = {};


  /**
   * Geeft ruwe data. Zoals de API die heeft gegeven.
   * 
   * @param string table De gevraagde tabel
   * @return mixed FALSE als de data niet beschikbaar is, anders een object met de data.
   */
  flexy_grid_service.get_raw_data = function(table) {
    if (angular.isUndefined( data[table]) || angular.isUndefined( data[table].raw )) return undefined;
    return data[table].raw;
  };
  
  /**
   * Geeft data die klaar is voor gebruik in de Grid directive.
   * 
   * @param string table De gevraagde tabel
   * @return mixed FALSE als de data niet beschikbaar is, anders een object met de data.
   */
  flexy_grid_service.get_grid_data = function(table) {
    if (angular.isUndefined( data[table]) || angular.isUndefined( data[table].grid )) return undefined;
    return data[table].grid;
  };
  
  /**
   * Geeft informatie over de tabel. De volgende keys zitten erin:
   * 
   * - rows       - Het aantal records dat beschikbaar is
   * - total_rows - Het totaal aantal records dat in het resultaat bestaat voor deze tabel
   * - table_rows - Het totaal aantal records dat in de tabel bestaat
   * 
   * @param string table De gevraagde tabel
   * @return mixed FALSE als de data/info niet beschikbaar is, anders een object met de data.
   */
  flexy_grid_service.get_info = function(table) {
    if (angular.isUndefined( data[table]) || angular.isUndefined( data[table].info )) return false;
    return data[table].info;
  };

  
  /**
   * Laad de data van de server.
   * 
   * @param string table De gevraagde tabel
   * @param object params Eventuele extra parameters die aan de API meegegeven worden (offset, limit)
   * @return promise
   */
  flexy_grid_service.load = function(table,params) {
    var args = {
      table           : table,
      txt_as_abstract : true
    };
    args = angular.extend({}, args, params);
    var config=['table_info','field_info'];
    api.table( args, config ).then(function(response){
      // Reset data
      data[table] = {};
      // Put (new) data
      data[table] = {
        args  : args,
        raw   : response.data,
        info  : response.info,
        grid  : create_grid_data(response.data)
      };
    });
  };
  
  
  return flexy_grid_service;
}]);