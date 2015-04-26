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
    info.limit = args.limit;
    if (angular.isDefined(info.total_rows) && angular.isDefined(args.limit) && args.limit>0) {
      info.total_pages = Math.ceil(info.total_rows / args.limit) ;
    }
    return info;
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
  * Maakt van ruwe data array een grid array met:
  * - `order` wordt ingesteld op de volgorde van de meegegeven array
  * - `_info` wordt ingesteld.
  * Als de data aan tree zijn (en `self_parent` bestaat), dan wordt per item `_info` zo ingesteld {level:(int),is_child:(bool),has_children:(bool)}.
  * Daarvoor wordt uitgegaan van de volgorde van de meegegeven array en het veld `self_parent` per item.
  */
 flexy_grid_service.add_tree_info = function(data,is_tree) {
   // Process _info
   var parents = {};

   // Loop all items and add info
   var level=0;
   var order=0;
   angular.forEach( data, function(item,key) {
     // Make sure _info is set
     data[key]._info = {};
     
     // TREE info if needed
     if (is_tree) {
       var parent_key = item.self_parent;
       var has_children = false;
       var is_child = false;
       // if not on toplevel:
       if (parent_key>0) {
         is_child=true;
         // are we on a known level?
         if ( angular.isDefined( parents[parent_key]) ) {
           // yes: get that level
           level=parents[parent_key];
         }
         else {
           // no: remember new level
           level++;
           parents[parent_key]=level;
         }
       }
       else {
         // on root, so level = 0
         level=0;
       }
       // add this info to this item
       data[key]._info.level         = level;
       data[key]._info.is_child      = is_child;
       data[key]._info.has_children  = false; // this will be set later...
     }
     
     // Reset order
     data[key].order = order;
     order++;
   });
   
   // Add more tree info (has_children)
   if (is_tree && parents!={}) {
     // console.log('PARENTS',parents);
     angular.forEach(parents,function(level,id){
       var key = jdb.indexOfProperty(data,'id',id);
       data[key]._info.has_children = true;
     });
   }
   // flexy_grid_service.show_grid_items(data);

   return data;
 };
 
 /**
  * Logging for grid items
  */
 flexy_grid_service.show_grid_items = function(items, message) {
   if (message) console.log(message);
   angular.forEach( items, function(item,id) {
     console.log({'id':item.id,'order':item.order,'parent':item.self_parent,'lev':item._info.level,'has':item._info.has_children},item.uri);
   });
 };
  
  /**
   * Geeft informatie over de tabel. De volgende keys zitten erin:
   * 
   * - rows         - Het aantal records dat beschikbaar is
   * - limit        - Het aantal records per pagina
   * - total_pages  - Het totaal aantal pagina's dat beschiklaar is
   * - total_rows   - Het totaal aantal records dat in het resultaat bestaat voor deze tabel
   * - table_rows   - Het totaal aantal records dat in de tabel bestaat
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
    var args = angular.extend({}, default_args, params, {'table':table});

    // API
    return api.table( args ).then(function(response){
      // Reset data
      data[table] = {};
      // Put (new) data
      data[table] = {
        args  : args,
        raw   : response.data,
        info  : calculate_pagination(response.info,args),
        grid  : flexy_grid_service.add_tree_info(response.data, settings.item('config','table_info', args.table, 'tree' ) )
      };
      return response;
    });
  };
  
  return flexy_grid_service;
}]);
