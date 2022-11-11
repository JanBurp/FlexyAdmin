<script>
import flexyVideo  from '../flexy-video.vue'
export default {
  name: 'videoPicker',
  components: { flexyVideo },
  props:{
    'value'   : [String,Object],
    'name'    : String,
  },
  data: function() {
    return {
      code: '',
      src : '',
      ratio: '',
      url : '',
      ratios : [
        "21:9",
        "16:9",
        "4:3",
        "1:1",
      ],
    };
  },

  mounted : function() {
    this.calcData();
  },

  updated : function() {
    this.calcData();
  },

  methods : {

    calcData : function() {
      var code = this.value;
      var ratio = this.ratios[2];
      if (code.substr(0,1)=='{') {
        code = JSON.parse(code);
      }
      if (typeof(code)=='object') {
        ratio = code.ratio;
        code = code.code;
      }
      this.code = code;
      this.ratio = ratio;
      this.src = 'https://img.youtube.com/vi/'+code+'/2.jpg';
      this.url = 'https://www.youtube.com/watch?v='+code;
    },

    changeCode : function(value) {
      this.code = this.get_video_code_from_url(value);
      this.updateEvent();
    },
    changeRatio : function(value) {
      this.ratio = value;
      this.updateEvent();
    },
    updateEvent : function() {
      var data = {
        code : this.code,
        ratio : this.ratio,
        platform : 'youtube',
      }
      data = JSON.stringify(data);
      this.$emit('input',data);
    },

    videothumb : function() {
      var data = {
        'code':this.code,
        'ratio':this.ratio
      };
      return data;
    },

    get_video_code_from_url : function(url) {
      var platform = 'youtube';
      var code = url;
      if ( (url.indexOf('www.')!==-1) || (url.indexOf('http')!==-1) ) {
        if (url.indexOf('vimeo.com')!==-1) {
          platform = 'vimeo';
        }
        // Get code
        var match = '';
        switch (platform) {
          case 'vimeo':
            match = url.match('/vimeo.com\/([0-9a-z_-]+)/i');
            break;
          case 'youtube':
          default:
            match = url.match(/v=([0-9a-z_-]+)/i);
            if ( match==null ) {
                match = url.match(/youtu\.be\/(CQ6szCNA4TA)/i);
            }
            break;
        }
        code = match[1];
      }
      return code;
    }

  },
}
</script>

<template>
  <div class="videopicker">
    <div class="video-code">
      <input class="form-control" name="videocode" :value="code" placeholder="Url or code from video" v-on:input="changeCode($event.target.value)" />
    </div>
    <div class="video-ratio">
      <select class="form-control" name="videoratio" :value="ratio" v-on:input="changeRatio($event.target.value)">
        <option disabled value="">Select ratio</option>
        <option v-for="option in ratios" :selected="(option==ratio)" :value="option">{{option}}</option>
      </select>
    </div>
    <flexy-video class="video-thumb" :video="videothumb()" />
  </div>
</template>

<style>
  .videopicker > * {
    float:left;
    margin-right:.5rem;
  }
  .videopicker > .video-code {
    width: 14rem;
  }
  .videopicker > .video-ratio {
    width: 8rem;
  }
  .videopicker > .video-thumb {
    height:2.35rem;
  }
  .videopicker > .video-thumb.ratio-21by9 {width:5.4833rem}
  .videopicker > .video-thumb.ratio-16by9 {width:4.1778rem}
  .videopicker > .video-thumb.ratio-4by3 {width:3.1333rem}
  .videopicker > .video-thumb.ratio-1by1 {width:2.35rem}
</style>
