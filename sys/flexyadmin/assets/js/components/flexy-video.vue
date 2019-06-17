<script>
export default {
  name: 'flexyVideo',
  props:{
    'video': [Object,String],
  },
  data: function() {
    return {
      src : '',
      url : '',
      ratio : '',
      ratios : [
        "21:9",
        "16:9",
        "4:3",
        "1:1",
      ],
    };
  },
  methods : {

    attributes : function() {
      var code = this.video;
      var ratio = this.ratios[2];
      if (typeof(code)=='string' && code.substr(0,1)=='{') {
        code = JSON.parse(code);
      }
      if (typeof(code)=='object') {
        if (!_.isUndefined(code.ratio)) {
          ratio = code.ratio;
        }
        code = code.code;
      }
      var attr = {
        code : code,
        ratio : ratio.replace(':','by'),
        src  : 'https://i.ytimg.com/vi/'+code+'/mqdefault.jpg',
        url  : 'https://www.youtube.com/watch?v='+code,
      };
      return attr;
    }

  },
}
</script>

<template>
  <div class="video-thumb" :class="'ratio-'+attributes().ratio">
    <a :href="url" target="_blank"><img  :src="attributes().src"></a>
  </div>
</template>

<style>
  .video-thumb {
    position:relative;
    display:block;
    margin:0;
    height:2rem;
    background-color:#000;
    border-radius:2px;
    overflow:hidden;
  }
  .video-thumb.ratio-21by9 {width:4.67rem}
  .video-thumb.ratio-16by9 {width:3.55rem}
  .video-thumb.ratio-4by3 {width:2.67rem}
  .video-thumb.ratio-1by1 {width:2rem}
  .video-thumb img {
    position:absolute;
    top: 50%;
    transform: translateY(-50%);
    border-radius:2px;
    width:100%;
  }
</style>
