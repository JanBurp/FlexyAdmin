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
      fields   : false,
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
        var fields = {
          'file'    : { 'label':'File', 'type':'file' },
        };
        self.fields = Object.assign( {}, fields );
        break;


      case 'export':
        return flexyState.api({
          url : 'tools/db_export_form',
        }).then(function(response){
          var tables = response.data.data.tables;
          self.filename = response.data.data.filename;
          var types = [
            { 'value':'complete',    'title':'Complete Export     (without Session data)' },
            { 'value':'all',         'title':'All Export          (without Session & Log data)' },
            { 'value':'data',        'title':'Data Only Export    (without Session,Logs & Config)' },
            { 'value':'select',      'title':'Select              (select tables)' },
          ];
          var fields = {
            'type'    : { 'label':'Wat',    'type':'select', 'options':types,  'value':'data' },
            'tables'  : { 'label':'Tables', 'type':'select', 'options':tables, 'multiple':true },
          };
          self.fields = Object.assign( {}, fields );
        });
        break;

      case 'import':
        var fields = {
          'file'    : { 'label':'File', 'type':'file' },
          'sql'     : { 'label':'SQL',  'type':'textarea' },

        };
        self.fields = Object.assign( {}, fields );
        break;


    }

  },


  methods : {

    // RESTORE
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


    // EXPORT
    export_db : function(event) {
      var self=this;
      self.filename = '';
      self.sql      = '';
      return flexyState.api({
        method : 'POST',
        url    : 'tools/db_export',
        data   : {
          'export_type'  : event.type,
          'tables'       : event.tables,
        },
      }).then(function(response){
        self.filename = response.data.data.filename;
        self.sql      = response.data.data.sql;
      });
    },

    // IMPORT
    import_db : function(event) {
      var self=this;
      console.log('import_db',event);
      // FILE
      if (event.sql=='') {
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
        <flexy-simple-form v-if="fields!==false" :fields="fields" @submit="restore($event)"></flexy-simple-form>
        <div v-if="errors!==false">
          <div v-for="error in errors" class="alert alert-danger">{{error.message}}</div>
        </div>
        <div v-if="message!==false" class="alert alert-success" v-html="message"></div>
      </div>
    </div>

    <!-- EXPORT -->
    <div v-if="action=='export'" class="card">
      <h1 class="card-header">Export Database</h1>
      <div v-if="fields!==false" class="card-block">
        <flexy-simple-form :fields="fields" @submit="export_db($event)"></flexy-simple-form>
      </div>
      <div v-if="sql!==''" class="card-block">
        <a :href="'data:text/plain;charset=utf-8,' + encodeURIComponent(sql)" :download="filename" class="btn btn-warning"><span class="fa fa-download"></span>Download Export</a>
      </div>
    </div>

    <!-- IMPORT -->
    <div v-if="action=='import'" class="card">
      <h1 class="card-header">Import SQL</h1>
      <div class="card-block">
        <flexy-simple-form v-if="fields!==false" :fields="fields" @submit="import_db($event)"></flexy-simple-form>
        <div v-if="errors!==false">
          <div v-for="error in errors" class="alert alert-danger">{{error.message}}</div>
        </div>
        <div v-if="message!==false" class="alert alert-success" v-html="message"></div>
      </div>
    </div>



  </div>
</template>