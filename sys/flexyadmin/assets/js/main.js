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

import FlexyBlocks      from './components/flexy-blocks.vue'
import FlexyButton      from './components/flexy-button.vue'
// import FlexyModal       from './components/flexy-modal.vue'
import FlexyPagination  from './components/flexy-pagination.vue'
import FlexyGrid        from './components/grid/flexy-grid.vue'
import FlexyForm        from './components/form/flexy-form.vue'


import VueTinymce from 'vue-tinymce'
Vue.use(VueTinymce)


// // TinyMCE
// import tinymce from 'tinymce/tinymce';
// import 'tinymce/themes/modern/theme';
// tinymce.init({
//   selector: 'textarea',
// });

// TinyMCE
// tinymce.init({
//   selector: 'textarea',
// });




// Language settings
const LOCALES = {};
LOCALES[_flexy.language] = JSON.parse(_flexy.language_keys);
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
    // FlexyModal,
    FlexyMessages,
    FlexyPagination,
    FlexyGrid,
    FlexyForm,
  },

  data : {
    state : flexyState.state,
  },
  
});


