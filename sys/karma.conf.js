// run karma start


// Karma configuration
// Generated on Tue Oct 07 2014 21:08:34 GMT+0200 (CEST)

module.exports = function(config) {
  config.set({

    // base path that will be used to resolve all patterns (eg. files, exclude)
    basePath: '__test/',

    // frameworks to use
    // available frameworks: https://npmjs.org/browse/keyword/karma-adapter
    frameworks: ['jasmine'],

    // list of files / patterns to load in the browser
    files: [

      // 'js/jquery.min.js',
      // 'js/angular.js',
      // 'js/angular-route.js',
      // 'js/ui-bootstrap-tpls.min.js',
      // 'js/showErrors.min.js',
      // 'js/http-auth-interceptor.js',
      // 'js/toArrayFilter.js',
      // 'js/loading-bar.js',
      // 'js/smart-table.min.js',
      // 'js/ng-sortable.min.js',
      // 'js/angular-file-upload.js',
      // 'js/angular-sanitize.min.js',
      // 'js/tv4.js',
      // 'js/ObjectPath.js',
      // 'js/schema-form.min.js',
      // 'js/bootstrap-decorator.min.js',
      // 'flexy-form/bootstrap-decorator-froala.js',
      // 'js/froala_editor.min.js',
      // 'js/angular-froala.js',
      // 'js/froala-sanitize.js',
      
      'js/externals.min.js',

      'js/angular-mocks.js',

      "jdb*.js",
      "**/jdb*.js",
      "flexy*.js",
      "**/flexy*.js",
      'tests/mock*.js',
    ],

    // list of files to exclude
    exclude: [
      'img/*',
      'css/*',
      'fonts/*',
      '*.png',
    ],

    // preprocess matching files before serving them to the browser
    // available preprocessors: https://npmjs.org/browse/keyword/karma-preprocessor
    preprocessors: {
    },

    // test results reporter to use
    // possible values: 'dots', 'progress'
    // available reporters: https://npmjs.org/browse/keyword/karma-reporter
    reporters: ['progress','notify'],
    // Optional Settings 
    notifyReporter: {
      reportEachFailure: true, // Default: false, Will notify on every failed sepc 
      reportSuccess: true, // Default: true, Will notify when a suite was successful 
    },

    // web server port
    port: 9876,

    // enable / disable colors in the output (reporters and logs)
    colors: true,

    // level of logging
    // possible values: config.LOG_DISABLE || config.LOG_ERROR || config.LOG_WARN || config.LOG_INFO || config.LOG_DEBUG
    logLevel: config.LOG_INFO,

    // enable / disable watching file and executing tests whenever any file changes
    autoWatch: true,

    // Continuous Integration mode
    // if true, Karma captures browsers, runs the tests and exits
    singleRun: false,

    // start these browsers
    // available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
    browsers: ['Chrome'],
    browserNoActivityTimeout : 50000,

  });
};
