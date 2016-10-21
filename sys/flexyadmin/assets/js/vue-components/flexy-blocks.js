
Vue.component('flexy-blocks', {

  props:['text','href'],

  data : function(){
    return {
      cssClass : 'flexy-block btn btn-outline-primary btn-sm',
    }
  },

  computed: {
    chars : function() {
      var text = this.text.split('');
      var chars=[];
      for (var i = 0; i < text.length; i++) {
        chars[i] = {
          char : text[i],
          css  : 'char-' + (text[i]===' '?'space':text[i]),
        }
      }
      return chars;
    },
  },

  template: '\
    <a v-bind:href="href">\
      <span v-for="char in chars" v-bind:class="[ cssClass, char.css ]">{{char.char}}</span>\
    </a>',

});