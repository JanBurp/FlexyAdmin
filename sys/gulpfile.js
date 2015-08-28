/**
 * 
 * gulpfile.js voor FlexyAdmin - Jan den Besten 2014
 * 
 * - gulp less    // compileer alleen de LESS bestanden tot CSS bestanden
 * - gulp cssmin  // voeg de CSS bestanden samen, autoprefixed browser specifieke css en voegt fallback in px toe waar rem units worden gebruikt (roept eerst 'less' aan)
 * - gulp jshint  // test JS bestanden op veelvoorkomende fouten
 * - gulp jsmin   // combineer en minificeer alle JS bestanden (roept eerst 'jshint' aan)
 * - gulp message // een test om te kijken of gulp werkt en er een notificatie komt
 * - gulp install // zie hierboven
 * 
 * Instellingen:
 * - files     : Als je meer css/less of js bestanden gebruikt dan de standaard installatie, voeg ze dan hier toe (bij je gekozen framework)
 */


/**
 * Om de hoeveel ms gulp watch z'n taken doet (100ms in standaard)
 */
var watch_interval = 100;


/** Paths (keep as is) */
var bower     = 'bower_components';
var assets    = '__test';

var files = {
  // JS
  'js'      : assets+'/js',
  'jshint'  : [
    assets+'/**/jdb*.js',
    assets+'/**/flexy*.js',
  ],
  'jsmin'   : [
    assets+'/js/jquery.min.js',
    assets+'/js/bootstrap.min.js',
    assets+'/js/site.js',
  ],
  'jsdest'  : 'scripts.min.js',
  'watchjs' : [
    assets+'/**/jdb*.js',
    assets+'/**/flexy*.js',
  ],
  
  // LESS / CSS
  'less'    : assets+'/less/flexyadmin.less',
  'css'     : assets+'/css',
  'cssmin'  : [
    assets+'/css/loading-bar.css',
    assets+'/css/font-awesome.min.css',
    assets+'/css/froala_editor.css',
    assets+'/css/froala_style.css',
    assets+'/css/ng-sortable.css',
    assets+'/css/flexyadmin.css',
  ],
  'cssdest' : 'flexyadmin.min.css',
  'watchcss': [
    assets+'/less/*'
  ]
};


/** HIERONDER NIETS AANPASSEN!!! ****************************/


/**
 * Notify options
 */
var title        = '';
var message      = '<%= file.relative %>';
var icon         = 'sys/flexyadmin/assets/img/work.png';
var dir          = __dirname.replace('/Users/','');


/**
 * Load plugins
 */
var gulp        = require('gulp');
var notify      = require("gulp-notify");
var gutil       = require('gulp-util');
var plumber     = require('gulp-plumber');
var livereload  = require('gulp-livereload');
var less        = require('gulp-less');
var sourcemaps  = require('gulp-sourcemaps');
var autoprefixer= require('gulp-autoprefixer');
var pixrem      = require('gulp-pixrem');
var concat      = require('gulp-concat');
var minify_css  = require('gulp-minify-css');
var jshint      = require('gulp-jshint');
var stylish     = require('jshint-stylish');
var uglify      = require('gulp-uglify');
var flatten     = require('gulp-flatten');
var cached      = require('gulp-cached');
var remember    = require('gulp-remember');

var phpunit     = require('gulp-phpunit');

/**
 * Calling 'gulp --build' minify the css (without sourcemaps)
 */
var build = false;
if(gutil.env.build === true) build=true;


/** TASKS ************************************************/


var onError = function (err) {  
  gutil.beep();
  console.log(err);
  this.emit('end');
};


gulp.task('message',function(){
  gulp.src( '' )
  .pipe(notify({
    'title'   : 'WATCH '+title,
    'message' : dir,
    'icon'    : icon
  })); 
});


/**
 * Verplaats door bower geinstalleerde/geupdate frameworks/plugins naar juiste plaats
 */
