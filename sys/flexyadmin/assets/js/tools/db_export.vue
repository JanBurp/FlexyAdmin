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
    }
  },

  created : function() {
    var self = this;
    return flexyState.api({
      url : 'tools/db_export_form',
    }).then(function(response){
      var tables = response.data.data.tables;
      self.filename = response.data.data.filename;
      var types = [
        { 'value':'complete',    'name':'Complete Export     (without Session data)' },
        { 'value':'all',         'name':'All Export          (without Session & Log data)' },
        { 'value':'data',        'name':'Data Only Export    (without Session,Logs & Config)' },
        { 'value':'select',      'name':'Select              (select tables)' },
      ];
      var fields = {
        'type'    : { 'label':'Wat',    'type':'select', 'options':types,  'value':'data' },
        'tables'  : { 'label':'Tables', 'type':'select', 'options':tables, 'multiple':true },
      };
      self.fields = Object.assign( {}, fields );
    });
  },


  methods : {

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
  }
  
}
</script>

<template>
  <div class="flexy-tool flexy-tool-db">
    <div class="card">
      <h1 class="card-header">Export Database</h1>
      <div v-if="fields!==false" class="card-body">
        <flexy-simple-form :fields="fields" @submit="export_db($event)"></flexy-simple-form>
      </div>
      <div v-if="sql!==''" class="card-body">
        <a :href="'data:text/plain;charset=utf-8,' + encodeURIComponent(sql)" :download="filename" class="btn btn-warning"><span class="fa fa-download"></span>Download Export</a>
      </div>
    </div>
  </div>
</template>
