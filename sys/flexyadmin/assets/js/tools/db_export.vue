<script>

import flexyState       from './../flexy-state.js'
import FlexySimpleForm  from '../components/form/flexy-simple-form.vue'

export default {
  name: 'db',
  components: {FlexySimpleForm},
  
  data :function(){
    return {
      fields   : false,
      href     : '',
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
        { 'value':'complete',    'name':'Complete - without session data' },
        { 'value':'all',         'name':'All - without log_ and session tables' },
        { 'value':'data',        'name':'Data Only - without log_ and cfg_ tables' },
        { 'value':'select',      'name':'Select - select tables' },
      ];
      var fields = {
        'type'    : { 'label':'Wat',    'type':'select', 'options':types,  'value':'all' },
        'tables'  : { 'label':'Tables', 'type':'select', 'options':tables, 'multiple':true, 'show':{field:'type',value:'select'} },
        'file'    : { 'label':'File',   'type':'select', 'value':'sql', 'options': [
          { 'value':'sql', 'name':'.sql' },
          { 'value':'zip', 'name':'.zip' },
        ]},
        'hash'    : { 'label':'Hash', 'type':'checkbox' },
      };
      self.fields = Object.assign( {}, fields );
    });
  },


  methods : {

    changed : function(event) {
      var type = 'all';
      var file = 'zip';
      var hash = 0;
      var tables = [];
      if (!_.isUndefined(event)) {
        type = event.type;
        file = event.file;
        hash = event.hash;
        if (type=='select' && !_.isUndefined(event.tables)) tables = event.tables;
      }
      this.href = "_admin/load/plugin/db/export/"+type+'/'+file+'/'+hash+'/'+tables.join('/');
      return this.href;
    }

  }
  
}
</script>

<template>
  <div class="flexy-tool flexy-tool-db">
    <div class="card">
      <h1 class="card-header">Export Database</h1>
      <div v-if="fields!==false" class="card-body">
        <flexy-simple-form :fields="fields" @changed="changed($event)" :buttons="{}"></flexy-simple-form>
      </div>
      <div v-if="href!==''" class="card-body">
        <a :href="href" class="btn btn-warning"><span class="fa fa-download"></span>Download Export</a>
      </div>
    </div>
  </div>
</template>
