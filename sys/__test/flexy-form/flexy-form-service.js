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


flexyAdmin.factory('flexyFormService', ['flexySettingsService','flexyApiService','flexyTableService','flexyAlertService','$translate','$q', function(settings,api,tableService,alertService,$translate,$q) {
  'use strict';

  var self = this;
  
  /**
   * Default args
   */
  var default_args = {
    table : '',
    id    : false, 
  };
  
  /**
   * flexyFormService
   */
  var flexy_form_service = {};
  
  
  /**
   * Laad de data met de api
   * 
   * @param string table De gevraagde tabel
   * @param object params Eventuele extra parameters die aan de API meegegeven worden (offset, limit)
   * @return promise met als response data[table]
   */
  flexy_form_service.load = function( table, id ) {
    // args
    var params = {
      table      : table,
      where      : id,
      schemaform : true,
    };
    var args = angular.extend({}, default_args, params );

    // API call
    return api.row( args ).then(function(response){

      // Ui names van de velden in het schema zetten
      angular.forEach( response.schemaform.schema.properties, function(value, key) {
        response.schemaform.schema.properties[key].title = settings.item( 'settings','table', table,'field_info',key,'ui_name');
      });

      // Geef data terug in de promise
      return $q.resolve( response );
    });
  };
  

  /**
   * Bewaar data van form op de server
   */
  flexy_form_service.save = function( data, table, id ) {
    return $translate(['FORM_SAVED','FORM_SAVE_ERROR']).then(function (translations) {
      var args = {
        'data':data,
        'table':table,
      };
      // Update or insert?
      if (angular.isDefined(id) && id>=0) {
        args.where = id;
      }
      api.update( args ).then( function(response) {
        if (response.success===true && response.data!==false) {
          id = response.data.id;
          // Bewaar data in table data, zodat geen reload van de hele table nodig is
          tableService.update_row( table,id, data);
          // Message
          alertService.add( 'success', '<b>'+translations.FORM_SAVED+'</b>');
          return $q.resolve(true);
        }
        else {
          // FOUT
          alertService.add( 'warning', '<b>'+translations.FORM_SAVE_ERROR+'</b>');
          return $q.resolve(false);
        }
      });
    });
  };
  
  
  
  return flexy_form_service;
}]);