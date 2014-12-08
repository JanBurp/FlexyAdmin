/**
 * 
 * gulpfile.js voor FlexyAdmin - Jan den Besten 2014
 * 
 * Installatie:
 * - Installeer node & npm, zie: http://nodejs.org 
 * - Ga naar je Terminal en zorg dat je in de root bent van je site
 * - type: npm install // een nieuwe map 'node_modules' wordt aangemaakt waar oa gulp en bower worden geinstalleerd, kan even duren.
 * - type: bower install // een nieuwe map 'bower_components' wordt aangemaakt met oa de laatste versies van jquery en bootstrap
 * - type: gulp install // dit zorgt ervoor dat de laatste versies van oa jquery en bootstrap naar de assets map worden verplaatst
 * 
 * Gebruik van deze gulp:
 * - gulp // maak een complete build: compileren en minificeren van LESS, combineren van alle CSS in één bestand, combineren en minificeren van alle JS bestanden
 * - gulp watch // kijkt of er bestanden zijn veranderd, zo ja, build dat deel en doe een livereload naar de browser (installeer een livereload plugin in je browser om dat te laten werken)
 * 
 * Specifieker gebruik van deze gulp:
 * - gulp less // compileer alleen de LESS bestanden tot CSS bestanden
 * - gulp cssmin // voeg de CSS bestanden samen, autoprefixed browser specifieke css en voegt fallback in px toe waar rem units worden gebruikt (roept eerst 'less' aan)
 * - gulp jshint // test JS bestanden op veelvoorkomende fouten
 * - gulp jsmin // combineer en minificeer alle JS bestanden (roept eerst 'jshint' aan)
 * - gulp message // een test om te kijken of gulp werkt en er een notificatie komt
 * - gulp install // zie hierboven
 * 
 * Instellingen:
 * - framework : Kies hier 'default' of 'bootstrap', je frontend framework
 * - files     : Als je meer css/less of js bestanden gebruikt dan de standaard installatie, voeg ze dan hier toe (bij je gekozen framework)
 * 
 * Gevorderden:
 * - Je kunt bower gebruiken om eigen plugins toe te voegen en eenvoudig te update door bv: bower install jquery --save
 * - Pas dan de gulp task 'install' ook aan zodat je deze plugins ook automatisch op de goede plaats zet
 * - Voeg je eigen plugins natuurlijk ook toe bij 'files'.
 */

/**
 * Frontend framework [default|bootstrap]
 * Pas dit ook aan in site/config/config.php
 */
var framework = 'default';
// var framework = 'bootstrap';


/**
 * Om de hoeveel ms gulp watch z'n taken doet (100ms in standaard)
 */
var watch_interval = 100;


/** Paths (keep as is) */
var bower     = 'bower_components';
var assets    = 'site/assets';


/**
 * Bestanden die per framework moeten worden verwerkt.
 * Pas dit aan (bij je framework). Bij default staat overal commentaar achter voor uitleg.
 */
