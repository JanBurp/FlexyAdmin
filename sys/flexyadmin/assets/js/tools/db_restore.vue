<script>

import flexyState       from './../flexy-state.js'
import FlexySimpleForm  from '../components/form/flexy-simple-form.vue'

export default {
  name: 'dbRestoreTool',
  components: {FlexySimpleForm},
  
  data :function(){
    return {
      fields   : false,
      sql      : '',
      errors   : false,
      message  : false,
    }
  },

  created : function() {
    var self = this;
    var fields = {
      'file'    : { 'label':'File', 'type':'file' },
    };
    self.fields = Object.assign( {}, fields );
  },


  methods : {
    restore : function(event) {
      var self=this;
      var input = event.target;
      var reader = new FileReader();
      reader.onloadend = function(){
        if (reader.result) {
          return flexyState.api({
            method : 'POST',
            url    : 'tools/db_restore',
            data   : { 'sql' : reader.result},
        }).then(function(response){
          if (_.isUndefined(response.data) || response.data.data==false) {
            self.errors = Object.assign( {}, { 0: {'message':'Error'} } );
          }
          if (!_.isUndefined(response.data.data.errors) && response.data.data.errors.length>0) {
            self.errors = Object.assign( {}, response.data.data.errors );
          }
          if (!_.isUndefined(response.data.data.comments)) self.message = response.data.data.comments;
          if (!self.errors) self.message = '<b>Succes!!</b><br><br>' + self.message;
        });
        }
      };
      reader.readAsText(event.file[0]);
    },
  }
}
</script>

<template>
  <div class="flexy-tool flexy-tool-db">
    <div class="card">
      <h1 class="card-header">Restore backup</h1>
      <div class="card-body">
        <flexy-simple-form v-if="fields!==false" :fields="fields" @submit="restore($event)"></flexy-simple-form>
        <div v-if="errors!==false" class="messages">
          <div v-for="error in errors" class="alert alert-danger">{{error.message}}</div>
        </div>
        <div v-if="message!==false" class="alert alert-success messages" v-html="message"></div>
      </div>
    </div>
  </div>
</template>
