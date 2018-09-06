<script>
var marked = require('marked');

import flexyButton      from '../flexy-button.vue';

export default {
  name: 'Markdown',
  components: {flexyButton},
  props:{
    'value'   : String,
    'name'    : String,
  },
  computed : {
  },
  
  data : function() {
    return {
      text    : this.value,
      preview : '',
      view    : 'markdown',
    };
  },

  created : function() {
    this.updatePreview();
  },
  
  methods : {

    changeView: function() {
      if (this.view === 'markdown') {
        this.view = 'preview';
        this.updatePreview();
      }
      else {
        this.view = 'markdown'; 
      }
    },

    changeMarkdown : function(text) {
      this.text = text;
      this.$emit('input',this.text);
      this.updatePreview();
    },

    updatePreview : function() {
      if (this.view==='preview') this.preview = marked(this.text);
    },

  },
  
}

</script>

<template>
  <div class="markdown">
    <flexy-button @click.native="changeView()" :icon="{'code':view==='preview','eye':view==='markdown'}" class="btn-default"/>  
    <textarea v-show="view==='markdown'" class="form-control markdown-editor" :value="value" v-on:input="changeMarkdown($event.target.value)" v-on:change="changeMarkdown($event.target.value)"></textarea>
    <div      v-show="view==='preview'" class="form-control markdown-preview" v-html="preview"></div>
  </div>
</template>


<style>
  .markdown .flexy-button {
    position:absolute;
    right:1rem;
    opacity:.5;
  }
  .markdown .flexy-button:hover {
    opacity:1;
  }
  .markdown .markdown-editor {
    font-family:courier;
    font-size:.9rem;
    min-height:40rem!important;
    background-color:#EFE9B7!important;
    color:#795443!important;
  }
  .markdown .markdown-preview {
    padding:1rem;
  }

</style>

