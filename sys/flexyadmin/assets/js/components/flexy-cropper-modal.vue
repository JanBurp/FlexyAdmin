<script>

import { Cropper }      from 'vue-advanced-cropper'
import flexyButton      from './flexy-button.vue';
import jdb              from '../jdb-tools.js'
import flexyState       from '../flexy-state.js'

export default {
  name: 'flexyCropperModal',
  components: {flexyButton,Cropper},
  props:{
    path : String,
    src  : String,
    width: Number,
    height:Number,
    scale:String,
  },

  data : function() {
    return {
      coordinates : {},
    };
  },

  computed : {

    aspectRatio : function() {
      let ratio = this.scale.split(':');
      ratio = ratio[0]/ratio[1];
      return ratio;
    },

  },

  methods :  {

    restrictions : function() {
      let restr = {
        maxWidth  : 100000,
        maxHeight : 100000,
        minWidth  : 0,
        minHeight : 0,
      }
      if (this.width<this.height) {
        restr.minWidth  = this.width;
        restr.minHeight = Math.round(this.width / this.aspectRatio);
      }
      else {
        restr.minWidth  = Math.round(this.height * this.aspectRatio);
        restr.minHeight = this.height;
      }
      // console.log('restrictions',restr);
      return restr;
    },

    close() {
      this.$emit('close');
    },

    change({coordinates, canvas}) {
      this.coordinates = coordinates;
    },

    crop() {
      let self = this;
      let data = {
        action  : 'crop',
        path    : this.path,
        where   : this.src,
        data    : {
          top     : Math.round(this.coordinates.top),
          left    : Math.round(this.coordinates.left),
          width   : Math.round(this.coordinates.width),
          height  : Math.round(this.coordinates.height),
        }
      }
      flexyState.api({
        method    : 'POST',
        url       : 'media',
        data      : data,
      }).then(function(response){
        self.$emit('cropped');
      });
    },

  },

};
</script>

<template>
  <div class="crop-modal modal fade show" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{$lang.crop_title}}</h5>
          <flexy-button @click.native="close()" icon="close" class="btn-outline-danger" />
        </div>
        <div class="modal-body">
          <cropper
              classname="cropper"
              :src="'_media/'+path+'/'+src"
              :stencilProps="{
                aspectRatio : aspectRatio
              }"
              :restrictions="restrictions"
              @change="change"
            ></cropper>
        </div>
        <div class="modal-footer">
          <button @click="close()" class="btn btn-outline-danger float-right">{{$lang.cancel}}</button>
          <button @click="crop()" class="btn btn-outline-primary float-right mr-2">{{$lang.crop}}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss">
  .crop-modal {
    display:none;
    background-color:rgba(0,0,0,0.5);
  }
  .crop-modal.show {
    display:block;
  }

</style>
