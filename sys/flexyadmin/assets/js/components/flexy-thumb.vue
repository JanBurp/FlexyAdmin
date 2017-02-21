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
  },
  computed: {
    
    type : function() {
      const DEFAULT = 'file';
      const TYPES   = {
        'jpg'   : 'image',
        'jpeg'  : 'image',
        'gif'   : 'image',
        'png'   : 'image',
        
        'zip'   : 'fa-file-archive-o',
        'pdf'   : 'fa-file-pdf-o',
        
        'mp3'   : 'fa-file-audio-o',
        'ogg'   : 'fa-file-audio-o',
        'mp4'   : 'fa-file-audio-o',
        'wav'   : 'fa-file-audio-o',
        'aiff'  : 'fa-file-audio-o',

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
    
    mediaClass : function() {
      return 'media-thumb-' + this.size;
    },
    
  },
}
</script>

<template>
  <div v-once class="flexy-thumb">
    <img v-if="type==='image'" :class="mediaClass" :src="src" :alt="alt" :title="alt">
    <span v-if="type!=='image'" class="fa" :class="type" :title="src"></span>
  </div>
</template>

<style>
  .flexy-thumb {display:inline;margin:0;}
  .flexy-thumb .fa {font-size:1.6rem;}
  .flexy-thumb img {width:auto;}
  
  .grid-media-view-thumbs .flexy-thumb .fa {font-size:10rem;}
  .grid-media-view-thumbs .flexy-thumb img {width:auto;max-width:14rem;height:auto;max-height:14rem;}

  .grid-media-view-small .flexy-thumb .fa {font-size:5rem;}
  .grid-media-view-small .flexy-thumb img {width:auto;max-width:6rem;height:auto;max-height:6rem;}


  .media-thumb-sm {
    height:1.75rem;
    border-radius:2px;
  }
  .media-thumb-lg {
    height:5rem;
    border-radius:2px;
  }
</style>
