/**
 * Simple State management for:
 * - progress bar
 * - messages
 */

export default {
  name: 'FlexyState',
  debug: false,
  state: {
    progress  : 0,
    messages    : [],
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
    window.setTimeout(function(){
      self.state.messages.shift();
      self.debug && console.log('state.messages',self.state.messages); 
    }, 3000);
  },
  
};
