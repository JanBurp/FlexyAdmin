<script>

import jdb              from './../jdb-tools.js'
import flexyState       from './../flexy-state.js'
import FlexySimpleForm  from '../components/form/flexy-simple-form.vue'

export default {
  name: 'Search',
  components: {FlexySimpleForm},
  
  data : function() {
    return {
      fields : {
        'search'    : { 'label':'Search',     'type':'input',   'value':'' },
        'replace'   : { 'label':'Replace',    'type':'input',   'value':'' },
        'regex'     : { 'label':'Regex',      'type':'checkbox','value':false },
        'fields'    : { 'label':'In Fields',  'type':'input',   'value':'str_title,txt_text' },
        'test'      : { 'label':'Test',       'type':'checkbox','value':true },
      },
      search : '',
      replace: '',
      result : false,
    }
  },

  methods : {

    submit : function(data) {
      var self = this;
      self.search = data.search;
      self.replace = data.replace;
      var url = jdb.serializeJSON(data);
     
      return flexyState.api({
        url : 'tools/search?'+url,
      }).then(function(response){
        self.result = response.data.data.result;
        if (response.data.data.found_fields!=='') {
          self.fields.fields.value = response.data.data.found_fields.join(',');
          self.fields = Object.assign({}, self.fields  );
        };
      });
    },

    highlight : function(item,highlight) {
      if (typeof(item)=='string')
        return item.replace(new RegExp(highlight,'g'),'<span class="text-danger">'+highlight+'</span>');
      return item;
    },

  },

}
</script>

<template>
  <div class="flexy-tool flexy-tool-search">
    <div class="card">
      <h1 class="card-header">Search &amp; Replace</h1>
      <div class="card-block">
        <flexy-simple-form :fields="fields" @submit="submit($event)" />
      </div>
    </div>

    <div v-if="result!==false" class="card">
      <h1 class="card-header">Result</h1>
      <div class="card-block tool-result">
        <table class="table table-bordered table-condensed">
          <thead>
            <tr>
              <th>#id</th>
              <th>Abstract</th>
              <th>Found</th>
              <th>Replaced</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in result">
              <td valign="top">{{item.table}}.{{item.field}}[{{item.primary_key}}]</td>
              <td valign="top">{{item.abstract}}</td>
              <td valign="top" v-html="highlight(item.value,search)"></td>
              <td valign="top" v-html="highlight(item.newvalue,replace)"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
  
</template>