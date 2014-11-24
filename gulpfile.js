/**
 * Frontend framework [default|bootstrap]
 */
var framework = 'default';
// var framework = 'bootstrap';

/**
 * Paden (hoef je niet aan te passen)
 */
var bower     = 'bower_components';
var assets    = 'site/assets';
var sys       = 'sys';

/**
 * De bestanden die per framework meegenomen moeten worden (pas aan naar behoefte)
 */
var files = {

  'default' : {
    'js'      : assets+'/js',                             // js map
    'jshint'  : assets+'/js/site.js',                     // js bestanden die gecontroleerd moeten worden op fouten
    'jsmin'   : [                                         // js bestanden die samengevoegd en gemimificeerd moeten worden 
      assets+'/js/rem.min.js',
      sys   +'/jquery/jquery-1.11.1.min.js',
      assets+'/js/site.js',
    ],
    'jsdest'  : 'scripts.min.js',                         // gemimificeerd js bestand
    'watchjs' : assets+'/js/site.js',
    'css'     : assets+'/css',                            // css map
    'less'    : assets+'/less-default/*.less',            // less bestanden die gecompileerd moeten worden (gecompileerd .css bestand komt in de css map terecht)
    'cssmin'  : [                                         // all css bestanden die samengevoegd en gemimificeerd moeten worden
      assets+'/css/normalize.css',
      assets+'/css/text.css',
      assets+'/css/layout.css'
    ],
    'cssdest' : 'styles.min.css',                         // gemimificeerd css bestand
    'watchcss': [
      assets+'/less-default/*.less'
    ],
    
  },

  'bootstrap' : {
    'js'      : assets+'/js',
    'jshint'  : assets+'/js/site.js',
    'jsmin'   : [
      assets+'/js/rem.min.js',
      sys   +'/jquery/jquery-1.11.1.min.js',
      assets+'/js/bootstrap.min.js',
      assets+'/js/site.js',
    ],
    'jsdest'  : 'scripts.min.js',
    'watchjs' : assets+'/js/site.js',
    'less'    : assets+'/less-bootstrap/bootstrap.less',
    'css'     : assets+'/css',
    'cssmin'  : [
      assets+'/css/bootstrap.css'
    ],
    'cssdest' : 'styles.min.css',
    'watchcss': [
      assets+'/less-bootstrap/*'
    ]
  },


};


/**
 * Notify options
 */
var title   = ' ['+framework+']';
var message = '<%= file.relative %>';


/**
 * Load plugins
 */
var gulp        = require('gulp');
var notify      = require("gulp-notify");
var concat      = require('gulp-concat');
var less        = require('gulp-less');
var autoprefixer= require('gulp-autoprefixer');
var sourcemaps  = require('gulp-sourcemaps');
var minify_css  = require('gulp-minify-css');
var jshint      = require('gulp-jshint');
var stylish     = require('jshint-stylish');
var uglify      = require('gulp-uglify');
var livereload  = require('gulp-livereload');
var gutil       = require('gulp-util');
var plumber     = require('gulp-plumber');

/**
 * Calling 'gulp --build' minify the css (without sourcemaps)
 */
var build = false;
if(gutil.env.build === true) build=true;


/** TASKS ************************************************/


var onError = function (err) {  
  gutil.beep();
  console.log(err);
};


/**
 * move installed bower components to assets
 */
gulp.task('install', function() {
  // Move bootstrap files
  gulp.src([
     // bower+'/bootstrap/dist/**/*.min.css',
     bower+'/bootstrap/dist/**/*.min.js',
     bower+'/bootstrap/dist/**/glyphicons*',
    ]).pipe(gulp.dest( assets ));
  gulp.src( bower+'/bootstrap/less/*')
    .pipe(gulp.dest(assets +'/less-bootstrap/bootstrap'));
  gulp.src( bower+'/bootstrap/less/mixins/*' )
    .pipe(gulp.dest(assets +'/less-bootstrap/bootstrap/mixins'))
    .pipe(notify("Bootstrap moved"));
});


/**
 * JS Hint, check js file(s) for bugs and if a bug found: stop.
 */
gulp.task('jshint',function(){
  return gulp.src( files[framework]['jshint'] )
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
          message: message 
        }));
});


/**
 * Unglify & combine JS files
 */
gulp.task('jsmin',['jshint'],function(){
  return gulp.src( files[framework]['jsmin'] )
        .pipe(uglify())
        .pipe(concat(files[framework]['jsdest']))
        .pipe(gulp.dest( files[framework]['js']) )
        .pipe(notify({
          title:  'JS contact & uglify ' + title,
          message: message
        }));
});

/**
 * compile LESS files
 */
gulp.task('less',function(){
  return gulp.src( files[framework]['less'] )
        .pipe(plumber({ errorHandler: onError }))
        .pipe(sourcemaps.init())
        .pipe(less())
        .pipe(sourcemaps.write('maps'))
        .pipe(gulp.dest( files[framework]['css'] ))
        .pipe(notify({
          title:   'LESS' + title,
          message: message
        }));
});
  
/**
 * Concat & Minify CSS
 */
gulp.task('cssmin',['less'],function(){
  return gulp.src( files[framework]['cssmin'] )
        .pipe(sourcemaps.init({loadMaps: true}))
        .pipe(autoprefixer())
        .pipe(concat(files[framework]['cssdest']))
        .pipe(sourcemaps.write('maps'))
        .pipe( build ? minify_css({keepSpecialComments:0,keepBreaks:false}) : gutil.noop() )
        .pipe(gulp.dest( files[framework]['css']) )
        .pipe(notify({
          title:   'CSS concat & minify' + title,
          message: message
        }));
});

gulp.task('default', ['jshint','jsmin','less','cssmin'] );


// Watchers
gulp.task('watch', function() {

  // watch for JS changes
  gulp.watch( files[framework]['watchjs'], ['jshint','jsmin'] );
 
  // watch for LESS/CSS changes
  gulp.watch( files[framework]['watchcss'], ['less','cssmin'] );
  
  // Watch any file for a change in assets folder and reload
  livereload.listen();
  gulp.watch( assets+'/**' ).on('change', livereload.changed);
  
});