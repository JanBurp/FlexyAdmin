<script>
import flexyState       from '../flexy-state.js'

export default {
  name: 'plugin',
  props:{
  	'plugin': {
      type: String,
      default:'',
    },
  },

  created : function() {
    this.loadPlugin(this.plugin);
  },
  
  beforeUpdate : function() {
    this.loadPlugin(this.plugin);
  },


  data :function(){
    return {
      currentPlugin : '[index]',
      name          : '',
      html          : '',
      list          : false,
      plugins       : [],
    }
  },

  methods : {

  	loadPlugin : function(plugin) {
      if (this.currentPlugin==plugin || this.currentPlugin=='') return;
      var self = this;

      return flexyState.api({
        url   : 'get_plugin?plugin='+plugin,
      }).then(function(response){
        self.name = response.data.data.title;
        self.html = response.data.data.html;
        self.list = _.isUndefined(response.data.data.plugin);
        if (self.list) self.plugins = response.data.data.plugins;
        self.currentPlugin = plugin;
        return response;
      });


  	},

  },

}
</script>

<template>
  <div class="card flexy-plugin" :class="'plugin_'+plugin">
    
    <template v-if="!list">
      <h1 class="card-header">{{name}}</h1>
      <div class="card-body" v-html="html"></div>
    </template>

    <template v-if="list">
      <h1 class="card-header">{{name}}</h1>
      <div class="card-body">
        <table class="table table-hover table-sm">
          <tr v-for="plugin in plugins">
            <td><router-link :to="plugin.uri">{{plugin.name}}</router-link></td>
            <td>{{plugin.doc.short}}</td>
          </tr>
        </table>
      </div>
   </template>
  </div>
</template>
