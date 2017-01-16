<script>
import flexyState  from '../flexy-state.js'
import flexyButton from './flexy-button.vue'

export default {
  name: 'FlexyModal',
  components: {flexyButton},
  props:{
    'options':{
      type:Object,
      default:{},
    },
  },
  
  data: function() {
    return {
      settings : this.options,
    }
  },
  
  methods : {
    
    close : function() {
      flexyState.modalState({type:'close'});
      flexyState.closeModal();
    },
    
    enter : function() {
      flexyState.modalState({type:'enter'});
      flexyState.closeModal();
    },
    
    clickedButton : function(index) {
      var button = this.settings.buttons[index];
      flexyState.modalState({type:button.type,button:button});
      if (button.close) flexyState.closeModal();
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
  
  <div class="modal flexy-modal" id="flexyadmin-modal" :class="{'hidden':!settings.show}">
    <div class="modal-dialog modal-sm" @keyup.esc="close()" @keyup.enter="enter()">
      <div class="modal-content">
        <div v-show="settings.title!==''" class="modal-header bg-primary text-white">
          <flexy-button @click.native="close()" icon="remove" class="btn-danger" />
          <h4 class="modal-title">{{settings.title}}</h4>
        </div>
        <div class="modal-body">{{settings.body}}</div>
        <div class="modal-footer">
          <button v-for="(button,index) in settings.buttons" @click="clickedButton(index)" type="button" class="btn" :class="button.class">{{button.title}}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
  .flexy-modal {display:block;height:auto;background-color:rgba(0,0,0,.5)}
  .flexy-modal.hidden {display:block;height:0px;}
  .flexy-modal .modal-header .flexy-button {float:right;}
  .flexy-modal .modal-footer .btn {margin-left:1rem;}
</style>
