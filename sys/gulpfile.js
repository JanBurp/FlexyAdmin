/**
 * 
 * gulpfile.js voor FlexyAdmin - Jan den Besten 2016
 * 
 * - gulp compile // compileer de LESS & SASS bestanden tot CSS bestanden
 * - gulp cssmin  // voeg de CSS bestanden samen, autoprefixed browser specifieke css en voegt fallback in px toe waar rem units worden gebruikt (roept eerst 'less' aan)
 * - gulp jshint  // test JS bestanden op veelvoorkomende fouten
 * - gulp jsmin   // combineer en minificeer alle JS bestanden (roept eerst 'jshint' aan)
 * - gulp message // een test om te kijken of gulp werkt en er een notificatie komt
 * - gulp install // zie hierboven
 * 
 * Instellingen:
 * - files     : Als je meer css/less/scss of js bestanden gebruikt dan de standaard installatie, voeg ze dan hier toe (bij je gekozen framework)
 */


/**
 * Om de hoeveel ms gulp watch z'n taken doet (100ms in standaard)
 */
var watch_interval = 100;


/** Paths (keep as is) */
var bower     = 'bower_components';
var assets    = 'flexyadmin/assets';

var files = {
  // JS
  'js'      : assets+'/js',
  'jshint'  : [
    assets+'/js/vue-components/*.js',
    assets+'/js/flexy*.js',
  ],
  'jsmin'   : [
    assets+'/js/vue-components/*.js',
    assets+'/js/flexy*.js',
    assets+'/js/vue.js',
  ],
  'jsdest'  : 'scripts.min.js',
  'watchjs' : [
    assets+'/js/*.js',
  ],
  
  // LESS / CSS / SASS
  'compile'    : [
    // assets+"/less/bootstrap/bootstrap.less",
    // assets+"/less/variables.less",
    // assets+"/less/flexy-main.less",
    // assets+"/less/flexy-table.less",
    assets+'/less/flexyadmin.less',
  ],
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
var gutil       = require('gulp-util');
var notify      = require("gulp-notify");
// var plumber     = require('gulp-plumber');
// var livereload  = require('gulp-livereload');
// var less        = require('gulp-less');
// var sourcemaps  = require('gulp-sourcemaps');
// var autoprefixer= require('gulp-autoprefixer');
// var pixrem      = require('gulp-pixrem');
// var concat      = require('gulp-concat');
// var minify_css  = require('gulp-minify-css');
// var jshint      = require('gulp-jshint');
// var stylish     = require('jshint-stylish');
// var uglify      = require('gulp-uglify');
// var flatten     = require('gulp-flatten');
// var cached      = require('gulp-cached');
// var remember    = require('gulp-remember');


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

  // Bootstrap
  gulp.src([
     bower+'/bootstrap/dist/js/bootstrap.js',
    ]).pipe(gulp.dest( assets + '/js' ));
  gulp.src( bower+'/bootstrap/scss/*')
    .pipe(gulp.dest(assets +'/scss/bootstrap'));
  gulp.src( '' ).pipe(notify("Bootstrap Installed"));

  // Fontawsome
  gulp.src([
    bower+"/components-font-awesome/css/font-awesome.min.css",
  ]).pipe(gulp.dest( assets+'/css' ));
  gulp.src([
    bower+"/components-font-awesome/fonts/*",
  ]).pipe(gulp.dest( assets+'/fonts' ));
  gulp.src( '' ).pipe(notify("Fontawsome Installed"));
  
  // Vue
  gulp.src([
     bower+'/vue/dist/vue.js',
    ]).pipe(gulp.dest( assets + '/js' ));
  gulp.src( '' ).pipe(notify("Vue Installed"));
  

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
        // .pipe(cached('sys_less'))
        .pipe(sourcemaps.init())
        .pipe(less({compress:true}))
        .pipe(sourcemaps.write('maps'))
        // .pipe(remember('sys_less'))
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
        // .pipe(pixrem())
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