var files = {

  /**
   * default bestanden
   */
  'default' : {
    'js'      : assets+'/js',                             // JS map (string)
    'jshint'  : assets+'/js/site.js',                     // JS bestanden die gecontroleerd moeten worden op fouten (string of array)
    'jsmin'   : [                                         // JS bestanden die samengevoegd en gemimificeerd moeten worden (string of array)
      assets+'/js/jquery.min.js',
      assets+'/js/site.js',
    ],
    'jsdest'  : 'scripts.min.js',                         // gemimificeerd JS bestand (string)
    'watchjs' : assets+'/js/site.js',                     // check deze JS bestanden op veranderingen (string of array)
    'css'     : assets+'/css',                            // CSS map (string)
    'less'    : assets+'/less-default/*.less',            // LESS bestanden die gecompileerd moeten worden (string of array)
    'cssmin'  : [                                         // all CSS bestanden die samengevoegd en gemimificeerd moeten worden (string of array)
      assets+'/css/normalize.css',
      assets+'/css/text.css',
      assets+'/css/layout.css'
    ],
    'cssdest' : 'styles.min.css',                         // gemimificeerd css bestand (string)
    'watchcss': [                                         // check deze LESS (en/of CSS) bestanden op veranderingen (string of array)
      assets+'/less-default/*.less'
    ],
  },

  /**
   * bootstrap bestanden
   */
  'bootstrap' : {
    'js'      : assets+'/js',
    'jshint'  : assets+'/js/site.js',
    'jsmin'   : [
      assets+'/js/jquery.min.js',
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



/** HIERONDER NIETS AANPASSEN!!! ****************************/


/**
 * Notify options
 */
var title   = ' ['+framework+']';
var message = '<%= file.relative %>';
var icon    = 'sys/flexyadmin/assets/img/work.png';
var dir     = __dirname.replace('/Users/','');


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
    .pipe(gulp.dest(assets +'/less-bootstrap/bootstrap'));
  gulp.src( bower+'/bootstrap/less/mixins/*' )
    .pipe(gulp.dest(assets +'/less-bootstrap/bootstrap/mixins'));
  gulp.src( '' ).pipe(notify("Bootstrap moved"));

  // Verplaatst en minificeer normalize.css
  gulp.src( bower+'/normalize.css/*.css')
    .pipe(minify_css({keepSpecialComments:0,keepBreaks:false}))
    .pipe(gulp.dest(assets +'/css'))
    .pipe(notify("normalize.css moved and minified"));
    
  // Verplaatst jquery 1.11.x
  gulp.src( bower+'/jquery-legacy/dist/jquery.min.js')
    .pipe(gulp.dest(assets +'/js'))
    .pipe(notify("jquery.min.js (v1.11.x) moved"));
    
  // Voeg hier eventueel je eigen commandos toe
    
});


/**
 * JS Hint, check js file(s) for bugs and if a bug found: stop.
 */
gulp.task('jshint',function(){
  return gulp.src( files[framework]['jshint'] )
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


/**
 * Unglify & combine JS files
 */
gulp.task('jsmin',['jshint'],function(){
  return gulp.src( files[framework]['jsmin'] )
        .pipe(flatten())
        .pipe(sourcemaps.init({loadMaps: true}))
        .pipe(concat(files[framework]['jsdest']))
        .pipe(uglify())
        .pipe(sourcemaps.write('maps'))
        .pipe(gulp.dest( files[framework]['js']) )
        .pipe(notify({
          title:  'JS contact & uglify ' + title,
          message: message
        }));
});

/**
 * compile & minify LESS files
 */
gulp.task('less',function(){
  return gulp.src( files[framework]['less'] )
        .pipe(plumber({ errorHandler: onError }))
        .pipe(sourcemaps.init())
        .pipe(less({compress:true}))
        .pipe(sourcemaps.write('maps'))
        .pipe(gulp.dest( files[framework]['css'] ))
        .pipe(notify({
          title:   'LESS' + title,
          message: message
        }));
});
  
/**
 * Concat CSS files
 */
gulp.task('cssmin',['less'],function(){
  return gulp.src( files[framework]['cssmin'] )
        .pipe(sourcemaps.init({loadMaps: true}))
        .pipe(autoprefixer())
        .pipe(pixrem())
        .pipe(concat(files[framework]['cssdest']))
        .pipe(sourcemaps.write('maps'))
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
  gulp.watch( files[framework]['watchjs'], { interval: watch_interval }, ['jshint','jsmin','message'] );
 
  // watch for LESS/CSS changes
  gulp.watch( files[framework]['watchcss'], { interval: watch_interval }, ['less','cssmin','message'] );
  
  // Watch any resulting file changed
  livereload.listen();
  gulp.watch( [
    files[framework]['css'] + '/' + files[framework]['cssdest'],
    files[framework]['js']  + '/' + files[framework]['jsdest'],
  ], { interval: watch_interval } ).on('change', livereload.changed);
  
});