gulp.task('install', function() {

  // Verplaats bootstrap dist bestanden (css,fonts,js)
  gulp.src([
     bower+'/bootstrap/dist/**/*.min.js',
     bower+'/bootstrap/dist/**/glyphicons*',
    ]).pipe(gulp.dest( assets ));
  gulp.src( bower+'/bootstrap/less/*')
    .pipe(gulp.dest(assets +'/less/bootstrap'));
  gulp.src( bower+'/bootstrap/less/mixins/*' )
    .pipe(gulp.dest(assets +'/less/bootstrap/mixins'));
  gulp.src( '' ).pipe(notify("Bootstrap moved"));
  
  // Verplaats Angular JS etc.
  gulp.src([
    // Angular
    bower+"/angular/angular.js",
    bower+"/angular-route/angular-route.js",
    bower+"/angular-bootstrap/ui-bootstrap-tpls.min.js",
    bower+"/angular-bootstrap-show-errors/src/showErrors.min.js",
    // Mock
    bower+"/angular-mocks/angular-mocks.js",
    // Angular External Modules
    bower+"/angular-http-auth/src/http-auth-interceptor.js",
    bower+"/angular-toArrayFilter/toArrayFilter.js",
    bower+"/angular-loading-bar/src/loading-bar.js",
    // grid
    bower+"/angular-smart-table/dist/smart-table.min.js",
    bower+"/ng-sortable/dist/ng-sortable.min.js",
    // media
    bower+"/angular-file-upload/angular-file-upload.js",
    // form
    bower+"/angular-sanitize/*.min.js",
    bower+"/tv4/tv4.js",
    bower+"/objectpath/lib/ObjectPath.js",
    bower+"/angular-schema-form/dist/*.min.js",
    // jQuery
    bower+"/jquery/dist/jquery.min.js",
    // Froala editor
    bower+"/FroalaWysiwygEditor/js/froala_editor.min.js",
    bower+"/angular-froala/src/angular-froala.js",
    bower+"/angular-froala/src/froala-sanitize.js",
  ]).pipe(gulp.dest( assets+'/js' ));
  gulp.src( '' ).pipe(notify("Angular, Angular modules, jQuery & Froala editore moved"));

  // CSS - Loading bar - Fontawsome - Froala - etc
  gulp.src([
    bower+"/angular-loading-bar/src/loading-bar.css",
    bower+"/ng-sortable/dist/ng-sortable.css",
    bower+"/components-font-awesome/css/font-awesome.min.css",
    bower+"/FroalaWysiwygEditor/css/froala_editor.css",
    bower+"/FroalaWysiwygEditor/css/froala_style.css",
  ]).pipe(gulp.dest( assets+'/css' ));
  gulp.src([
    bower+"/components-font-awesome/fonts/*",
  ]).pipe(gulp.dest( assets+'/fonts' ));
  gulp.src( '' ).pipe(notify("CSS Moved"));
  
  // Minify JS
  gulp.src([
    assets+"/js/jquery.min.js",
    assets+"/js/angular.js",
    assets+"/js/angular-route.js",
    assets+"/js/ui-bootstrap-tpls.min.js",
    assets+"/js/showErrors.min.js",
    assets+"/js/http-auth-interceptor.js",
    assets+"/js/toArrayFilter.js",
    assets+"/js/loading-bar.js",
    assets+"/js/smart-table.min.js",
    assets+"/js/ng-sortable.min.js",
    assets+"/js/angular-file-upload.js",
    assets+"/js/angular-sanitize.min.js",
    assets+"/js/tv4.js",
    assets+"/js/ObjectPath.js",
    assets+"/js/schema-form.min.js",
    assets+"/js/bootstrap-decorator.min.js",
    assets+"/flexy-form/bootstrap-decorator-froala.js",
    assets+"/js/froala_editor.min.js",
    assets+"/js/angular-froala.js",
    assets+"/js/froala-sanitize.js"
  ])
  .pipe(flatten())
  .pipe(sourcemaps.init({loadMaps: true}))
  .pipe(concat( 'externals.min.js' ))
  .pipe(uglify())
  .pipe(sourcemaps.write('maps'))
  .pipe(gulp.dest( assets+'/js') )
  .pipe(notify({
    title:  'Angular & Modules concat & minified ' + title,
    message: message
  }));
  
  

});


/**
 * JS Hint, check js file(s) for bugs and if a bug found: stop.
 */
