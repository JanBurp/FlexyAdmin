/**
 * Simple State management for:
 * - progress bar
 * - messages
 */

import Axios from 'axios';
import jdb   from './jdb-tools.js'

export default {
  name: 'FlexyState',
  debug: false,
  state: {
    progress    : 0,
    messages    : [],
    media_view  : _flexy.media_view,
    _modal      : {
      callback  : undefined,
      state     : undefined,
    },
    modal       : {
      show    : false,
      title   : 'Title',
      body    : 'Message',
      buttons : [
        {
          type   : 'cancel',
          title  : _flexy.language_keys.cancel,
          class  : 'btn-outline-primary',
          close  : true,
        },
        {
          type   : 'ok',
          title  : _flexy.language_keys.ok,
          class  : 'btn-outline-danger',
          close  : true,
        },
      ],
    },
  },
  
  getState : function(item) {
    return this.state[item];
  },
  
  /**
   * PROGRESS
   */
  showProgress : function() {
    this.state.progress = 10;
    this.debug && console.log('state.progress',this.state.progress); 
  },
  hideProgress : function() {
    var self=this;
    self.state.progress = 100;
    window.setTimeout(function(){
      self.state.progress = 0;
      self.debug && console.log('state.progress',self.state.progress); 
    }, 250);
  },
  setProgress : function(value,max) {
    var percent = Math.round(value * 100 / max);
    if (percent<10) percent=10; // Start met minimaal 10%
    this.state.progress = percent;
    this.debug && console.log('state.progress',this.state.progress); 
  },
  
  /**
   * Modal
   */
  openModal : function(options,callback) {
    if (!_.isUndefined(options)) _.merge(this.state.modal,options);
    this.state.modal.show = true;
    this.state._modal.callback = callback;
    var buttonEL = document.querySelector('#flexyadmin-modal .modal-footer button:last-child');
    buttonEL.focus();
  },
  modalState : function(state) {
    this.state._modal.state = state;
  },
  closeModal  : function() {
    this.state.modal.show = false;
    if ( !_.isUndefined(this.state._modal.callback) ) {
      this.state._modal.callback.call( this, this.state._modal );
    }
  },
  
  /**
   * Messages
   */
  addMessage : function(message,type) {
    if (_.isUndefined(type)) type='success';
    var self = this;
    self.state.messages.push({'text':message,'type':type});
    self.debug && console.log('state.messages',self.state.messages); 
    if (type!=='danger') {
      window.setTimeout(function(){
        self.state.messages.shift();
        self.debug && console.log('state.messages',self.state.messages); 
      }, 3000);
    }
  },
  removeMessage : function(id) {
    this.state.messages.splice(id,1);
  },
  
  
  /**
   * Media view
   */
  getMediaView : function() {
    return this.state.media_view;
  },
  setMediaView : function(view) {
    var self = this;
    this.state.media_view = view;
    this.debug && console.log('state.media_view',this.state.media_view); 
    return self.api({
      url : 'row',
      'data': {
        'table' : 'cfg_users',
        'where' : 'current',
        'data'  : {
          'str_filemanager_view' : self.state.media_view,
        }
      },
    });
  },
  
  
  
  /**
   * API
   * Global method om Api aan te roepen. Options Object bevat de volgende properties:
   * - url, de url van de api (auth,table,row, etc)
   * - data, de mee te geven parameters
   * - Laat ook progress bar & spinner zien
   */
  api : function(options) {
    var self = this;
    self.showProgress();

    var method = 'GET';
    if (options.url==='row' && !_.isUndefined(options.data.where)) method = 'POST';
    
    var defaultRequest = {
      method  : method,
      headers : {
        'Authorization': _flexy.auth_token,
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
          self.setProgress(progressEvent.loaded,progressEvent.total);
        }
      },
      // timeout: 1000,
    };
    
    // Request Options
    var request = _.extend( defaultRequest, options );
    // Standard URL for request
    request.url = '_api/' + request.url;
    
    self.debug && console.log('api > ',request);
    return Axios.request( request ).then(function (response) {
      self.hideProgress();
      self.debug && console.log('api < ',response);
      // trace/bug?
      if (typeof(response.data)==='string' && response.data.substr(0,1)==='<') {
        self.addMessage(response.data,'danger');
        console.log('TRACE', jdb.stripHTML(response.data) );
        var startOfObject = response.data.indexOf('{"success":');
        response.data = JSON.parse(response.data.substr(startOfObject));
      }
      return response;
    })
    .catch(function (error) {
      self.hideProgress();
      if (error.response) {
        // The request was made, but the server responded with a status code
        // that falls out of the range of 2xx
        if (error.response.status==401) {
          self.addMessage(_flexy.language_keys.api_error_401,'danger');
        }
        else {
          console.log('api ERROR <',error, error.response, error.config);
          self.addMessage(error.response.data,'danger');
        }
      } else {
        // Something happened in setting up the request that triggered an Error
        console.log('api ERROR <', error.message,error.config);
        self.addMessage(error.response.data,'danger');
      }
      return {'error':error};
    });
  },
  
  
  
};
