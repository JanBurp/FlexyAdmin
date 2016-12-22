<script>
export default {
  name: 'FlexyModal',
  // props:['title','body','buttons'],
  data: function(){
    return {
      visible  : false,
      // Default options
      options : {
        title   : 'Title',
        body    : 'Message',
        buttons : {
          close  : {
            title  : 'Cancel',
            method : function(){
              alert('closed');
            },
          },
          ok     : {
            title  : 'Ok',
            method : function(){
              alert('Ok');
            },
          },
        },
      }
    }
  },
  
  methods : {
    
    show   : function(options) {
      if ( !_.isUndefined(options) ) {
        this.options = options;
      }
      this.visible = true;
    },
    
    close : function() {
      this.visible = false;
    },

    buttonClass : function(button) {
      return this.options.button.class || '';
    },
    
    clicked : function(button) {
      return this.options.button.method;
    },
    
  }
}
</script>

<template>
  <div class="flexy-modal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="flexy-modal-title">{{title}}</h4>
        </div>
        <div class="modal-body" raw-html="body"></div>
        <div class="modal-footer">
          <button v-for="button in buttons" v-on:click="clicked(button)" type="button" class="btn" :class="buttonClass(button)">{{button.title}}</button>
        </div>
      </div>
    </div>
  </div>
</template>
