/**
 * Bootstrapping FlexyAdmin:
 * - Create Vue Instance
 * 
 * @author: Jan den Besten
 */

import Vue from 'vue'
import FlexyBlocks from './vue-components/flexyblocks.vue'
import VuePagination from './vue-components/vue-pagination.vue'
import VueGrid from './vue-components/vue-grid.vue'

var _ = require('lodash');

// Every component logs its name and props
// Vue.mixin({
//   created: function () {
//     if (this.$options._componentTag) {
//       console.log(this.$options._componentTag, this.$options.propsData);
//     }
//     else {
//       console.log('Some Vue Component/Instance ready');
//     }
//   },
// });


var vm = new Vue({
  el:'#main',
  components: { FlexyBlocks,VuePagination,VueGrid }
});
