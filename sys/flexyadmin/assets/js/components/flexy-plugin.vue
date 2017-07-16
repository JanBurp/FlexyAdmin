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
    if (this.plugin !== this.currentPlugin) {
      this.loadPlugin(this.plugin);
    }
    this.currentPlugin = this.plugin;
  },


  data :function(){
    return {
      currentPlugin : '',
      name          : '',
      html          : '',
      list          : false,
      plugins       : [],
    }
  },

  methods : {

  	loadPlugin : function(plugin) {
      var self = this;

      return flexyState.api({
        url   : 'get_plugin?plugin='+plugin,
      }).then(function(response){
        console.log(response.data.data);
        self.name = response.data.data.title;
        self.html = response.data.data.html;
        self.list = _.isUndefined(response.data.data.plugin);
        if (self.list) self.plugins = response.data.data.plugins;
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
      <div class="card-block" v-html="html"></div>
    </template>

    <template v-if="list">
      <h1 class="card-header">{{name}}</h1>
      <div class="card-block">
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
