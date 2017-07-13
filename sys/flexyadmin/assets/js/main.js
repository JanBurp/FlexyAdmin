/**
 * Bootstrapping FlexyAdmin:
 * - Import components
 * - Create Vue Instance
 * - Global Vue Settings (Mixins)
 * 
 * @author: Jan den Besten
 */

var _ = require('lodash');

import Vue              from 'vue'
import VueRouter        from 'vue-router'
import Lang             from 'vue-lang'

import flexyState       from './flexy-state.js'
import FlexyMessages    from './components/flexy-messages.vue'
import FlexyAccordion   from './components/flexy-accordion.vue'

import FlexyBlocks      from './components/flexy-blocks.vue'
import FlexyButton      from './components/flexy-button.vue'
import FlexyModal       from './components/flexy-modal.vue'
import FlexyPagination  from './components/flexy-pagination.vue'
import FlexyGrid        from './components/grid/flexy-grid.vue'
import FlexyForm        from './components/form/flexy-form.vue'
import mediapicker      from './components/form/mediapicker.vue'

// Routes
import RouteGrid        from './routes/grid.vue'
import RouteMedia       from './routes/media.vue'
import Route404         from './routes/404.vue'


// Import TinyMCE
import tinymce from 'tinymce/tinymce';
import 'tinymce/themes/modern/theme';


if ( !_.isUndefined(_flexy.auth_token) ) {
   // TinyMCE Global & Set extra
  _flexy.tinymceOptions = JSON.parse(_flexy.tinymceOptions);
  _flexy.tinymceOptions['link_list'] = '_api/get_link_list?_authorization='+_flexy.auth_token;
  _flexy.tinymceOptions['image_list'] = '_api/get_image_list?_authorization='+_flexy.auth_token;
  tinymce.init( _flexy.tinymceOptions );

  // Language settings
  const LOCALES = {};
  // _flexy.language_keys = JSON.parse(_flexy.language_keys);
  LOCALES['lang'] = _flexy.language;
  LOCALES[_flexy.language] = _flexy.language_keys;
  window.VueStrapLang = function() { return LOCALES[_flexy.language]['strap_lang']; }
  Vue.use(Lang, {lang: _flexy.language, locales: LOCALES});

  // Global Vue registering (state)
  Vue.mixin({
    data : function() {
      return {
        state : flexyState.state,
      }
    },
  });

  // ROUTER
  Vue.use(VueRouter);

  // const Grid = { template: '<div>GRID: {{ $route.params.table }} <flexy-grid :name="$route.params.table" :title="$route.params.table"></flexy-grid></div>' }
  
  const routes = [
    { path: '/grid/:table', component: RouteGrid },
    { path: '/media/:path', component: RouteMedia },
    // { path: '*', component: Route404 }
  ]
  const router = new VueRouter({
    // mode: 'history',
    routes // short for `routes: routes`
  })


  /**
     Main Vue Instance
   */
  var vm = new Vue({
    router,
    el:'#main',
    components: {
      FlexyBlocks,
      FlexyButton,
      FlexyModal,
      FlexyMessages,
      FlexyPagination,
      FlexyGrid,
      FlexyForm,
      FlexyAccordion,
      mediapicker,
    },
    data : function() {
      return {
        global : flexyState,
        state  : flexyState.state,
        mediaPopup : {
          'src' : '',
          'alt' : ''
        },
      }
    },
    methods: {
      mediaPopupChanged : function(media) {
        this.mediaPopup.alt = media;
        this.mediaPopup.src = media;
      }
    }
  });

}

// LOGIN
else {
  
  /**
     Login Vue Instance
   */
  var vm = new Vue({
    el:'#main',
    components: {},
    data : {
      message                 : _flexy.message,
      forgottenPasswordDialog : false,
    },
    methods : {
      
      showForgottenPasswordDialog : function(show) {
        this.message = '';
        this.forgottenPasswordDialog = show;
      },
      
    },
    
  });
  
}

// runtime bug, just testing;
// window.setTimeout(function(){
//   console.log('start bug');
//   var bug = bugger;
// }, 500);

