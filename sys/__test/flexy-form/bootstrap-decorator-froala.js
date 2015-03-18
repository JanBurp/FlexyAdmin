// angular.module("schemaForm").run(["$templateCache", function($templateCache) {$templateCache.put("directives/decorators/bootstrap/froala/froala.html","<div>FROALA</div>");}]);

angular.module('schemaForm-froala', ['schemaForm', 'froala'])
.config(['schemaFormProvider', 'schemaFormDecoratorsProvider', 'sfPathProvider', function(schemaFormProvider,  schemaFormDecoratorsProvider, sfPathProvider) {

  var wysiwyg = function(name, schema, options) {
    if (schema.type === 'string' && schema.format == 'html') {
      
      var f = schemaFormProvider.stdFormObj(name, schema, options);
      f.key  = options.path;
      f.type = 'wysiwyg';
      options.lookup[sfPathProvider.stringify(options.path)] = f;
      return f;
    }
  };
  schemaFormProvider.defaults.string.unshift(wysiwyg);

  //Add to the bootstrap directive
  schemaFormDecoratorsProvider.addMapping('bootstrapDecorator', 'wysiwyg', 'directives/decorators/bootstrap/froala/froala.html');
  schemaFormDecoratorsProvider.createDirective('wysiwyg', 'directives/decorators/bootstrap/froala/froala.html');
}]);
