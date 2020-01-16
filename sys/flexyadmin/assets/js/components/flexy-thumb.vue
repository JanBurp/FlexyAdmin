<script>
export default {
  name: 'flexyThumb',
  props:{
    'src':String,
    'alt':{
      type:String,
      default:'',
    },
    'size':{
      type:String,
      default:'sm',
    },
    'scale' : {
      type:[Number,Boolean],
      default:false,
    },
    'sizes' : {
      type:Object,
      default:{},
    },
  },
  computed: {

    type : function() {
      const DEFAULT = 'file';
      const TYPES   = {
        'jpg'   : 'image',
        'jpeg'  : 'image',
        'gif'   : 'image',
        'png'   : 'image',
        'svg'   : 'image',

        'zip'   : 'fa-file-archive-o',
        'pdf'   : 'fa-file-pdf-o',

        'mp3'   : 'fa-file-audio-o',
        'ogg'   : 'fa-file-audio-o',
        'wav'   : 'fa-file-audio-o',
        'aiff'  : 'fa-file-audio-o',

        'mid'   : 'fa-music',
        'midi'  : 'fa-music',

        'mp4'   : 'fa-film',
        'mov'   : 'fa-film',
        'wmv'   : 'fa-film',
        'm4v'   : 'fa-film',
        'webm'  : 'fa-film',
        'ogv'   : 'fa-film',

        'xls'   : 'fa-file-excel-o',
        'xlsx'  : 'fa-file-excel-o',
        'doc'   : 'fa-file-word-o',
        'docx'  : 'fa-file-word-o',
      };

      var ext = this.src.split('.');
      ext = ext[ext.length-1].toLowerCase();

      var type = DEFAULT;
      if ( !_.isUndefined(TYPES[ext]) ) type = TYPES[ext];
      return type;
    },

    typeClass  : function() {
      return this.type + ' fa-'+this.size;
    },

    mediaClass : function() {
      return 'media-thumb-' + this.size;
    },

    scaleSizes : function() {
      var style = 'display:none';

      // square
      if ( Math.round(this.scale*100) == Math.round(this.sizes.scale*100)) {
        style = 'width:100%;height:100%';
      }

      // landscape
      if (this.scale > this.sizes.scale) {
        style = 'width:100%;height:'+Math.round(100/this.scale*this.sizes.scale)+'%';
      }

      // portrait
      if (this.scale < this.sizes.scale) {
        style = 'width:'+Math.round(100*(this.scale/this.sizes.scale))+'%;height:100%';
      }

      return style;
    },

    file : function() {
      let src = this.src;
      if ( this.sizes!=={} ) {
        src += '?';
        if (!_.isUndefined(this.sizes.width))   src += this.sizes.width+'x';
        if (!_.isUndefined(this.sizes.height))  src += this.sizes.height+'_';
        if (!_.isUndefined(this.sizes.size))    src += this.sizes.size;
      }
      return src;
    }

  },
};
</script>

<template>
  <div class="flexy-thumb">
    <img  v-if="type==='image'" :class="mediaClass" :src="file" :alt="alt" :title="alt">
    <span v-if="type!=='image'" class="fa" :class="typeClass" :title="file" :alt="alt"></span>
    <span v-if="type!=='image'" class="thumb-title">{{alt}}</span>
    <div v-if="type==='image' && scale" class="scale">
      <div class="scale-inner" :style="scaleSizes">
        <img :src="file">
      </div>
    </div>
  </div>
</template>

<style>
  .flexy-thumb {
    position: relative;
    display:inline;
    margin:0;
  }
  .flexy-thumb .fa {font-size:1.6rem;}
  .flexy-thumb .fa-lg {
    font-size:4.5rem;
    margin:.75rem .5rem;
  }

  .flexy-thumb img {width:auto;}
  .flexy-thumb .thumb-title {display:none;}
  .mediapicker-selection .flexy-thumb .thumb-title {display:inline;}

  .grid-media-view-thumbs .flexy-thumb .fa {font-size:10rem;}
  .grid-media-view-thumbs .flexy-thumb img {width:auto;max-width:14rem;height:auto;max-height:14rem;}

  .grid-media-view-small .flexy-thumb .fa {font-size:5rem;}
  .grid-media-view-small .flexy-thumb img {width:auto;max-width:6rem;height:auto;max-height:6rem;}

  .media-thumb-sm {
    height:1.7rem;
    border-radius:2px;
  }
  .media-thumb-lg {
    height:5rem;
    border-radius:2px;
  }

  .flexy-thumb .scale {
    position: absolute;
    top:0px;
    width:100%;
    height: 100%;
    background-color: rgba(0,0,0,.4);
  }
  .flexy-thumb .scale-inner {
    position: absolute;
    top:0px;
    border:dotted 1px #CCC;
    overflow:hidden;
  }


</style>
