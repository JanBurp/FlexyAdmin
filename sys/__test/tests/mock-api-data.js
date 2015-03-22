/**
 * FlexyAdmin (c) Jan den Besten
 * www.flexyadmin.com
 * 
 * 
 * Just a basic service for mock data used for mocking the api service
 * 
 * 
 * @author: Jan den Besten
 * @copyright: Jan den Besten
 * @license: n/a
 * 
 * $Author$
 * $Date$
 * $Revision$
 */

flexyAdmin.factory( 'flexyApiMock', ['flexySettingsService','$http',function(settings,$http) {
  'use strict';
  
  var base = settings.item('admin/__test') + '/tests/data/';
  
  $http.get( base + 'tbl_site.json' ).then(function(response){
    console.log('MOCK DATA',response);
  });
  
  
  
  
  return [
    
  {
    type:     'GET',
    api:      'table',
    params:   {table:'tbl_links'},
    respond:  {
      'success' : true,
      'data' : [
        { 'id':0, 'str_title': 'Burp', 'url_link': 'http://www.burp.nl' },
        { 'id':1, 'str_title': 'Flexy', 'url_link': 'http://www.flexyadmin.com' },
      ],
      'args' : {
        'table' : 'tbl_links'
      },
    } 
  },

  {
    type:     'GET',
    api:      'table',
    params:   {table:'tbl_site'},
    respond:  {
      'success' : true,
      'data' : [
        { 'id':0, 'str_title': 'SITE', 'url_link': 'http://www.burp.nl' },
      ],
      'args' : {
        'table' : 'tbl_site'
      },
    }
  },
  
  
  ];
}]);
