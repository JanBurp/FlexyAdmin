/**
 * Simple State management for:
 * - progress bar
 * - messages
 */

export default {
  name: 'FlexyState',
  debug: false,
  state: {
    progress    : 0,
    messages    : [],
    media_view  : _flexy.media_view,
  },
  
  getState : function(item) {
    return this.state[item];
  },
  
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
    var percent = max / value * 100;
    if (percent<10) percent=10; // Start met minimaal 10%
    this.state.progress = percent;
    this.debug && console.log('state.progress',this.state.progress); 
  },
  
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
  
  getMediaView : function() {
    return this.state.media_view;
  },
  
  setMediaView : function(view) {
    this.state.media_view = view;
    // TODO api call to change it
    this.debug && console.log('state.media_view',this.state.media_view); 
  },
  
};
