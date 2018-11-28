<script>

import jdb              from './../jdb-tools.js'
import flexyState       from './../flexy-state.js'
import FlexySimpleForm  from '../components/form/flexy-simple-form.vue'

export default {
  name: 'Fill',
  components: {FlexySimpleForm},
  
  data :function(){
    return {
      fields : {
        'aantal'    : { 'label':'Aantal nieuwe rijen', 'type':'input' },
        'table'     : { 'label':'Tabel',               'type':'input', 'value' : 'tbl_blog' },
        'fields'    : { 'label':'Velden',              'type':'input', 'value' : 'str_title,dat_date,media_picture,medias_pictures,txt_text' },
        'where'     : { 'label':'WHERE',               'type':'input' },
        'value'     : { 'label':'Waarde ( {RANDOM} )',              'type':'input',  'value':'{RANDOM}' },
        'test'      : { 'label':'Test',                'type':'checkbox','value':true },
      },
      result        : false,
    }
  },

  methods : {

    submit : function(data) {
      var self = this;
      var url = jdb.serializeJSON(data);
     
      return flexyState.api({
        url : 'tools/fill?'+url,
      }).then(function(response){
        self.result = response.data.data.result;
      });
    },

    result_fields : function() {
      return this.fields.fields.value.split(',');
    },

  }
  
}
</script>

<template>
  <div class="flexy-tool flexy-tool-fill">
    <div class="card">
      <h1 class="card-header">Auto Fill</h1>
      <div class="card-body">
        <flexy-simple-form :fields="fields" @submit="submit($event)"/>
      </div>
    </div>
    <div v-if="result!==false" class="card">
      <h1 class="card-header">Result</h1>
      <div class="card-body tool-result">
        <table class="table table-bordered table-condensed">
          <thead>
            <tr>
              <th>#id</th>
              <th v-for="field in result_fields()">{{field}}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in result">
              <td valign="top">{{item.id}}</td>
              <td valign="top" v-for="field in result_fields()" v-html="item[field]"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
</template>