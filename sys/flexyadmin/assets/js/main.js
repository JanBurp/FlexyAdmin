/**
 * Bootstrapping FlexyAdmin:
 * - Create Vue Instance
 * 
 * @author: Jan den Besten
 */

// var _ = require('lodash');
var _ = require('lodash/core');

import Vue from 'vue'
import VueForm from 'vue-form-2'

import FlexyBlocks from './vue-components/flexyblocks.vue'
import VuePagination from './vue-components/vue-pagination.vue'
import VueGrid from './vue-components/vue-grid.vue'


// var css = require("!css!sass!../scss/flexyadmin.scss");
// // HACK TODO: Require does't work as expected, so include it by hand
// var head = document.head || document.getElementsByTagName('head')[0];
// var style = document.createElement('style');
// style.type = 'text/css';
// style.appendChild(document.createTextNode(css));
// head.appendChild(style);


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

Vue.use( VueForm,{
  layout:'form-horizontal',
  
});

var vm = new Vue({
  el:'#main',
  components: { FlexyBlocks,VuePagination,VueGrid},
});
