<script>

import jdb  from '../../jdb-tools.js'

export default {
  name: 'select-option',
  props:{
    'label'       : String,
    'extraClass' : String,
  },
  
  data : function() {
    return {
    };
  },
  
  methods : {
    style : function(value) {
      if ( value.substr(0,1)=='#' && value.length==7 ) {
        return 'color:'+jdb.complementColor(value)+';background-color:'+value+';padding:.25rem .5rem;';
      }
      return '';
    },
  },
  
}
</script>

<template>
  <span class="select-option" :class="extraClass" :style="style(label)">
    {{label}}
  </span>
</template>