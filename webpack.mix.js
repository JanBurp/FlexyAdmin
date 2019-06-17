let mix = require('laravel-mix');

// Kies frontend framework, stel dat ook in in config.php

// var framework = 'bootstrap3';          // bootstrap 3 & jQuery
var framework = 'bootstrap4';          // bootstrap 4 & jQuery [DEFAULT]
// var framework = 'bootstrap4vue';       // bootstrap 4 & VueJS

var proxy = __dirname.replace('/Users/jan/Sites/','http://localhost/') + '/public';

mix
  .setResourceRoot('../assets/')
  .setPublicPath('public/assets');

switch (framework) {
  case 'bootstrap3' :
    mix.js('public/assets/js/site.js', 'public/assets/scripts.min.js');
    mix.less('public/assets/less-bootstrap/bootstrap.less', 'public/assets/styles.min.css');
    break;

  case 'bootstrap4' :
    mix.js('public/assets/js/site-jquery.js', 'public/assets/scripts.min.js');
    mix.sass('public/assets/scss/bootstrap.scss', 'public/assets/styles.min.css');
    break;

  case 'bootstrap4vue' :
    mix.js('public/assets/js/site-vue.js', 'public/assets/scripts.min.js');
    mix.sass('public/assets/scss/bootstrap.scss', 'public/assets/styles.min.css');
    break;
}

mix
  .version()
  .sourceMaps()
  .browserSync({
    proxy: proxy,
  })
;

console.log( 'Building for framework: ' + framework );
