/**
 * FlexyAdmin login
 * 
 * @author: Jan den Besten
 */

// require("babel-polyfill");

import Vue              from 'vue'

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