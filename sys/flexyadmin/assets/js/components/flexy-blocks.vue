<script>
export default {
  name: 'FlexyBlocks',
  props:['text','href'],
  computed: {
    chars : function() {
      var text = this.text.split('');
      var chars=[];
      for (var i = 0; i < text.length; i++) {
        chars[i] = {
          char : (text[i]===' '?'.':text[i]),
          css  : 'char-' + (text[i]===' '?'space':text[i]),
        };
      }
      return chars;
    },
  },
}
</script>

<template>
  <a v-bind:href="href" class="flexy-block-container">
    <span v-once v-for="char in chars" class="flexy-block btn btn-secondary" :title="text" v-bind:class="char.css">{{char.char}}</span>
  </a>
</template>

<style>
  .flexy-block-container {overflow: hidden;}
  .flexy-block {text-transform:uppercase;float:left;margin-right:1px;margin-bottom:5px;}
  .flexy-block.text-lowercase {text-transform:lowercase;}
  .flexy-block.char-space {
    visibility:hidden;
    padding-left:.25rem;
    padding-right:.25rem;
  }
</style>
