<script>

import draggable        from 'vuedraggable'

import flexyButton      from '../flexy-button.vue'
import flexyThumb       from '../flexy-thumb.vue'

export default {
  name: 'MediaPicker',
  components: {draggable,flexyButton,flexyThumb},
  props:{
    'value'   : String,
    'name'    : String,
    'path'    : String,
  },
  computed : {
    
    draggableOptions : function() {
      return {
        // group         : { name:'tree', pull:true},
        draggable     : 'div',
        forceFallback : true,
        // handle        : '.draggable-handle',
        // scroll        : true,
        // scrollFn      : function(offsetX, offsetY, originalEvent) {
        //   console.log(offsetY);
        //
        // },
      }
    },
    
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
          value : array[i],
          src   : '_media/thumb/' + this.path +'/'+ array[i],
          alt   : array[i],
        }
      }
      return array;
    },
    
    dragEnd : function(event) {
      var oldIndex = event.oldIndex;
      var newIndex = event.newIndex;
      var currentMedia = this.media.split('|');
      var newMedia = _.clone(currentMedia);
      newMedia.splice(newIndex, 0, newMedia.splice(oldIndex,1)[0] );
      newMedia = _.join(newMedia,'|');
      this.changeMedia(newMedia);
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
    <draggable :list="thumbs()" :options="draggableOptions" @end="dragEnd($event)">
      <flexy-thumb v-for="img in thumbs()" size="lg" :src="img.src" :alt="img.alt" :value="img.value" />
    </draggable>
  </div>
</template>

<style>
  .mediapicker .flexy-button {float:left;margin-right:.5rem;}
  .mediapicker .flexy-thumb {cursor:move;}
</style>
