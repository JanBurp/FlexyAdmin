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
import Lang             from 'vue-lang'

import flexyState       from './flexy-state.js'
import FlexyMessages    from './components/flexy-messages.vue'
import FlexyHelp        from './components/flexy-help.vue'

import FlexyBlocks      from './components/flexy-blocks.vue'
import FlexyButton      from './components/flexy-button.vue'
import FlexyModal       from './components/flexy-modal.vue'
import FlexyPagination  from './components/flexy-pagination.vue'
import FlexyGrid        from './components/grid/flexy-grid.vue'
import FlexyForm        from './components/form/flexy-form.vue'


// TinyMCE Global & Set extra
_flexy.tinymceOptions = JSON.parse(_flexy.tinymceOptions);
_flexy.tinymceOptions['link_list'] = '_api/get_link_list?_authorization='+_flexy.auth_token,
_flexy.tinymceOptions['image_list'] = '_api/get_image_list?_authorization='+_flexy.auth_token,
tinymce.init( _flexy.tinymceOptions );

// Language settings
const LOCALES = {};
// _flexy.language_keys = JSON.parse(_flexy.language_keys);
LOCALES['lang'] = _flexy.language;
LOCALES[_flexy.language] = _flexy.language_keys;
window.VueStrapLang = function() { return LOCALES[_flexy.language]['strap_lang']; }
Vue.use(Lang, {lang: _flexy.language, locales: LOCALES});


Vue.mixin({
  
  data : function() {
    return {
      state : flexyState.state,
    }
  },
})


/**
 Main Vue Instance
 */
var vm = new Vue({
  el:'#main',
  components: {
    FlexyBlocks,
    FlexyButton,
    FlexyModal,
    FlexyMessages,
    FlexyPagination,
    FlexyGrid,
    FlexyForm,
    FlexyHelp
  },

  data : {
    state : flexyState.state,
  },
  
});


