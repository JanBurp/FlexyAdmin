/**
 * gulpfile.js voor FlexyAdmin
 * 
 * - gulp install // alle benodigde bestanden van bower naar juiste plek verplaatsen
 * - gulp compile // compileer de SASS bestanden tot CSS bestanden
 * - gulp cssmin  // voeg de CSS bestanden samen, autoprefixed browser specifieke css en voegt fallback in px toe waar rem units worden gebruikt (roept eerst 'compile' aan)
 * - gulp jshint  // test JS bestanden op veelvoorkomende fouten
 * - gulp jsmin   // combineer en minificeer alle JS bestanden (roept eerst 'jshint' aan)
 * - gulp message // een test om te kijken of gulp werkt en er een notificatie komt
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
  'jsdest'  : 'build.min.js',
  'watchjs' : [
    assets+'/js/**',
  ],
  
  // CSS / SASS
  'compile'    : [
    assets+'/scss/flexyadmin.scss',
  ],
  'css'     : assets+'/css',
  'cssmin'  : [
    assets+'/css/bootstrap.css',
    assets+'/css/font-awesome.min.css',
    assets+'/css/flexyadmin.css',
  ],
  'cssdest' : 'flexyadmin.min.css',
  'watchcss': [
    assets+'/scss/**'
  ]
};


/** HIERONDER NIETS AANPASSEN!!! ****************************/


/**
 * Notify options
 */
var title        = '';
var message      = '<%= file.relative %>';
var dir          = __dirname.replace('/Users/','');


/**
 * Load plugins
 */
var gulp        = require('gulp');
var gutil       = require('gulp-util');
var notify      = require("gulp-notify");
var livereload  = require('gulp-livereload');
// jshint
var plumber     = require('gulp-plumber');
var jshint      = require('gulp-jshint');
var stylish     = require('jshint-stylish');
// jsmin
var flatten     = require('gulp-flatten');
var uglify      = require('gulp-uglify');
var sourcemaps  = require('gulp-sourcemaps');
var concat      = require('gulp-concat');
// sass
var sass        = require('gulp-sass');
// cssmin
var autoprefixer= require('gulp-autoprefixer');



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
    'message' : dir
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
  gulp.src( bower+'/bootstrap/scss/**')
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
    }));
});


/**
 * Unglify & combine JS files
 */
gulp.task('jsmin',['jshint'],function(){
  return gulp.src( files['jsmin'] )
    .pipe(flatten())
    .pipe(sourcemaps.init({loadMaps: true}))
    .pipe(uglify())
    .pipe(sourcemaps.write('maps'))
    .pipe(concat( files['jsdest']) )
    .pipe(gulp.dest( files['js'] ))
    .pipe(notify({
      title:  'JS contact & uglify ' + title,
      message: message
    }));
});


/**
 * compile & minify SASS files
 */
gulp.task('compile',function(){
  return gulp.src( files['compile'] )
    .pipe(plumber({ errorHandler: onError }))
    .pipe(sourcemaps.init())
    .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
    .pipe(sourcemaps.write('maps'))
    .pipe(gulp.dest( files['css'] ))
    .pipe(notify({
      title:   'SCSS' + title,
      message: message
    }));
});
  
/**
 * Concat CSS files
 */
gulp.task('cssmin',['compile'],function(){
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
      }));
  });
 
  // watch for LESS/CSS changes
  gulp.watch( files['watchcss'], { interval: watch_interval }, ['compile','cssmin','message'] );
  
  // Watch any resulting file changed
  livereload.listen();
  
  gulp.watch( [
    files['css'] + '/' + files['cssdest'],
    files['js']  + '/' + files['jsdest'],
    files['watchjs'],
  ], { interval: watch_interval } ).on('change', livereload.changed);
  
});

