<script>
import flexyState   from '../flexy-state.js'
import flexyButton  from './flexy-button.vue'

export default {
  name: 'FlexyMessages',
  components: {flexyButton},
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
  <div id="messages" class="navbar navbar-fixed-top">
    <transition-group name="slideUp" tag="div">
      <div v-for="message in state.messages" :key="message.id" class="alert" :class="typeClass(message)">
        <flexy-button @click.native="removeMessage(message.id)" icon="close" v-if="message.type==='danger'" class="btn-danger message-button" />
        <div v-html="message.text" class="message-text"></div>
      </div>
    </transition-group>
  </div>
</template>

<style>
  #messages > div {width:100%}
</style>
