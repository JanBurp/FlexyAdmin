<script>

// import jdb              from './../jdb-tools.js'
import flexyState       from './../flexy-state.js'
import FlexySimpleForm  from '../components/form/flexy-simple-form.vue'

export default {
  name: 'db',
  components: {FlexySimpleForm},
  props: ['action'],
  
  data :function(){
    return {
      fields   : {},
      filename : '',
      sql      : '',
      errors   : false,
      message  : false,
    }
  },


  created : function() {
    var self = this;

    switch (this.action) {


      case 'backup':
        return flexyState.api({
          url : 'tools/db_backup',
        }).then(function(response){
          self.filename = response.data.data.filename;
          self.sql      = response.data.data.sql;
        });
        break

      case 'restore':
        break;


    }

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
          if (!_.isUndefined(response.data.data.errors) && response.data.data.errors.length>0) {
            self.errors = Object.assign( {}, response.data.data.errors );
          }
          self.message = response.data.data.comments;
          if (!self.errors) {
            self.message = '<b>Succes!!</b><br><br>' + self.message;  
          }
        });
        }
      };
      reader.readAsText(input.files[0]);
    },

  }
  
}
</script>

<template>
  <div class="flexy-tool flexy-tool-db">

    <!-- BACKUP -->
    <div v-if="action=='backup'" class="card">
      <h1 class="card-header">Backup</h1>
      <div v-if="filename!==''" class="card-block">
        <a :href="'data:text/plain;charset=utf-8,' + encodeURIComponent(sql)" :download="filename" class="btn btn-warning"><span class="fa fa-download"></span>Download Backup</a>
      </div>
    </div>

    <!-- RESTORE -->
    <div v-if="action=='restore'" class="card">
      <h1 class="card-header">Restore backup</h1>
      <div class="card-block">
        <input type="file" id="restore_file" name="restore_file" @change="restore($event)" />
        <div v-if="errors!==false">
          <div v-for="error in errors" class="alert alert-danger">{{error.message}}</div>
        </div>
        <div v-if="message!==false" class="alert alert-success" v-html="message"></div>
      </div>
    </div>


  </div>
</template>