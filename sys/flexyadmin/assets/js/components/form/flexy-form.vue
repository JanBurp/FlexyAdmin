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
      validationErrors : {},
      isSaving : false,
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
      if (!this.isSaving) {
        var url = 'admin/show/grid/' + this.name;
        window.location.assign( url );
      }
    },
    
    save : function() {
      if (!this.isSaving) {
        this.postForm();
      }
    },
    
    submit : function() {
      if (!this.isSaving) {
        var name = this.name;
        this.postForm().then(function (response) {
          if (!response.error) {
            var url = 'admin/show/grid/' + name;
            window.location.assign( url );
          }
        })
      }
    },
    
    postForm : function() {
      var self=this;
      self.isSaving = true;
      return this.api({
        url : 'row',
        'data': {
          'table'   : this.name,
          'where'   : this.row['id'],
          'data'    : this.row
        },
      }).then(function(response){
        self.isSaving = false;
        if (!response.error) {
          if ( _.isUndefined(response.data.info) || response.data.info.validation!==false) {
            flexyState.addMessage('Item saved');
            if (self.isNewItem()) {
              self.row['id'] = response.data.data.id;
            }
          }
          else {
            flexyState.addMessage( self.$lang.form_validation_error, 'danger');
            if ( !_.isUndefined(response.data.info) ) self.validationErrors = response.data.info.validation_errors;
          }
        }
        else {
          flexyState.addMessage( self.$lang.form_save_error, 'danger');
        }
        return response;
      });
    },
    
    isNewItem : function() {
      return this.row['id'] === -1;
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
      <button type="button" v-on:click="cancel()" :disabled="isSaving" class="btn btn-sm btn-danger">{{$lang.cancel}}<span class="fa fa-close"></span></button>
      <button type="button" v-on:click="save()"   :disabled="isSaving" class="btn btn-sm btn-warning">{{$lang.save}}<span class="fa fa-save"></span></button>
      <button type="button" v-on:click="submit()" :disabled="isSaving" class="btn btn-sm btn-info">{{$lang.submit}}<span class="fa fa-check"></span></button>
    </div>
  </div>

  <div class="card-block">
    
    <tabs navStyle="tabs">
      <tab v-for="(fieldset,name) in fieldsets" :header="name">
        <template v-for="field in fieldset">
          
          <template v-if="isType('primary',field)">
            <!-- Primary -->
            <input type="hidden" :value="row[field]">
          </template>
          
          <div class="form-group row" :class="validationClass(field)" v-if="isType('textarea',field)">
            <!-- Textarea -->
            <div v-if="validationErrors[field]" class="validation-error form-text text-danger">{{validationErrors[field]}}</div>
            <label class="col-xs-3 form-control-label" :for="field">{{fields[field]['name']}}</label>
            <div class="col-xs-9">
              <textarea class="form-control" :id="field" :name="field" :value="row[field]" v-on:input="updateField(field,$event.target.value)" placeholder="">
            </div>
          </div>

          <div class="form-group row" :class="validationClass(field)" v-if="isType('checkbox',field)">
            <!-- Checkbox -->
            <div v-if="validationErrors[field]" class="validation-error form-text text-danger">{{validationErrors[field]}}</div>
            <label class="col-xs-3 form-control-label" :for="field">{{fields[field]['name']}}</label>
            <div class="col-xs-9">
              <input class="form-check-input" type="checkbox" :id="field" :name="field" v-model="row[field]" v-on:input="updateField(field,$event.target.value)">
            </div>
          </div>
          
          <div class="form-group row" :class="validationClass(field)" v-if="isType('select',field)">
            <!-- Select -->
            <div v-if="validationErrors[field]" class="validation-error form-text text-danger">{{validationErrors[field]}}</div>
            <label class="col-xs-3 form-control-label" :for="field">{{fields[field]['name']}}</label>
            <div class="col-xs-9">
              <select class="form-control" :id="field" :name="field" :value="row[field]" v-on:input="updateField(field,$event.target.value)" :multiple="isMultiple(field)">
                <option v-for="option in options[field]['data']" :value="option.value" :selected="option.value==row[field]">{{option.name}}</option>
              </select>
            </div>
          </div>

          <div class="form-group row" :class="validationClass(field)" v-if="isType('default',field)">
            <!-- Default -->
            <div v-if="validationErrors[field]" class="validation-error form-text text-danger">{{validationErrors[field]}}</div>
            <label class="col-xs-3 form-control-label" :for="field">{{fields[field]['name']}}</label>
            <div class="col-xs-9"><input type="text" class="form-control" :id="field" :name="field" :value="row[field]" v-on:input="updateField(field,$event.target.value)" placeholder=""></div>
          </div>
          
        </template>
      </tab>
    </tabs>

  </div>
</div>
</template>

<style>
  .form .form-group {min-height:2.35rem;}
  .form-control-label {text-transform:uppercase;font-weight:bold;padding-top:.5rem;padding-bottom:0;margin-bottom:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
  textarea {min-height:10rem;max-height:20rem;}
  .form-check-input {margin-left:0;margin-top:.75rem;}
  .validation-error {padding:.25rem 1rem;font-weight:bold;}
</style>
