angular.module('schemaForm-media', ['schemaForm'])
.config(['schemaFormProvider', 'schemaFormDecoratorsProvider', 'sfPathProvider', function(schemaFormProvider,  schemaFormDecoratorsProvider, sfPathProvider) {

  //Add to the bootstrap directive
  schemaFormDecoratorsProvider.addMapping('bootstrapDecorator', 'media', 'directives/decorators/bootstrap/media.html');
  schemaFormDecoratorsProvider.createDirective('media', 'directives/decorators/bootstrap/media.html');
}]);
