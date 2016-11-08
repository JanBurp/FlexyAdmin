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
import Axios            from 'axios'

import flexyState       from './flexy-state.js'
import FlexyMessages    from './components/flexy-messages.vue'

import FlexyBlocks      from './components/flexy-blocks.vue'
import FlexyPagination  from './components/flexy-pagination.vue'
import FlexyGrid        from './components/grid/flexy-grid.vue'
import FlexyForm        from './components/form/flexy-form.vue'


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
          var requestString='';
          if (data) {
            requestString = self.flexySerializeJSON(data);
          }
          return requestString;
        }],
        onDownloadProgress: function (progressEvent) {
          flexyState.setProgress(progressEvent.loaded,progressEvent.total);
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
        flexyState.addMessage('API error, see console for details','danger');
        console.log('api ERROR <',request,error);
        return {'error':error};
      });
    },
    
    
    /* Maakt normale POST data (string) van meegegeven Object */
    flexySerializeJSON : function(data) {
      var serializeString='';
      if ( !_.isUndefined(data) ) {
        // sort the keys, so the returned string has always same order of keys
        var keys = Object.keys(data).sort();
        // Loop the keys
        for (var i = 0; i < keys.length; i++) {
          var key=keys[i];
          if (serializeString!=='') serializeString+='&';
          // array
          if (_.isArray(data[key])) {
            data[key].forEach(function(el,index) {
              if (serializeString!=='') serializeString+='&';
              serializeString += encodeURIComponent(key) + '[]=' + encodeURIComponent(el);
            });
          }
          // object
          if (_.isObject(data[key])) {
            _.forEach(data[key], function(el,index) {
              if (serializeString!=='') serializeString+='&';
              serializeString += encodeURIComponent(key) + '['+index+']=' + encodeURIComponent(el);
            });
          }
          // normal
          else {
            serializeString += encodeURIComponent(key) + '=' + encodeURIComponent(data[key]);
          }
        }
      }
      return serializeString;
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
    FlexyMessages,
    FlexyPagination,
    FlexyGrid,
    FlexyForm,
  },

  data : {
    state : flexyState.state,
  },
  
});


