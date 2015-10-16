
flexyAdmin.config(['$translateProvider', function ($translateProvider) {

  /**
   * ENGLISH
   */
  $translateProvider.translations('en', {

    'ITEMS_ON_PAGE' : "{{rows}} items on {{pages}} pages",

    'DIALOGS_SURE'    : 'Are you sure?',
    'DIALOGS_DELETE'  : 'Delete',
    'DIALOGS_DELETED' : 'deleted',
    'DIALOGS_YES'     : 'Ok',
    'DIALOGS_NO'      : 'Cancel',
    
  });

  /**
   * NEDERLANDS
   */
  $translateProvider.translations('nl', {

    'ITEMS_ON_PAGE'   : "{{rows}} rijen in {{pages}} pagina's",

    'DIALOGS_SURE'    : 'Zeker weten?',
    'DIALOGS_DELETE'  : 'Verwijder',
    'DIALOGS_DELETED' : 'verwijderd',
    'DIALOGS_YES'     : 'Ok',
    'DIALOGS_NO'      : 'Annuleer',

  });

 
  $translateProvider.preferredLanguage('nl');
  $translateProvider.useSanitizeValueStrategy('sanitize');
  
}]);
