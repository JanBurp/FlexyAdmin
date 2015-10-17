
flexyAdmin.config(['$translateProvider', function ($translateProvider) {

  /**
   * ENGLISH
   */
  $translateProvider.translations('en', {

    'ITEMS_ON_PAGE' : "{{rows}} items on {{pages}} pages",

    'DIALOGS_SURE'         : 'Are you sure?',
    'DIALOGS_DELETE_ITEM'  : 'Delete this item?',
    'DIALOGS_DELETED'      : 'Has been deleted.',
    'DIALOGS_DELETE_ERROR' : 'AN ERROR HAS OCURRED WHILE DELETING.',
    
    'DIALOGS_YES'          : 'Ok',
    'DIALOGS_NO'           : 'Cancel',
    
  });

  /**
   * NEDERLANDS
   */
  $translateProvider.translations('nl', {

    'ITEMS_ON_PAGE'   : "{{rows}} rijen in {{pages}} pagina's",

    'DIALOGS_SURE'              : 'Zeker weten?',
    'DIALOGS_DELETE_ITEM'       : 'Verwijder deze gegevens?',
    'DIALOGS_DELETED'           : 'Is verwijderd.',
    'DIALOGS_DELETE_SELECTED'   : 'Verwijder {{num}} geselecteerde rijen?',
    'DIALOGS_DELETED_SELECTED'  : 'items verwijderd.',
    'DIALOGS_DELETE_ERROR'      : 'ER IS EEN FOUT OPGETREDEN TIJDENS VERWIJDEREN.',
    
    'DIALOGS_YES'         : 'Ok',
    'DIALOGS_NO'          : 'Annuleer',

  });

 
  $translateProvider.preferredLanguage('nl');
  $translateProvider.useSanitizeValueStrategy('sanitize');
  
}]);
