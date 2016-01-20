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
  // api_base_url  : 'http://ntozl.nl/test/flexyadmin/3.x-beta/_api/',
  

  /**
   * Folder from root
   */
  sys_folder    : 'sys/__test/',
  
  /**
   * Assets folder
   */
  site_assets   : 'site/assets/',


  /**
   * A prefix that will be added to all logging messages
   */
  log_prefix    : 'FA ',
  
  
  /**
   * LANGUAGES
   */
  languages : {
    en : {
      'ERROR'                     : 'Error',
      'HOME_TITLE'                : 'What to do:',
      'HOME_ADD'                  : 'Add something',
      'HOME_EDIT'                 : 'Edit something',
      'HOME_DELETE'               : 'Delete something',
      'HOME_STATISTICS'           : 'Show statistics',
      'HOME_USER'                 : 'Edit my settings',
      'HOME_HELP'                 : 'Look a short tutorial',
      'EMAIL'                     : 'Email',
      'LOGIN_TITLE'               : 'Log in',
      'LOGIN_USERNAME'            : 'Username',
      'LOGIN_PASSWORD'            : 'Password',
      'LOGIN_FORGOT'              : 'Forgot password?',
      'LOGIN_SEND_PASSWORD'       : 'Send new password',
      'LOGIN_SUBMIT'              : 'Login',
      'ITEMS_ON_PAGE'             : "{{rows}} items on {{pages}} pages",
      'JUMP_TO_TODAY'             : 'Today',
      'DIALOGS_SURE'              : 'Are you sure?',
      'DIALOGS_DELETE_ITEM'       : 'Delete this item?',
      'DIALOGS_DELETED'           : 'Has been deleted.',
      'DIALOGS_DELETE_ERROR'      : 'AN ERROR HAS OCURRED WHILE DELETING.',
      'DIALOGS_ORDER_SUCCESS'     : 'Order has changed successfull',
      'DIALOGS_ORDER_PAGE_DOWN'   : 'Item is moved to the end of the previous page, which is showed now.',
      'DIALOGS_ORDER_PAGE_UP'     : 'Item is moved to the beginning of the next page, which is showed now.',
      'DIALOGS_ORDER_ERROR'       : 'ERROR: Order could not be changed on the server.<br>Reload the page and try again.',
      'DIALOGS_YES'               : 'Ok',
      'DIALOGS_NO'                : 'Cancel',
      'FORM_SUBMIT'               : 'Submit',
      'FORM_SAVE'                 : 'Save',
      'FORM_RESET'                : 'Reset',
      'FORM_CANCEL'               : 'Cancel',
      'FORM_SAVED'                : 'Data is saved',
      'FORM_SAVE_ERROR'           : 'ERROR, DATA IS NOT SAVED.',
      
    },
    nl : {
      'ERROR'                     : 'Fout',
      'HOME_TITLE'                : 'Wat wil je doen?',
      'HOME_ADD'                  : 'Iets toevoegen',
      'HOME_EDIT'                 : 'Iets aanpassen',
      'HOME_DELETE'               : 'Iets verwijderen',
      'HOME_STATISTICS'           : 'Statistieken bekijken',
      'HOME_USER'                 : 'Mijn gegevens aanpassen',
      'HOME_HELP'                 : 'Een korte uitleg bekijken',
      'EMAIL'                     : 'E-mail',
      'LOGIN_TITLE'               : 'Log in',
      'LOGIN_USERNAME'            : 'Gebruikersnaam',
      'LOGIN_PASSWORD'            : 'Wachtwoord',
      'LOGIN_FORGOT'              : 'Wachtwoord vergeten?',
      'LOGIN_SEND_PASSWORD'       : 'Stuur nieuw wachtwoord',
      'LOGIN_SUBMIT'              : 'Inloggen',
      'ITEMS_ON_PAGE'             : "{{rows}} rijen in {{pages}} pagina's",
      'JUMP_TO_TODAY'             : 'Vandaag',
      'DIALOGS_SURE'              : 'Zeker weten?',
      'DIALOGS_DELETE_ITEM'       : 'Verwijder deze gegevens?',
      'DIALOGS_DELETED'           : 'Is verwijderd.',
      'DIALOGS_DELETE_SELECTED'   : 'Verwijder {{num}} geselecteerde rijen?',
      'DIALOGS_DELETED_SELECTED'  : 'items verwijderd.',
      'DIALOGS_DELETE_ERROR'      : 'ER IS EEN FOUT OPGETREDEN TIJDENS VERWIJDEREN.',
      'DIALOGS_ORDER_SUCCESS'     : 'De volgorde is aangepast.',
      'DIALOGS_ORDER_PAGE_DOWN'   : 'Het item is verplaatst naar het eind van de vorige pagina, die nu wordt getoond.',
      'DIALOGS_ORDER_PAGE_UP'     : 'Het item is verplaatst naar het begin van de volgende pagina, die nu wordt getoond.',
      'DIALOGS_ORDER_ERROR'       : 'FOUT: De volgorde kon niet worden aangepast op de server.<br>Herlaad de pagina en probeer het nog eens.',
      'DIALOGS_YES'               : 'Ok',
      'DIALOGS_NO'                : 'Annuleer',
      'FORM_SUBMIT'               : 'Invoeren',
      'FORM_SAVE'                 : 'Bewaar',
      'FORM_RESET'                : 'Reset',
      'FORM_CANCEL'               : 'Annuleer',
      'FORM_SAVED'                : 'Gegevens zijn bewaard',
      'FORM_SAVE_ERROR'           : 'BEWAREN VAN GEGEVENS IS MISLUKT.',
    },
  },
  
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
    site_assets   : constants.site_assets,
    log_prefix    : constants.log_prefix,

    /**
     * Use mock data (for testing)
     */
    use_mock      : true,

    /**
     * Settings die van de api komen
     */
    settings : {
      // 'table' : [
      //   'tbl_menu' : []
      // ],
      // 'path'  : [
      //   'pictures' : []
      // ]
    },
    
    screen : {
      width       : window.innerWidth,
      height      : window.innerHeight,
      pagination  : (window.innerHeight<400)?5 : Math.ceil((window.innerHeight - 400) / 35 / 5 ) * 5 , // height - header&footer / row | in steps of 5 en minimaal 5
    },
    
    file_types : {
      'file-image' : ['jpg','gif','png'],
      'file-pdf'   : ['pdf'],
      'file-excel' : ['xls','xlsx'],
      'file-word'  : ['doc','docx'],
      'file-zip'   : ['zip','rar'],
      // 'file-movie'
      // 'file-archive'
      // 'file-audio'
      // 'file-code'
      // 'file-powerpoint'
      // 'file-text'
      // 'file-video'
    },

    
  };

}]);
