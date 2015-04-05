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
   * Default args
   */
  var default_args = {
    table  : '',
    limit  : 0,
    offset : 0,
    txt_as_abstract : true
  };
  
  /**
   * Hier wordt de data, per tabel, bewaard, bijvoorbeeld:
   * 
   * [
   *  'tbl_menu : {
   *     args       : {},    // parameters waarmee de data zijn verkregen (bijvoorbeeld where)
   *     raw        : {},    // ruwe data
   *     info       : {},    // info (oa pagination data)
   *     grid       : {}     // data klaar voor het grid
   *  }
   * ...
   * ]
   */
  var data = [];
  
  
  /**
   * Bereken pagination en voeg die data toe aan info 
   */
  function calculate_pagination(info,args) {
    info.total_pages = 1;
    if (angular.isDefined(info.total_rows) && angular.isDefined(args.limit) && args.limit>0) {
      info.total_pages = Math.ceil(info.total_rows / args.limit) ;
    }
    return info;
  }
  
  /**
   * Process the raw data so its ready for the grid: set tree info
   */
  function create_grid_data(data,args) {
    // Process _info
    var is_tree = settings.item('config','table_info', args.table, 'tree' );
    var parents = {};

    // Loop all items and add info
    angular.forEach( data, function(item,id) {
      // Make sure _info is set
      if ( angular.isUndefined(data[id]._info)) data[id]._info = {};
      
      // TREE info if needed
      if (is_tree) {
        var parent_id = item.self_parent;
        var level=0;
        var has_children = false;
        var is_child = false;
        // if not on toplevel:
        if (parent_id>0) {
          is_child=true;
          // are we on a known level?
          if ( angular.isDefined( parents[parent_id]) ) {
            // yes: get that level
            level=parents[parent_id];
          }
          else {
            // no: remember new level
            level++;
            parents[parent_id]=level;
          }
        }
        // add this info to this item
        data[id]._info.level         = level;
        data[id]._info.is_child      = is_child;
        data[id]._info.has_children  = false; // this will be set later...
      }
    });
    
    // Add more tree info (has_children)
    if (is_tree && parents!={}) {
      angular.forEach(parents,function(value,key){
        data[key]._info.has_children = true;
      });
    }
    
    // console.log(parents);
    // angular.forEach( data, function(item,id) {
    //   console.log(item.id,item.self_parent,item.order,item._info,item.uri);
    //
    // });
    
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
    if (angular.isUndefined(params)) params = {};
    params.table = table;
    var args = angular.extend({}, default_args, params);

    // API
    api.table( args ).then(function(response){
      // Reset data
      data[table] = {};
      // Put (new) data
      data[table] = {
        args  : args,
        raw   : response.data,
        info  : calculate_pagination(response.info,args),
        grid  : create_grid_data(response.data,args)
      };
    });
    
  };
  
  
  return flexy_grid_service;
}]);
