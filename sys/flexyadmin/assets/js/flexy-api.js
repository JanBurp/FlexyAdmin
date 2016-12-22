/**
 * API
 */
import flexyState       from './flexy-state.js'

export default {
  name: 'FlexyApi',
  debug: false,
  
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
        'Authorization': LOCALES['AUTH_TOKEN'],
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

};
