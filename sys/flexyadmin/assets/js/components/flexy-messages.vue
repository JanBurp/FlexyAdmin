<script>
export default {
  name: 'FlexyMessages',
  props:['messages'],
  methods : {
    typeClass : function(message) {
      return 'alert-'+message.type;
    },
  }
}
</script>

<template>
  <div id="messages">
    <transition-group name="slideUp" tag="div">
      <div v-for="message in state.messages" :key="message" class="alert" :class="typeClass(message)" v-html="message.text"></div>
    </transition-group>
  </div>
</template>
