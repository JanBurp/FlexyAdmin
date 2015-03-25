/**
 * FlexyAdmin (c) Jan den Besten
 * www.flexyadmin.com
 * 
 * 
 * Just a basic service for all the config
 * 
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
 * These are constants that are fixed and used all over the app, including the config parts
 */
flexyAdmin.constant('flexyConstants', {

  /**
   * URL of app
   */
  base_url      : 'admin/__test',

  /**
   * URL of api calls
   */
  api_base_url  : '_api/',

  /**
   * Folder from root
   */
  sys_folder    : 'sys/__test/',

  /**
   * A prefix that will be added to all logging messages
   */
  log_prefix    : 'FA ',
  
});



/**
 * All the settings, including the constants
 */
flexyAdmin.factory( 'flexySettings', ['flexyConstants', function(constants) {
  'use strict';
  return {

    /**
     * Add the constants:
     */
    base_url      : constants.base_url,
    api_base_url  : constants.api_base_url,
    sys_folder    : constants.sys_folder,
    log_prefix    : constants.log_prefix,

    /**
     * Use mock data (for testing)
     */
    use_mock      : true,

    /**
     * Config that comes from database, and will grow if needed with the cfg_ methods
     */
    cfg : {
      // table_info : {},
      // field_info : {},
    }, 
  
  
    /**
     * Form fields, for form thema's
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
    
    },  // form_field_types
    
    
  };

}]);
