<script>

import draggable        from 'vuedraggable'

import flexyButton      from '../flexy-button.vue'
import flexyThumb       from '../flexy-thumb.vue'

import flexyGrid        from '../grid/flexy-grid.vue'

export default {
  name: 'MediaPicker',
  components: {draggable,flexyButton,flexyThumb,flexyGrid},
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
      choose: false,
    };
  },
  
  methods : {
    
    thumbs : function() {
      var media = _.trim(this.media,'|');
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
    
    addMedia : function(media) {
      var currentMedia = _.trim(this.media,'|').split('|');
      var newMedia = _.clone(currentMedia);
      newMedia = newMedia.concat(media);
      newMedia = _.uniq(newMedia);
      this.changeMedia(newMedia);
    },
    
    removeMedia : function(index) {
      var currentMedia = _.trim(this.media,'|').split('|');
      var newMedia = _.clone(currentMedia);
      newMedia.splice(index, 1);
      this.changeMedia(newMedia);
    },
    
    dragEnd : function(event) {
      var oldIndex = event.oldIndex;
      var newIndex = event.newIndex;
      var currentMedia = _.trim(this.media,'|').split('|');
      var newMedia = _.clone(currentMedia);
      newMedia.splice(newIndex, 0, newMedia.splice(oldIndex,1)[0] );
      this.changeMedia(newMedia);
    },
    
    changeMedia : function(media) {
      if (typeof(media)!=='string') media = _.join(media,'|');
      this.media = media;
      this.$emit('input',this.media);
    },
    
  },
  
}
</script>

<template>
  <div class="mediapicker">
    <div class="mediapicker-selection">
      <flexy-button :icon="{'plus':!choose,'chevron-up':choose}" class="btn-outline-warning" @click.native="choose=!choose" />
      <draggable :list="thumbs()" :options="draggableOptions" @end="dragEnd($event)">
        <div v-for="(img,index) in thumbs()" class="mediapicker-thumb">
          <flexy-button icon="remove" class="btn-danger" @click.native="removeMedia(index)"/>
          <flexy-thumb size="lg" :src="img.src" :alt="img.alt" :value="img.value" />
        </div>
      </draggable>
    </div>
    
    <div class="mediapicker-choose" v-if="choose">
      <flexy-grid type='mediapicker' api='table' name="pictures" :title="$lang.file_select" offset="0" limit="10" @grid-selected="addMedia($event)"></flexy-grid>
    </div>
    
  </div>
</template>
