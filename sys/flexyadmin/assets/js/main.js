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
// import tab              from './vue-strap-src/components/Tab.vue'
// import tabs             from './vue-strap-src/components/Tabs.vue'
// import tabGroup         from './vue-strap-src/components/TabGroup.vue'

import FlexyBlocks      from './components/flexy-blocks.vue'
import FlexyPagination  from './components/flexy-pagination.vue'
import FlexyGrid        from './components/grid/flexy-grid.vue'
import FlexyForm        from './components/form/flexy-form.vue'

var vm = new Vue({
  el:'#main',
  components: {
    FlexyBlocks,
    FlexyPagination,
    FlexyGrid,
    FlexyForm,
  },
});
