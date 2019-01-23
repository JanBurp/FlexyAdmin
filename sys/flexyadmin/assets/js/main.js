/**
 * Bootstrapping FlexyAdmin:
 * - Import components
 * - Create Vue Instance
 * - Global Vue Settings (Mixins)
 * 
 * @author: Jan den Besten
 */

require("babel-polyfill");

var _  = require('lodash');

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
import RouteEdit        from './routes/edit.vue'
import RouteMedia       from './routes/media.vue'
import RoutePlugin      from './routes/plugin.vue'
import RouteTools       from './routes/tools.vue'
import RouteLogout      from './routes/logout.vue'
import Route404         from './routes/route404.vue'


// Import TinyMCE
import tinymce from 'tinymce/tinymce';
import 'tinymce/themes/modern/theme';
// import 'tinymce/skins/lightgray/skin.min.css';
// import 'tinymce/skins/lightgray/content.min.css';
// import 'tinymce/skins/lightgray/fonts/';


// standard plugins
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/code';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/hr';
import 'tinymce/plugins/image';
import 'tinymce/plugins/imagetools';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/media';
import 'tinymce/plugins/paste';
import 'tinymce/plugins/preview';
import 'tinymce/plugins/table';
import 'tinymce/plugins/textpattern';
import 'tinymce/plugins/visualblocks';
import 'tinymce/plugins/wordcount';
// flexy plugins
import '../dist/plugins/flexy_count';
import '../dist/plugins/flexy_image';
import '../dist/plugins/flexy_link';

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
const router = new VueRouter({
  mode                 : 'history',
  base                 : _flexy.base_url,
  linkActiveClass      : 'active',
  linkExactActiveClass : 'active',

  routes : [
    { path: '/edit/:table/:id?/:type?', component: RouteEdit },
    { path: '/media/:path',             component: RouteMedia },
    { path: '/plugin',                  component: RoutePlugin },
    { path: '/plugin/:plugin*',         component: RoutePlugin },
    { path: '/tools/:tool*',            component: RouteTools },
    { path: '/logout',                  component: RouteLogout },
    { path: '*',                        component: Route404 }
  ],
  scrollBehavior (to, from, savedPosition) {
   return { x: 0, y: 0 }
  }
});



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
      global            : flexyState,
      state             : flexyState.state,
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
    },

  }
});


// console.log( 'Vue version:', Vue.version );

