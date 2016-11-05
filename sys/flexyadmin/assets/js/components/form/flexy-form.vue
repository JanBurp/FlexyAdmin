<script>
import tab              from '../../vue-strap-src/components/Tab.vue'
import tabs             from '../../vue-strap-src/components/Tabs.vue'
import tabGroup         from '../../vue-strap-src/components/TabGroup.vue'

export default {
  name: 'FlexyForm',
  components: {tab,tabs,tabGroup},
  props:{
    'title':String,
    'name':String,
    'id':Number,
    'fields':[Object,Array],
    'fieldsets':[Object,Array],
    'data':[Object,Array],
  },
  
  computed:{
  },
  
  data : function() {
    return {
    }
  },
  
  methods:{

  }
}
</script>

<template>
<div class="card form">
  <div class="card-header">
    <h1>{{title}}</h1>
    <div class="btn-group" role="group">
      <button type="button" class="btn btn-sm btn-danger">Annuleer<span class="fa fa-close"></span></button>
      <button type="button" class="btn btn-sm btn-warning">Bewaar<span class="fa fa-save"></span></button>
      <button type="button" class="btn btn-sm btn-info">Invoeren<span class="fa fa-check"></span></button>
    </div>
  </div>

  <div class="card-block">
    
    <tabs navStyle="tabs">
      <tab v-for="(fieldset,name) in fieldsets" :header="name">
        <div v-for="field in fieldset" class="form-group row">
          <label class="col-xs-2 col-form-label" :for="field">{{fields[field]['name']}}</label>
          <div class="col-xs-10"><input type="text" class="form-control" :id="field" :name="field" :value="data[field]" placeholder=""></div>
        </div>
      </tab>
    </tabs>

  </div>
</div>
</template>

<style>
</style>
