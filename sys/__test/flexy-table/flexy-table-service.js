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

/*jshint -W069 */

flexyAdmin.factory('flexyTableService', ['flexySettingsService','flexyApiService','$q','$translate','dialogs','flexyAlertService', function(settings,api,$q,$translate,dialogs,alertService) {
  'use strict';
  
  var self = this;
  
  /**
   * Default args
   */
  var default_args = {
    table  : '',
    limit  : 20,
    offset : false,   // met false ipv 0 werkt jump_to_today
    as_grid : true,
    // txt_abstract : true
  };
  
  /**
   * Hier wordt de data, per tabel, bewaard, bijvoorbeeld:
   * 
   * [
   *  'tbl_menu : {
   *     tableState : {},    // Last tableState
   *     args       : {},    // argumenten waarmee de table is opgevraagd bij de server
   *     info       : {},    // info (oa pagination data)
   *     data       : {}     // data klaar voor het grid
   *  }
   * ...
   * ]
   */
  var data = [];
  
  
  /**
   * Bereken pagination en voeg die data toe aan info 
   */
  function calculate_pagination(info,args) {
    if ( angular.isUndefined(info.num_pages) ) info.num_pages = Number(args.num_pages);
    if ( angular.isUndefined(info.limit) )     info.limit = Number(args.limit);
    if ( angular.isDefined(info.total_rows) && angular.isDefined(info.limit) && info.limit>0 ) {
      info.num_pages = Math.ceil(info.total_rows / info.limit);
    }
    if ( angular.isUndefined(info.offset) ) info.offset = Number(args.offset);
    return info;
  }
  
  
  
  /**
   * flexyTableService
   */
  var flexy_table_service = {};


  /**
   * Geeft data van gevraagde tabel, als die beschikbaar is
   * 
   * @param string table De gevraagde tabel
   * @return mixed FALSE als de data niet beschikbaar is, anders een object met de data.
   */
  flexy_table_service.get_table_data = function(table,type) {
    if ( angular.isDefined(data[table]) ) {
      if ( angular.isDefined(type) ) {
        if ( angular.isDefined(data[table][type]) ) {
          return data[table][type];
        }
      }
      else {
        return data[table];
      }
    }
    return undefined;
  };
  
  
 /**
  * Maakt van ruwe data array een grid array met:
  * - `order` wordt ingesteld op de volgorde van de meegegeven array
  * - `_info` wordt ingesteld.
  * Als de data aan tree zijn (en `self_parent` bestaat), dan wordt per item `_info` zo ingesteld {level:(int),is_child:(bool),has_children:(bool)}.
  * Daarvoor wordt uitgegaan van de volgorde van de meegegeven array en het veld `self_parent` per item.
  */
 flexy_table_service.add_tree_info = function(data,is_tree) {
   var parents = {};
   
   // Loop all items and add info
   var level=0;
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
   });
   
   // Add more tree info (has_children)
   if (is_tree && parents!={}) {
     angular.forEach(parents,function(level,id){
       var key = jdb.indexOfProperty(data,'id',id);
       if (key!==false) data[key]._info.has_children = true;
     });
   }
   // flexy_table_service.log_grid_items(data);

   return data;
 };
 
 
 /**
  * Logging for grid items
  */
 flexy_table_service.log_grid_items = function(items, message) {
   if (message) console.log(message);
   angular.forEach( items, function(item,id) {
     console.log({'id':item.id,'order':item.order,'parent':item.self_parent,'lev':item._info.level,'has':item._info.has_children},item.uri);
   });
 };
 
  
  /**
   * Geeft informatie over de tabel. De volgende keys zitten erin:
   * 
   * - num_rows     - Het aantal records dat beschikbaar is
   * - num_pages    - Het totaal aantal pagina's dat beschiklaar is
   * - total_rows   - Het totaal aantal records dat in het resultaat bestaat voor deze tabel
   * - limit        - Het aantal records per pagina
   * - offset       - Start record
   * 
   * @param string table De gevraagde tabel
   * @return mixed FALSE als de data/info niet beschikbaar is, anders een object met de data.
   */
  flexy_table_service.get_info = function(table) {
    if (angular.isUndefined( data[table]) || angular.isUndefined( data[table].info )) return false;
    return data[table].info;
  };
  
  
  /**
   * Controleert of de gevraagde data al bestaat
   * 
   * @param string table De gevraagde tabel
   * @param object [args] Eventuele args van de tabel
   * @return bool
   */
  flexy_table_service.tabledata_is_present = function( table, args ) {
    if ( angular.isUndefined( data[table] ) ) {
      return false;
    }
    if ( angular.isDefined(args) && !angular.equals( data[table].args, args )) {
      return false;
    }
    return true;
  };
  
  
  /**
   * Laad de data (eventueel van de server)
   * 
   * @param string table De gevraagde tabel
   * @param object params Eventuele extra parameters die aan de API meegegeven worden (offset, limit)
   * @return promise met als response data[table]
   */
  flexy_table_service.load = function( table, path, tableState ) {
    /**
     * Maak args klaar aan de hand van gegeven tableState
     * - pagination
     * - sorting
     * - filter
     */
    var offset = 0;
    if ( angular.isDefined( tableState ) && angular.isDefined( tableState.pagination.start ) ) offset = tableState.pagination.start;
    var params = {
      offset  : offset,
      limit   : settings.item(['screen','pagination'])
    };
    
    if ( angular.isDefined( tableState ) && angular.isDefined( tableState.sort.predicate ) ) {
      params.sort = tableState.sort.predicate;
      if (tableState.sort.reverse===true) params.sort = '_' + params.sort;
    }
    if ( angular.isDefined( tableState ) && angular.isDefined( tableState.search.predicateObject ) ) {
      params.filter = tableState.search.predicateObject.$;
    }
    // args
    var args = angular.extend({}, default_args, params, {'table':table });
    if ( angular.isDefined(path) ) args = angular.extend({}, args, {'path':path });
    

    /**
     * Als er al data van deze table bestaat, dan is een API call niet nodig, geef de promise met de data terug
     */
    if ( flexy_table_service.tabledata_is_present( table, args ) ) {
      return $q.resolve(data[table]);
    }
    
    // API call
    return api.table( args ).then(function(response){
      
      // console.log('flexy_table_service.load response',response);
      
      
      // Reset data als eerste pagina
      if ( tableState.pagination.start===0) data[table] = {
        tableState : {},
        args       : {},
        info       : {},
        data       : {}
      };
      
      // add info to loaded data
      var newData = flexy_table_service.add_tree_info( response.data, settings.item('settings','table', args.table, 'table_info', 'tree' ) );
      
      // Bewaar data (met juiste args)
      var saved_args = args;
      delete(saved_args.settings);
      data[table] = {
        tableState : tableState,
        args       : saved_args,
        info       : calculate_pagination( response.info, args),
        data       : newData
      };
      
      // Geef data terug in de promise
      return $q.resolve(data[table]);
    });
  };
  

  /**
   * Verwijder data uit table
   * @param string table tabel
   */
  flexy_table_service.remove = function(table) {
    delete(data[table]);
  };
  
  
  /**
   * DELETE (selected) ITEM(s), maar eerst CONFIRM
   * 
   * @param string table De gevraagde tabel
   * @param array selected De te verwijderen item id's
   * @return promise met als response TRUE/FALSE
   */
  flexy_table_service.delete = function( table, selected ) {
    // Alleen verder als er minimaal een item moet worden verwijderd
    if (selected.length<=0) return $q.resolve(false);
    // prepare confirm dialog
    return $translate(['DIALOGS_SURE','DIALOGS_DELETE_ITEM','DIALOGS_DELETED','DIALOGS_DELETE_SELECTED','DIALOGS_DELETED_SELECTED','DIALOGS_DELETE_ERROR'],{num:selected.length}).then(function (translations) {
      var title   = translations.DIALOGS_SURE;
      var abstract = '';
      var message = '<b>';
      if (selected.length>1) {
        message+= translations.DIALOGS_DELETE_SELECTED + '</b>';
      }
      else {
        abstract = flexy_table_service.get_abstract( table, selected[0] );
        message+= translations.DIALOGS_DELETE_ITEM + '</b><br>' + abstract;
      }
      // Confirm dialog
      return dialogs.confirm( title, message, {'size':'sm'} ).result.then(function(btn){
        // OK: dus delete
        api.delete( { 'table':table, 'where':selected }).then( function(response) {
          if (response.success===true && response.data!==false) {
            // Message
            if (selected.length>1) {
              alertService.add( 'success', selected.length+' <b>'+translations.DIALOGS_DELETED_SELECTED+'</b>');
            }
            else {
              alertService.add( 'success', abstract + ' <b>'+translations.DIALOGS_DELETED+'</b>');
            }
            return $q.resolve(true);
          }
          else {
            // FOUT
            alertService.add( 'warning', ' <b>'+translations.DIALOGS_DELETE_ERROR+'</b>');
            return $q.resolve(false);
          }
        },
        function(response){
          // FOUT
          alertService.add( 'warning', ' <b>'+translations.DIALOGS_DELETE_ERROR+'</b>');
          return $q.resolve(false);
        });
      },function(btn){
        // CANCEL
        return $q.resolve(false);
      });
    });
  };
  
  
  /**
   * Geeft de aangepaste volgorde van de meegegeven items door aan de server
   * 
   * @param string table De gevraagde tabel
   * @param array items De items in de juiste volgorde
   * @return promise met als response een array met per row de id en de nieuwe order
   */
  flexy_table_service.change_order = function( table, items, from ) {
    return $translate(['DIALOGS_ORDER_SUCCESS','DIALOGS_ORDER_ERROR']).then(function (translations) {
      var args = { 'table':table, 'id': [], 'from':0 };
      if (angular.isDefined(from)) args.from = from;
      angular.forEach( items, function(item,key) {
        args.id.push( item.id );
      });
      // API
      return api.table_order( args ).then(function(response){
        var expected = true;
        var new_data = response.data;
        // Check of data hetzelfde is
        angular.forEach( items, function(item,key) {
          if ( Number(item.order)!==Number(jdb.assocArrayItem(new_data,'id',item.id).order) ) {
            expected = false;
          }
        });
        // Melding
        if (expected) {
          data[table].data=items;
          alertService.add( 'success', '<b>'+translations.DIALOGS_ORDER_SUCCESS+'</b>');
        }
        else {
          alertService.add( 'danger', '<b>'+translations.DIALOGS_ORDER_ERROR+'</b>');
        }
        return $q.resolve(data[table]);
      });
    });
  };
  
  /**
   * Pas van één item de volgorde aan (rekening houdend met kinderen en tussenliggende items)
   * 
   * @param string table De gevraagde tabel
   * @param object Het item dat verplaatst moet worden
   * @param int new_order de nieuwe volgorde
   * @param int direction de richting (-1 of 1)
   * @return promise met als response TRUE/FALSE
   */
  flexy_table_service.set_order = function(table, item, new_order, direction) {
    return $translate(['DIALOGS_ORDER_PAGE_UP','DIALOGS_ORDER_PAGE_DOWN','DIALOGS_ORDER_ERROR']).then(function (translations) {
      var args = { 'table':table, 'id': item.id, 'from': new_order };
      // console.log('flexy_table_service.set_order args:',args);
      // API
      return api.table_order( args ).then(function(response){
        // console.log('flexy_table_service.set_order response:',response);
        var new_data = response.data;
        // Check of data hetzelfde is
        var expected = (new_order==new_data);
        // Melding
        if (expected) {
          if (direction==-1)
            alertService.add( 'success', '<b>'+translations.DIALOGS_ORDER_PAGE_DOWN+'</b>');
          else
            alertService.add( 'success', '<b>'+translations.DIALOGS_ORDER_PAGE_UP+'</b>');
        }
        else {
          alertService.add( 'danger', '<b>'+translations.DIALOGS_ORDER_ERROR+'</b>');
        }
        return $q.resolve(expected);
      });
    });
  };
  
  
  
  /**
   * Past een rij aan in de data van een tabel (gebruikt als een form een update geeft)
   * TODO: Op dit moment wordt hele data gereset zodat data opnieuw van server wordt opgevraagd
   */
  flexy_table_service.update_row = function( table, id, new_row ) {
    flexy_table_service.remove(table);
  };
  
  
  /**
   * Geeft een rij uit een table
   */
  flexy_table_service.row = function( table,id ) {
    if ( angular.isDefined(data) && angular.isDefined(data[table]) && angular.isDefined(data[table].data) ) {
      return jdb.assocArrayItem( data[table].data,'id',id );
    }
    return undefined;
  };
  
  
  /**
   * Geef een abstract van een rij uit een table
   */
  flexy_table_service.get_abstract = function( table, id ) {
    var abstract = table+'.'+id;
    var abstract_fields = settings.item( ['settings','table', table, 'abstract_fields'] );
    if (angular.isDefined(abstract_fields)) {
      var row = flexy_table_service.row( table,id );
      if (angular.isDefined(row)) {
        abstract = '';
        angular.forEach( abstract_fields, function(abstract_field,key) {
          abstract+=row[abstract_field]+' | ';
        });
        abstract = abstract.substr(0, abstract.length - 3);
      }
    }
    return abstract;
  };
  
  
  
  return flexy_table_service;
}]);
