<script>
import flexyState       from '../../flexy-state.js'

import tab              from '../../vue-strap-src/components/Tab.vue'
import tabs             from '../../vue-strap-src/components/Tabs.vue'
// import tabGroup         from '../../vue-strap-src/components/TabGroup.vue'


export default {
  name: 'FlexyForm',
  components: {tab,tabs},
  props:{
    'title':String,
    'name':String,
    'primary':Number,
    'fields':[Object,Array],
    'fieldsets':[Object,Array],
    'data':[Object,Array],
    'options':[Object,Array],
  },
  
  computed : {
    fieldTypes : function() {
      var types = {
        primary   : ['primary'],
        hidden    : ['hidden'],
        checkbox  : ['checkbox'],
        select    : ['select','media'],
        textarea  : ['textarea','wysiwyg'],
      };
      types.default = [].concat( types.primary, types.hidden, types.checkbox, types.select, types.textarea );
      return types;
    }
  },
  
  // Copy of props.data
  data : function() {
    return {
      row : {},
      validationErrors : {}
    }
  },
  // Make copy of props.data
  created : function() {
    this.row = this.data;
  },
  
  methods:{
    
    isType : function( type,field ) {
      if (type==='default') {
        return this.fieldTypes['default'].indexOf(this.fields[field].schema['form-type']) === -1;
      }
      return this.fieldTypes[type].indexOf(this.fields[field].schema['form-type']) >= 0;
    },
    
    isMultiple : function( field ) {
      var multiple = false;
      if (this.options[field].multiple) multiple='multiple';
      if (flexyState.debug) console.log('isMultiple',field,multiple);
      return multiple;
    },
    
    validationClass : function(field) {
      var validation='';
      if (this.validationErrors[field]) validation = 'has-danger';
      return validation;
    },
    
    cancel : function() {
      var url = 'admin/show/grid/' + this.name;
      window.location.assign( url );
    },
    
    save : function() {
      this.postForm();
    },
    
    submit : function() {
      var name = this.name;
      this.postForm().then(function (response) {
        if (!response.error) {
          var url = 'admin/show/grid/' + name;
          window.location.assign( url );
        }
      })
    },
    
    postForm : function() {
      var self=this;
      return this.api({
        url : 'row',
        'data': {
          'table'   : this.name,
          'where'   : this.primary,
          'data'    : this.row
        },
      }).then(function(response){
        if (!response.error) {
          if (response.data.info.validation!==false) {
            flexyState.addMessage('Item saved');
          }
          else {
            flexyState.addMessage('Niet alle velden zijn goed ingevuld!','danger');
            self.validationErrors = response.data.info.validation_errors;
          }
        }
        else {
          flexyState.addMessage('<b>ERROR</b> while saving item!','danger');
        }
        return response;
      });
    },
    
    updateField : function( field, value ) {
      this.row[field] = value;
    },
    
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
          
          <template v-if="isType('primary',field)">
            <input type="hidden" :value="row[field]">
          </template>
          
          <div class="form-group row" :class="validationClass(field)" v-if="isType('textarea',field)">
            <div v-if="validationErrors[field]" class="validation-error form-text text-danger">{{validationErrors[field]}}</div>
            <label class="col-xs-2 form-control-label" :for="field">{{fields[field]['name']}}</label>
            <div class="col-xs-10">
              <textarea class="form-control" :id="field" :name="field" :value="row[field]" v-on:input="updateField(field,$event.target.value)" placeholder="">
            </div>
          </div>

          <div class="form-group row" :class="validationClass(field)" v-if="isType('checkbox',field)">
            <div v-if="validationErrors[field]" class="validation-error form-text text-danger">{{validationErrors[field]}}</div>
            <label class="col-xs-2 form-control-label" :for="field">{{fields[field]['name']}}</label>
            <div class="col-xs-10">
              <input class="form-check-input" type="checkbox" :id="field" :name="field" :value="row[field]" v-on:input="updateField(field,$event.target.value)">
            </div>
          </div>
          
          <div class="form-group row" :class="validationClass(field)" v-if="isType('select',field)">
            <div v-if="validationErrors[field]" class="validation-error form-text text-danger">{{validationErrors[field]}}</div>
            <label class="col-xs-2 form-control-label" :for="field">{{fields[field]['name']}}</label>
            <div class="col-xs-10">
              <select class="form-control" :id="field" :name="field" :value="row[field]" v-on:input="updateField(field,$event.target.value)" :multiple="isMultiple(field)">
                <option v-for="option in options[field]['data']" :value="option.value" :selected="option.value==row[field]">{{option.name}}</option>
              </select>
            </div>
          </div>

          <div class="form-group row" :class="validationClass(field)" v-if="isType('default',field)">
            <div v-if="validationErrors[field]" class="validation-error form-text text-danger">{{validationErrors[field]}}</div>
            <label class="col-xs-2 form-control-label" :for="field">{{fields[field]['name']}}</label>
            <div class="col-xs-10"><input type="text" class="form-control" :id="field" :name="field" :value="row[field]" v-on:input="updateField(field,$event.target.value)" placeholder=""></div>
          </div>
          
        </template>
      </tab>
    </tabs>

  </div>
</div>
</template>

<style>
  .form .form-group {min-height:2.35rem;}
  .form-control-label {text-transform:uppercase;font-weight:bold;padding-top:.35rem;}
  textarea {min-height:10rem;max-height:20rem;}
  .form-check-input {margin-left:0;margin-top:.75rem;}
  .validation-error {padding:.25rem 1rem;font-weight:bold;}
</style>
