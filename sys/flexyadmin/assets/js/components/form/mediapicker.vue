<script>

import flexyButton      from '../flexy-button.vue'
import flexyThumb       from '../flexy-thumb.vue'

export default {
  name: 'MediaPicker',
  components: {flexyButton,flexyThumb},
  props:{
    'value'   : String,
    'name'    : String,
    'path'    : String,
  },
  computed : {
  },
  
  data : function() {
    return {
      media : this.value,
    };
  },
  
  methods : {
    
    thumbs : function() {
      var media = this.media;
      var array = media.split('|');
      for (var i = 0; i < array.length; i++) {
        array[i] = {
          src : '_media/thumb/' + this.path +'/'+ array[i],
          alt : array[i],
        }
      }
      return array;
    },
    
    changeMedia : function(media) {
      this.media = media;
      this.$emit('input',this.media);
    },
    
  },
  
}
</script>

<template>
  <div class="mediapicker">
    <flexy-button icon="plus" class="btn-outline-warning" size="xlg" />
    <flexy-thumb v-for="img in thumbs()" size="lg" :src="img.src" :alt="img.alt" />
  </div>
</template>