gulp.task('jshint',function(){
  return gulp.src( files['jshint'] )
    .pipe(plumber({ errorHandler: onError }))
    .pipe(cached('sys_jshint'))
    .pipe(jshint())
    .pipe(remember('sys_jshint'))
    // Use gulp-notify as jshint reporter
    .pipe(notify(function (file) {
      if (file.jshint.success) { return false }
      return file.relative + " (" + file.jshint.results.length + " errors)\n";
    }))
    .pipe(jshint.reporter(stylish))
    .pipe(jshint.reporter('fail'))
    .pipe(notify({
      title:   'JS Hint OK' + title,
      message: message,
      icon: icon 
    }));
});


/**
 * Unglify & combine JS files
 */
gulp.task('jsmin',['jshint'],function(){
  return gulp.src( files['jsmin'] )
    .pipe(flatten())
    .pipe(sourcemaps.init({loadMaps: true}))
    .pipe(cached('sys_jsmin'))
    .pipe(uglify())
    .pipe(remember('sys_jsmin'))
    .pipe(sourcemaps.write('maps'))
    .pipe(concat(files['jsdest']))
    .pipe(gulp.dest( files['js']) )
    .pipe(notify({
      title:  'JS contact & uglify ' + title,
      message: message
    }));
});

/**
 * compile & minify LESS files
 */
gulp.task('less',function(){
  return gulp.src( files['less'] )
        .pipe(plumber({ errorHandler: onError }))
        .pipe(sourcemaps.init())
        .pipe(cached('sys_less'))
        .pipe(less({compress:true}))
        .pipe(remember('sys_less'))
        .pipe(sourcemaps.write('maps'))
        .pipe(gulp.dest( files['css'] ))
        .pipe(notify({
          title:   'LESS' + title,
          message: message
        }));
});
  
/**
 * Concat CSS files
 */
gulp.task('cssmin',['less'],function(){
  return gulp.src( files['cssmin'] )
        .pipe(sourcemaps.init({loadMaps: true}))
        .pipe(autoprefixer())
        .pipe(pixrem())
        .pipe(concat(files['cssdest']))
        .pipe(sourcemaps.write('maps'))
        .pipe(gulp.dest( files['css']) )
        .pipe(notify({
          title:   'CSS concat & minify' + title,
          message: message
        }));
});

gulp.task('default', ['jshint','jsmin','less','cssmin'] );



// Watchers
gulp.task('watch', function() {
  
  // watch for JS changes
  // gulp.watch( files['watchjs'], { interval: watch_interval }, ['jshint','jsmin','message'] );
  
  // Watch for JS changes
  gulp.watch( files['watchjs'], { interval: watch_interval } ).on('change', function(jsfile) {
    return gulp.src( jsfile.path )
      .pipe(plumber({ errorHandler: onError }))
      .pipe(jshint())
      // Use gulp-notify as jshint reporter
      .pipe(notify(function (file) {
        if (file.jshint.success) { return false }
        return file.relative + " (" + file.jshint.results.length + " errors)\n";
      }))
      .pipe(jshint.reporter(stylish))
      .pipe(jshint.reporter('fail'))
      .pipe(notify({
        title:   'JS Hint OK' + title,
        message: message,
        icon: icon 
      }));
  });
 
  // watch for LESS/CSS changes
  gulp.watch( files['watchcss'], { interval: watch_interval }, ['less','cssmin','message'] );
  
  // Watch any resulting file changed
  livereload.listen();
  
  gulp.watch( [
    files['css'] + '/' + files['cssdest'],
    files['js']  + '/' + files['jsdest'],
    files['watchjs'],
  ], { interval: watch_interval } ).on('change', livereload.changed);
  
});


// PHPUNIT
gulp.task('phpunit', function() {
   gulp.src('phpunit.xml')
     .pipe(phpunit('/usr/local/bin/phpunit', {notify:true }))
     .on('error', notify.onError('phpunit FAILED'))
     .pipe(notify({
       title:'phpunit',
       message:'phpunit OK!'
     }));
});

// PHP Watch
gulp.task('watch_php', function() {
  // WATCH PHP
  gulp.watch( [ 'flexyadmin/**/*.php', 'flexyadmin/**/**/*.php'], { interval: watch_interval }, ['phpunit'] );
});

