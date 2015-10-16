
flexyAdmin.config(['$translateProvider', function ($translateProvider) {

  /**
   * ENGLISH
   */
  $translateProvider.translations('en', {

    'ITEMS_ON'      : 'items on',
    'PAGES'         : 'pages',

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

    'ITEMS_ON'      : 'rijen op',
    'PAGES'         : "pagina's",

    'DIALOGS_SURE'    : 'Zeker weten?',
    'DIALOGS_DELETE'  : 'Verwijder',
    'DIALOGS_DELETED' : 'verwijderd',
    'DIALOGS_YES'     : 'Ok',
    'DIALOGS_NO'      : 'Annuleer',

  });

 
  $translateProvider.preferredLanguage('nl');
  $translateProvider.useSanitizeValueStrategy('sanitize');
  
}]);
