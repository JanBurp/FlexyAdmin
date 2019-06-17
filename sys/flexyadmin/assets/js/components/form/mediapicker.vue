<script>

import draggable        from 'vuedraggable'

import flexyState       from '../../flexy-state.js'

import flexyButton      from '../flexy-button.vue'
import flexyThumb       from '../flexy-thumb.vue'

import flexyGrid        from '../grid/flexy-grid.vue'

export default {
  name: 'MediaPicker',
  components: { draggable,flexyButton,flexyThumb,flexyGrid },
  props:{
    'value'   : String,
    'name'    : String,
    'path'    : String,
    'multiple':{
      type:Boolean,
      default:false,
    },
    'openpicker': {
      type:Boolean,
      default:false,
    },
    'autoresize': {
      type:Boolean,
      default:true,
    },
  },
  computed : {

    draggableOptions : function() {
      return {
        forceFallback : true,
      }
    },

  },

  data : function() {
    return {
      media   : this.value,
      choose  : this.openpicker,
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
            src   : _flexy.media+'thumb/' + this.path +'/'+ array[i],
            alt   : array[i],
          }
        }
      }
      // console.log('thumbs',thumbs);
      return thumbs;
    },

    openChoose : function(event) {
      event.stopPropagation();
      event.preventDefault();
      this.choose = true;
      // flexyState.eventbus.$emit('upload-file',event); // Uncomment dit en uploaden start meteen. mediapicker-choose moet dan v-show zijn ipv v-if
    },

    selection : function() {
      var selection = _.trim(this.media,'|').split('|');
      // console.log('mediapicker.selection():',selection);
      return selection;
    },

    selectMedia : function(media) {
      // console.log('selectMedia result',media);
      media = _.uniq(media);
      this.changeMedia(media);
    },

    toggleMedia : function(item) {
      var currentMedia = _.trim(this.media,'|').split('|');
      var newMedia = _.clone(currentMedia);
      if (!this.multiple) newMedia = [];
      var exist = false;
      for (var i = newMedia.length - 1; i >= 0; i--) {
        if ( newMedia[i]==item ) {
          newMedia.splice(i, 1);
          exist = true;
        }
      }
      if ( !exist ) {
        newMedia.push(item);
      }
      this.changeMedia(newMedia);
    },

    addMedia : function(item) {
      var currentMedia = _.trim(this.media,'|').split('|');
      var newMedia = _.clone(currentMedia);
      if (!this.multiple) newMedia = [];
      newMedia.push(item);
      this.changeMedia(newMedia);
    },

    removeMediaIndex : function(index) {
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
      this.media = _.trim(media,'|');
      this.$emit('input',this.media);
    },

  },

}
</script>

<template>
  <div class="mediapicker" :data-src="this.media" :data-alt="this.media" @drop="openChoose($event)" @dragover.prevent>
    <div class="mediapicker-selection">
      <div class="mediapicker-thumb mediapicker-thumb-button" v-if="!openpicker">
        <flexy-button :icon="{'plus':!choose,'chevron-up':choose}" class="btn-outline-warning" @click.native="choose=!choose" />
      </div>
      <draggable :list="thumbs()" v-bind="draggableOptions" @end="dragEnd($event)">
        <div v-for="(img,index) in thumbs()" class="mediapicker-thumb">
          <flexy-button icon="remove" class="mediapicker-remove-button btn-danger" @click.native="removeMediaIndex(index)"/>
          <flexy-thumb size="lg" :src="img.src" :alt="img.alt" :value="img.value" />
        </div>
      </draggable>
    </div>

    <div class="mediapicker-choose" v-if="choose">
      <flexy-grid type='mediapicker' api='table' :name="path" :title="$lang.file_select" offset="0" limit="10" :selection="selection()" :multiple="this.multiple" :autoresize="this.autoresize" @grid-toggle-item="toggleMedia($event)" @grid-uploaded-item="addMedia($event)"></flexy-grid>
    </div>

  </div>
</template>
