<script>

import jdb  from '../../jdb-tools.js'

export default {
  name: 'radio-image',
  props:{
    'settings' : {
      type: [Array,Object],
      default: {},
    },
    'index' : {
      type: [Number],
      default: 0,
    }
  },
  
  data : function() {
    return {
    };
  },
  
  methods : {
    style : function() {
      var style = 'background-image:url("'+ this.settings.src +'"); width:'+ this.settings.width +'px; height:'+ this.settings.height +'px;';
      style += "background-position: "+ (-this.index * this.settings.width) +"px; top";
      return style;
    },
  },
  
}
</script>

<template>
  <div class="radio-image" :style="style()"></div>
</template>

<style>
  .radio-image {
    display:inline-block;
  }
</style>
