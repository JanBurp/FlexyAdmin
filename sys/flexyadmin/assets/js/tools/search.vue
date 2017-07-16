<script>
import FlexySimpleForm  from '../components/form/flexy-simple-form.vue'

export default {
  name: 'Search',
  components: {FlexySimpleForm},
  
  data : function() {
    return {
      fields : {
        'search'    : { 'label':'Search',     'type':'input',   'value':'test' },
        'replace'   : { 'label':'Replace',    'type':'input' },
        'regex'     : { 'label':'Regex',      'type':'checkbox' },
        'fields'    : { 'label':'In Fields',  'type':'input' },
        'test'      : { 'label':'Test',       'type':'checkbox', 'value':true },
      },
    }
  },

  methods : {

    submit : function(data) {
      console.log('submit search',data);
    },

  },

}
</script>

<template>
  <div class="flexy-tool flexy-tool-search">
    <div class="card">
      <h1 class="card-header">Search &amp; Replace</h1>
      <div class="card-block">
        <flexy-simple-form :fields="fields" @submit="submit($event)"/>
      </div>
    </div>

  </div>
  
</template>
