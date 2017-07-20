<script>

import flexyState       from './../flexy-state.js'
import FlexySimpleForm  from '../components/form/flexy-simple-form.vue'

export default {
  name: 'db',
  components: {FlexySimpleForm},
  
  data :function(){
    return {
      fields   : false,
      filename : '',
      sql      : '',
      errors   : false,
      message  : false,
    }
  },

  created : function() {
    var self = this;
    var fields = {
      'file'    : { 'label':'File', 'type':'file' },
      'sql'     : { 'label':'SQL',  'type':'textarea' },

    };
    self.fields = Object.assign( {}, fields );
  },


  methods : {

    import_db : function(event) {
      var self=this;
      // FILE
      if ( _.isUndefined(event.sql)) {
        var reader = new FileReader();
        reader.onloadend = function(){
          if (reader.result) {
            return self._import_db(reader.result);
          }
        };
        reader.readAsText(event.file[0]);
      }
      // SQL
      else {
        return self._import_db(event.sql);
      }
    },

    _import_db : function(sql) {
      var self=this;
      return flexyState.api({
        method : 'POST',
        url    : 'tools/db_import',
        data   : { 'sql' : sql},
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
    },

  }
}
</script>

<template>
  <div class="flexy-tool flexy-tool-db">
    <div class="card">
      <h1 class="card-header">Import SQL</h1>
      <div class="card-block">
        <flexy-simple-form v-if="fields!==false" :fields="fields" @submit="import_db($event)"></flexy-simple-form>
        <div v-if="errors!==false" class="messages">
          <div v-for="error in errors" class="alert alert-danger">{{error.message}}</div>
        </div>
        <div v-if="message!==false" class="alert alert-success messages" v-html="message"></div>
      </div>
    </div>
  </div>
</template>
