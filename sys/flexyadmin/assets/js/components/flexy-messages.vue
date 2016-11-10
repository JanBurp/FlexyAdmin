<script>
import flexyState from '../flexy-state.js'
export default {
  name: 'FlexyMessages',
  props:['messages'],
  methods : {
    typeClass : function(message) {
      return 'alert-'+message.type;
    },
    removeMessage : function(id) {
      flexyState.removeMessage(id);
    },
  }
}
</script>

<template>
  <div id="messages">
    <transition-group name="slideUp" tag="div">
      <div v-for="(message,id) in state.messages" :key="message" class="alert" :class="typeClass(message)">
        <button v-on:click="removeMessage(id)" v-if="message.type==='danger'" class="btn btn-sm btn-danger message-button"><span class="fa fa-close"></span></button>
        <div v-html="message.text" class="message-text"></div>
      </div>
    </transition-group>
  </div>
</template>

<style>
  .message-text {width:90%;}
  .message-button {float:right;margin-top:-.25rem;}
</style>
