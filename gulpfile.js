var gulp = require('gulp');
var concat = require('gulp-concat');
// var less = require('gulp-less');
// var less = require('gulp-uglify');


var base='sys/__test/';

var paths = {
  js:  [
    base+'jdb*.js',
    base+'flexy*.js',
    base+'flexy*/flexy*.js',
    '!'+base+'*_test.js'
  ],
  ext: [
    base+'external/angular/angular.js',
    base+'external/angular-route/angular-route.js',
    base+'external/angular-bootstrap/ui-bootstrap-tpls.min.js',
    base+'external/angular-bootstrap-show-errors/src/showErrors.min.js',
    base+'external/angular-http-auth/src/http-auth-interceptor.js',
    base+'external/angular-toArrayFilter/toArrayFilter.js',
    base+'external/angular-loading-bar/src/loading-bar.js',
    base+'external/angular-smart-table/dist/smart-table.min.js',
    base+'external/ng-sortable/dist/ng-sortable.min.js',
  ],
  css: 'css/',
};

// JS-EXTERNALS
gulp.task('angular-externals', function() {
  return gulp.src(paths.ext)
    .pipe(concat('concat_angular-externals.js'))
    .pipe(gulp.dest('sys/__test'));
});
// JS-APP
gulp.task('flexy-admin', function() {
  return gulp.src(paths.js)
    .pipe(concat('concat_flexy-admin-app.js'))
    .pipe(gulp.dest('sys/__test'));
});


// Default
gulp.task('default', ['angular-externals','flexy-admin'] );

// Watcher
gulp.task('watch', ['flexy-admin'], function() {
  gulp.watch('sys/__test/**/*.js', ['js']);
});