/**
 * Bootstrapping FlexyAdmin:
 * - Import components
 * - Create Vue Instance
 * - Global Vue Settings (Mixins)
 * 
 * @author: Jan den Besten
 */

// var _ = require('lodash');
var _ = require('lodash/core');

import Vue              from 'vue'
import tab              from './vue-strap/src/components/Tab.vue'
import tabs             from './vue-strap/src/components/Tabs.vue'
import tabGroup         from './vue-strap/src/components/TabGroup.vue'

import FlexyBlocks      from './components/flexy-blocks.vue'
import FlexyPagination  from './components/flexy-pagination.vue'
import FlexyGrid        from './components/grid/flexy-grid.vue'

// Vue.use( VueForm,{
//   layout:'form-horizontal',
// });

// Vue.mixin({
  // // Every component logs its name and props
  // created: function () {
  //   if (this.$options._componentTag) {
  //     console.log(this.$options._componentTag, this.$options.propsData);
  //   }
  //   else {
  //     console.log('Some Vue Component/Instance ready');
  //   }
  // },
// });

var vm = new Vue({
  el:'#main',
  components: {
    FlexyBlocks,
    FlexyPagination,
    FlexyGrid,
    tabs,
    tabGroup,
    tab,
  },
});
