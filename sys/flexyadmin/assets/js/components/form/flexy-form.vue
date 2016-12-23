<script>
import flexyState       from '../../flexy-state.js'
import flexyButton      from '../flexy-button.vue'

import tab              from '../../vue-strap-src/components/Tab.vue'
import tabs             from '../../vue-strap-src/components/Tabs.vue'


export default {
  name: 'FlexyForm',
  components: {flexyButton,tab,tabs},
  props:{
    'title':String,
    'name':String,
    'path':String,
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
        textarea  : ['textarea'],
        wysiwyg   : ['wysiwyg'],
      };
      types.default = [].concat( types.primary, types.hidden, types.checkbox, types.select, types.textarea, types.wysiwyg );
      return types;
    },
  },
  
  // Copy of props.data
  data : function() {
    return {
      row : {},
      validationErrors : {},
      isSaving : false,
      tinymceOptions : {},
    }
  },
  // Make copy of props.data
  created : function() {
    this.row = this.data;
    this.tinymceOptions = JSON.parse(_flexy.tinymceOptions);
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
      var self=this;
      if (!this.isSaving) {
        window.location.assign( self.returnUrl() );
      }
    },
    
    submit : function() {
      var self=this;
      if (!this.isSaving) {
        this.postForm().then(function (response) {
          if (!response.error) {
            window.location.assign( self.returnUrl() );
          }
        })
      }
    },
    
    returnUrl : function() {
      var url = 'admin/show/grid/' + this.name;
      if (this.path && this.path!=='false')  url='admin/show/media/' + this.path;
      console.log(this.path,url);
      return url;
    },
    
    save : function() {
      if (!this.isSaving) {
        this.postForm();
      }
    },
    
    postForm : function() {
      var self=this;
      self.isSaving = true;
      var data = this.row;
      for (var field in data) {
        if (this.isType('checkbox',field)) {
          data[field] = (data[field]?1:0);
        }
      }
      
      return flexyState.api({
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

    // TinyMCE changed
    updateText : function(editor,content) {
      this.updateField(editor.id,content);
    }
    
  }
  
}
</script>

<template>
<div class="card form">
  <div class="card-header">
    <h1>{{title}}</h1>
    <div class="btn-group" role="group">
      <flexy-button @click.native="cancel()" icon="close" :text="$lang.cancel" :disabled="isSaving" class="btn-danger"/>
      <flexy-button @click.native="save()"   icon="save" :text="$lang.save" :disabled="isSaving" class="btn-warning"/>
      <flexy-button @click.native="submit()" icon="check" :text="$lang.submit" :disabled="isSaving" class="btn-info"/>
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

          <div class="form-group row" :class="validationClass(field)" v-if="isType('wysiwyg',field)">
            <!-- wysiwyg : tinyMCE -->
            <div v-if="validationErrors[field]" class="validation-error form-text text-danger">{{validationErrors[field]}}</div>
            <label class="col-xs-3 form-control-label" :for="field">{{fields[field]['name']}}</label>
            <div class="col-xs-9">
              <tinymce :id="field" :options="tinymceOptions" @change="updateText" :content="row[field]"></tinymce>
            </div>
          </div>


          <div class="form-group row" :class="validationClass(field)" v-if="isType('checkbox',field)">
            <!-- Checkbox -->
            <div v-if="validationErrors[field]" class="validation-error form-text text-danger">{{validationErrors[field]}}</div>
            <label class="col-xs-3 form-control-label" :for="field">{{fields[field]['name']}}</label>
            <div class="col-xs-9">
              <input class="form-check-input" type="checkbox" :id="field" :name="field" :checked="row[field]" @input="updateField(field,$event.target.checked)">
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
