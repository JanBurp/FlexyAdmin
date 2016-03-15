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
   * Complementeer schemaform in de response
   * 
   * @param object response
   * @parem string table
   * @return object response met compleet schemaform
   */
  flexy_form_service.complete_schemaform_in_response = function( response, table ) {

    angular.forEach( response.schemaform.schema.properties, function(value, key) {

      // Ui names van de velden in het schema zetten
      response.schemaform.schema.properties[key].title = settings.item( 'settings','table', table,'field_info',key,'ui_name');
      
      // Select opties in het schema zetten
      if (value['form-type']==='select') {
        var options = settings.item( 'settings','table',table,'field_info', key, 'options' );
        if (angular.isDefined(options)) {
          // De waarden in het schema
          response.schemaform.schema.properties[key].enum = jdb.arrayKeys( options.data, 'value' );
          // De visuele weergave in form, zoek eerst de juiste form entree (tab, index)
          var formIndex = false;
          if (response.schemaform.form.length>1) {
            // Geen tabs/fieldsets
            formIndex = jdb.indexOfProperty( response.schemaform.form, 'key', key);
            response.schemaform.form[ formIndex ].titleMap = options.data;
          }
          else {
            // Wel in een tab/fieldsey
            var tabs = response.schemaform.form[0].tabs;
            var tab = -1;
            while ( !formIndex && tab<tabs.length-1) {
              tab++;
              formIndex = jdb.indexOfProperty( tabs[tab].items, 'key', key);
            }
            response.schemaform.form[0].tabs[tab].items[ formIndex ].titleMap = options.data;
          }
            
        }
      }
    });
    return response;
  };
  
  
  /**
   * Laad de data, checkt eerst of de data al bestaat, zoja geeft dat terug, anders aan api call
   * 
   * @param string table De gevraagde tabel
   * @param object params Eventuele extra parameters die aan de API meegegeven worden (offset, limit)
   * @return promise met als response data[table]
   */
  flexy_form_service.load = function( table, id ) {
    
    // Checkt of de data al bestaat, zo ja geef die dan meteen terug
    // Geeft wat problemen:
    // - grid set geeft niet alle velden
    // 
    // var response = tableService.get_table_data( table );
    // if ( angular.isDefined(response) && angular.isDefined( response.schemaform )) {
    //   var row = tableService.row( table, id );
    //   // Als alle data er al is, maak de response alsof het een row api call was
    //   if ( angular.isDefined(row) ) {
    //     // Als het een nieuw item is (row is leeg), pak dan de defaults
    //     if ( angular.isUndefined(row.id) ) {
    //       row = tableService.row_defaults( table );
    //     }
    //     response.data = row;
    //
    //     // Maak Schemaform compleet
    //     response = flexy_form_service.complete_schemaform_in_response( response, table );
    //     // Geef data terug in de promise
    //     return $q.resolve( response );
    //   }
    // }
    
    // args
    var params = {
      table      : table,
      where      : id,
      schemaform : true,
    };
    var args = angular.extend({}, default_args, params );
    

    // API call
    return api.row( args ).then(function(response){
      // Maak Schemaform compleet
      response = flexy_form_service.complete_schemaform_in_response( response, table );
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