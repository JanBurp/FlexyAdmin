/**
 * Bootstrapping FlexyAdmin:
 * - Import components
 * - Create Vue Instance
 * - Global Vue Settings (Mixins)
 * 
 * @author: Jan den Besten
 */

var AUTH_TOKEN = _flexy.auth_token;

var _ = require('lodash');

import Vue              from 'vue'
import Lang             from 'vue-lang'
import Axios            from 'axios'

import jdb              from './jdb-tools.js'
import flexyState       from './flexy-state.js'
import FlexyMessages    from './components/flexy-messages.vue'

import FlexyBlocks      from './components/flexy-blocks.vue'
import FlexyButton      from './components/flexy-button.vue'
// import FlexyModal       from './components/flexy-modal.vue'
import FlexyPagination  from './components/flexy-pagination.vue'
import FlexyGrid        from './components/grid/flexy-grid.vue'
import FlexyForm        from './components/form/flexy-form.vue'


// Languaga settings
const locales = {};
locales[_flexy.language] = JSON.parse(_flexy.language_keys);
Vue.use(Lang, {lang: _flexy.language, locales: locales});


Vue.mixin({
  
  data : function() {
    return {
      state : flexyState.state,
    }
  },

  methods: {

    /**
      Global method om Api aan te roepen. Options Object bevat de volgende properties:
      - url, de url van de api (auth,table,row, etc)
      - data, de mee te geven parameters
      - Laat ook progress bar & spinner zien
     */
    api : function(options) {
      var self = this;
      flexyState.showProgress();
      var method = 'GET';
      if (options.url==='row' && !_.isUndefined(options.data.where)) method = 'POST';
      var request = {
        method  : 'POST',
        url     : '_api/'+options.url,
        data    : options.data,
        headers : {
          'Authorization': AUTH_TOKEN,
          'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8',
        },
        transformRequest: [function (data) {
          if (!options.formData) {
            var requestString='';
            if (data) {
              requestString = jdb.serializeJSON(data);
            }
            return requestString;
          }
          return data;
        }],
        onDownloadProgress: function (progressEvent) {
          if (options.onDownloadProgress) {
            options.onDownloadProgress(progressEvent);
          }
          else {
            flexyState.setProgress(progressEvent.loaded,progressEvent.total);
          }
        },
      };
      flexyState.debug && console.log('api > ',request);
      return Axios.request( request ).then(function (response) {
        flexyState.hideProgress();
        flexyState.debug && console.log('api < ',response);
        return response;
      })
      .catch(function (error) {
        flexyState.hideProgress();
        flexyState.addMessage( self.$lang.error_api,'danger');
        console.log('api ERROR <',request,error);
        return {'error':error};
      });
    },
    
    
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


