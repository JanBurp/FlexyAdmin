import Vue from 'vue';

import { BootstrapVue } from 'bootstrap-vue'
Vue.use(BootstrapVue);

// import VueRouter from 'vue-router';
// Vue.use(VueRouter)

// import axios from 'axios'
// import VueAxios from 'vue-axios'
// Vue.use(VueAxios, axios);

// Automatically register all Vue components
let files = require.context('./components', true, /\.vue$/i);
let components = [];
files.keys().map(function(key){
  let name = key.split('/').pop().split('.')[0];
  components[name] = Vue.component(name, files(key).default);
});

// let routes = [];
// const router = new VueRouter({
//   routes
// });

// Voorkom waarschuwing door safe_mailto() <script>
Vue.config.warnHandler = function (msg, vm, trace) {
  if (!msg.indexOf('<script>')) {
    console.log(msg);
  }
}

new Vue({
  el: '#site',
  // router,
});

