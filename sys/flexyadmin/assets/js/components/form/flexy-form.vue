<script>

import jdb              from '../../jdb-tools.js'

import flexyState       from '../../flexy-state.js'
import flexyButton      from '../flexy-button.vue'

import tab              from '../../vue-strap-src/components/Tab.vue'
import tabs             from '../../vue-strap-src/components/Tabs.vue'
import datepicker       from '../../vue-strap-src/Datepicker.vue'


export default {
  name: 'FlexyForm',
  components: {flexyButton,tab,tabs,datepicker},
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
        primary     : ['primary'],
        hidden      : ['hidden','primary','uri','order'],
        checkbox    : ['checkbox'],
        datepicker  : ['date'],
        select      : ['select','media'],
        textarea    : ['textarea'],
        wysiwyg     : ['wysiwyg'],
      };
      var defaultTypes = [];
      for(var type in types) {
        defaultTypes = defaultTypes.concat(types[type]);
      }
      types.default = defaultTypes;
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
      
      if (_.isUndefined(this.fields[field])) return false;
      
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
    
    isSelectedOption : function(field,value,option) {
      var selected = '';
      if (typeof(value)!=='object') {
        if (parseInt(value)===option) selected='selected';
      }
      else {
        for(var item in value) {
          var id = parseInt(value[item]['id']);
          if (id===option) selected='selected';
        }
      }
      return selected;
    },
    
    dateObject : function(value) {
      if (value==='0000-00-00') {
        var date  = new Date();
      }
      else {
        var year  = value.substr(0,4);
        var month = value.substr(5,2);
        var day   = value.substr(8,2);
        var date  = new Date(year,month,day);
      }
      return date;
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
      var data = _.clone(this.row);
      
      // Prepare data for ajax call
      for (var field in data) {
        if (field.indexOf('.abstract')>0) {
          delete(data[field]);
        }
        else {
          if (this.isType('checkbox',field)) {
            data[field] = (data[field]?1:0);
          }
          if (this.isType('select',field) && this.isMultiple(field)) {
            data[field] = [];
            for (var i = 0; i < this.row[field].length; i++) {
              data[field].push(this.row[field][i].id);
            }
          }
        }
      }
      
      console.log(data);
      
      return flexyState.api({
        url : 'row',
        'data': {
          'table'   : this.name,
          'where'   : this.row['id'],
          'data'    : data
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
      console.log('updateField',field,value);
      this.row[field] = value;
    },
    
    // update select (multiple)
    updateSelect : function( field, selected ) {
      if ( !this.isMultiple(field) ) {
        var value = selected[0].value;
      }
      else {
        var value = [];
        for (var i = 0; i < selected.length; i++) {
          value.push({'id':selected[i].value});
        }
      }
      this.updateField(field,value);
    },
    
    updateDate : function(field,value) {
      var stringValue = value.getFullYear() +'-'+ value.getMonth() +'-'+ value.getDate();
      console.log('updateDate',field,value,stringValue);
      this.updateField(field,stringValue);
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
          <template v-if="!isType('hidden',field)">
          
            <div class="form-group row" :class="validationClass(field)">
              <div v-if="validationErrors[field]" class="validation-error form-text text-danger">{{validationErrors[field]}}</div>
              <label class="col-xs-3 form-control-label" :for="field">{{fields[field]['name']}}</label>
              <div class="col-xs-9">

                <template v-if="isType('textarea',field)">
                  <!-- Textarea -->
                  <textarea class="form-control" :id="field" :name="field" :value="row[field]" v-on:input="updateField(field,$event.target.value)" placeholder="">
                </template>
              
                <template v-if="isType('wysiwyg',field)">
                  <!-- WYSIWYG -->
                  <tinymce :id="field" :options="tinymceOptions" @change="updateText" :content="row[field]"></tinymce>
                </template>

                <template v-if="isType('checkbox',field)">
                  <!-- Checkbox -->
                  <input class="form-check-input" type="checkbox" :id="field" :name="field" :checked="row[field]" @input="updateField(field,$event.target.checked)">
                </template>

                <template v-if="isType('datepicker',field)">
                  <!-- Datepicker -->
                  <datepicker :id="field" :name="field" :value="row[field]" format="yyyy-MM-dd" @input="updateDate(field,$event)"></datepicker>
                </template>
              
                <template v-if="isType('select',field)">
                  <!-- Select -->
                  <select class="form-control" :id="field" :name="field" :value="row[field]" v-on:input="updateSelect(field,$event.target.selectedOptions)" :multiple="isMultiple(field)">
                    <option v-for="option in options[field]['data']" :value="option.value" :selected="isSelectedOption(field,row[field],option.value)">{{option.name}}</option>
                  </select>
                </template>
              
                <template v-if="isType('default',field)">
                  <!-- Default -->
                  <input type="text" class="form-control" :id="field" :name="field" :value="row[field]" v-on:input="updateField(field,$event.target.value)" placeholder="">
                </template>

              </div>
            </div>
            
          </template>
        </template>
      </tab>
    </tabs>

  </div>
</div>
</template>
