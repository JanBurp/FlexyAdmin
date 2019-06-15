let mix = require('laravel-mix');

var proxy = __dirname.replace('/Users/jan/Sites/','http://localhost/') + '/public';

mix
  .setResourceRoot('../assets/')
  .setPublicPath('public/assets')

  // Bootstrap 3 & jQuery (with site.php & menu)        -- set platform in config.php
  // .js('public/assets/js/site.js', 'public/assets/scripts.min.js')
  // .less('public/assets/less-bootstrap/bootstrap.less', 'public/assets/styles.min.css')

  // Bootstrap 4 & jQuery (with site-bootstrap4.php)    -- set platform in config.php
  .js('public/assets/js/site-jquery.js', 'public/assets/scripts.min.js')
  .sass('public/assets/scss/bootstrap.scss', 'public/assets/styles.min.css')

  // Bootstrap 4 & VueJS (with site-vue.php & menu_vue) -- set platform in config.php
  // .js('public/assets/js/site-vue.js', 'public/assets/scripts.min.js')
  // .sass('public/assets/scss/bootstrap.scss', 'public/assets/styles.min.css')

  .version()
  .sourceMaps()
  .browserSync({
    proxy: proxy,
  })
;
