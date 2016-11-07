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
    'primary':Number,
    'fields':[Object,Array],
    'fieldsets':[Object,Array],
    'data':[Object,Array],
  },
  
  mounted : function() {
    console.log(this.fields);
  },
  
  methods:{
    
    cancel : function() {
      var url = 'admin/show/grid/' + this.name;
      window.location.assign( url );
    },
    
    save : function() {
      this.postForm().then(function (response) {
        console.log(response);
      })
      .catch(function (error) {
        console.log('error',error);
      });
    },
    
    submit : function() {
      var name = this.name;
      this.postForm().then(function (response) {
        console.log(response);
        var url = 'admin/show/grid/' + name;
        window.location.assign( url );
      })
      .catch(function (error) {
        console.log('error',error);
      });
    },
    
    postForm : function() {
      return this.api({
        url : 'row',
        data: {
          'table'   : this.name,
          'where'   : this.primary,
          'data'    : this.data
        },
      });
    },
    
    updateField : function( field, value ) {
      this.data[field] = value;
    }
    
  }
  
}
</script>

<template>
<div class="card form">
  <div class="card-header">
    <h1>{{title}}</h1>
    <div class="btn-group" role="group">
      <button type="button" v-on:click="cancel()" class="btn btn-sm btn-danger">Annuleer<span class="fa fa-close"></span></button>
      <button type="button" v-on:click="save()"   class="btn btn-sm btn-warning">Bewaar<span class="fa fa-save"></span></button>
      <button type="button" v-on:click="submit()" class="btn btn-sm btn-info">Invoeren<span class="fa fa-check"></span></button>
    </div>
  </div>

  <div class="card-block">
    
    <tabs navStyle="tabs">
      <tab v-for="(fieldset,name) in fieldsets" :header="name">
        <template v-for="field in fieldset">
          
          <template v-if="fields[field].schema['form-type']==='primary'">
            <input type="hidden" :value="data[field]">
          </template>
          
          <div class="form-group row" v-if="fields[field].schema['form-type']==='textarea' || fields[field].schema['form-type']==='wysiwyg'">
            <label class="col-xs-2 col-form-label" :for="field">{{fields[field]['name']}}</label>
            <div class="col-xs-10">
              <textarea class="form-control" :id="field" :name="field" :value="data[field]" v-on:input="updateField(field,$event.target.value)" placeholder="">
            </div>
          </div>

          <div class="form-group row" v-if="fields[field].schema['form-type']==='text' || fields[field].schema['form-type']==='number' || fields[field].schema['form-type']==='select'">
            <label class="col-xs-2 col-form-label" :for="field">{{fields[field]['name']}}</label>
            <div class="col-xs-10"><input type="text" class="form-control" :id="field" :name="field" :value="data[field]" v-on:input="updateField(field,$event.target.value)" placeholder=""></div>
          </div>

        </template>
      </tab>
    </tabs>

  </div>
</div>
</template>

<style>
  .col-form-label {text-transform:uppercase;font-weight:bold;padding-bottom:0;margin-bottom:0;}
  textarea {min-height:10rem;max-height:20rem;}
</style>
