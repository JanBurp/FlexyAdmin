
flexyAdmin.config(['flexyConstants','$translateProvider', function (constants,$translateProvider) {

  // Default lang is navigator language
  var language = window.navigator.language;
  
  $translateProvider.fallbackLanguage( 'en' );
  $translateProvider.preferredLanguage( language );
  $translateProvider.useSanitizeValueStrategy('sanitize');
  
  /**
   * Laad bestaande languages uit de constants
   */
  angular.forEach( constants.languages, function(language,key) {
    $translateProvider.translations( key, language );
  });

  
}]);
