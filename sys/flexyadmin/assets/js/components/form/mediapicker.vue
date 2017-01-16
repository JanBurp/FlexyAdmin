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
        // draggable     : 'div',
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
      var thumbs = [];
      for (var i = 0; i < array.length; i++) {
        if (array[i]!=='') {
          thumbs[i] = {
            value : array[i],
            src   : '_media/thumb/' + this.path +'/'+ array[i],
            alt   : array[i],
          }
        }
      }
      return thumbs;
    },
    
    removeMedia: function(index) {
      var currentMedia = this.media.split('|');
      var newMedia = _.clone(currentMedia);
      newMedia.splice(index, 1);
      newMedia = _.join(newMedia,'|');
      this.changeMedia(newMedia);
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
      <div v-for="(img,index) in thumbs()" class="mediapicker-thumb">
        <flexy-button icon="remove" class="btn-danger" @click.native="removeMedia(index)"/>
        <flexy-thumb size="lg" :src="img.src" :alt="img.alt" :value="img.value" />
      </div>
    </draggable>
  </div>
</template>

<style>
  .mediapicker {min-height:6rem;}
  .mediapicker>.flexy-button {float:left;margin-right:.5rem;}
  .mediapicker .mediapicker-thumb {display:inline;margin-right:.25rem;cursor:move;}
  .mediapicker .mediapicker-thumb>.flexy-button {position:absolute;}
</style>
