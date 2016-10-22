
Vue.component('flexy-blocks', {

  props:['text','href'],

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
      <span v-once v-for="char in chars" class="flexy-block btn btn-outline-primary btn-sm" v-bind:class="char.css">{{char.char}}</span>\
    </a>',

});