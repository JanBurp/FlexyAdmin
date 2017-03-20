<script>

import jdb              from '../../jdb-tools.js'

import flexyState       from '../../flexy-state.js'
import flexyButton      from '../flexy-button.vue'

import flexyThumb       from '../flexy-thumb.vue'

import timepicker       from './timepicker.vue'
import datetimepicker   from './datetimepicker.vue'
import colorpicker      from './colorpicker.vue'
import mediapicker      from './mediapicker.vue'

import tab              from '../../vue-strap-src/components/Tab.vue'
import tabs             from '../../vue-strap-src/components/Tabs.vue'
import vselect           from '../../vue-strap-src/components/Select.vue'
import datepicker       from '../../vue-strap-src/Datepicker.vue'

export default {
  name: 'FlexyForm',
  components: {flexyButton,flexyThumb,timepicker,datetimepicker,colorpicker,mediapicker,tab,tabs,datepicker,vselect},
  props:{
    'title'   :String,
    'name'    :String,
    'primary' :[Number,String],
    'formtype':{
      type:String,
      default:'normal', // normal|single|subform
    },
  },
    
  
  computed : {
    fieldTypes : function() {
      var types = {
        primary           : ['primary'],
        hidden            : ['hidden','primary','uri','order'],
        checkbox          : ['checkbox'],
        datepicker        : ['date'],
        timepicker        : ['time'],
        datetimepicker    : ['datetime'],
        colorpicker       : ['color','rgb'],
        mediapicker       : ['media','medias'],
        select            : ['select'],
        radio             : ['radio'],
        textarea          : ['textarea'],
        wysiwyg           : ['wysiwyg'],
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
      fields : {},
      fieldsets: {},
      validationErrors : {},
      isSaving : false,
      insertForm : {},
    }
  },
  
  created : function() {
    this.reloadForm();
  },
  
  methods:{
    
    reloadForm : function(apiParts) {
      var self = this;
      return flexyState.api({
        url       : self.apiUrl(apiParts),
      })
      .then(function(response){
        if (!_.isUndefined(response.data)) {
          if (response.data.success) {
            // Zijn er settings meegekomen?
            if ( !_.isUndefined(response.data.settings) ) {
              self.fields = response.data.settings.form_set.field_info;
              self.fieldsets = response.data.settings.form_set.fieldsets;
            }
            // Data en die aanvullen met data
            self.row = response.data.data;
          }
        }
        // TinyMCE
        self.createWysiwyg();
        return response;
      });
    },
    
    createWysiwyg: function() {
      var self=this;
      var init = _.extend(_flexy.tinymceOptions,{
        setup : function(ed){
          ed.on('NodeChange', function(e){ self.updateText(ed); })
          ed.on('keyup', function(e){ self.updateText(ed); });
        }
      });
      tinyMCE.remove();

      // Wait just a bit...
      var timer = window.setInterval(function(){
        tinymce.init(init);
        var exists = document.querySelector('.mce-tinymce');
        if ( !_.isUndefined(exists) && exists!==null ) {
          clearInterval(timer)
        };
      }, 10 );
    },
    
    apiUrl : function(parts) {
      parts = _.extend( this.apiParts, parts );
      this.apiParts = parts;
      var url = 'row?table='+this.name + '&where='+this.primary + '&as_form=true&settings=form_set';
      return url;
    },
    
    label : function(field) {
      if (_.isUndefined(this.fields[field])) return field;
      return this.fields[field].label;
    },
    
    tabsClass : function() {
      if (Object.keys(this.fieldsets).length<2) return 'single-tab';
      return '';
    },
        
    isType : function( type,field ) {
      if (_.isUndefined(this.fields[field])) return false;
      if (type==='default') {
        return this.fieldTypes['default'].indexOf(this.fields[field]['type']) === -1;
      }
      return this.fieldTypes[type].indexOf(this.fields[field]['type']) >= 0;
    },
    
    isMultiple : function( field ) {
      var multiple = false;
      if (_.isUndefined(this.fields[field])) return false;
      if (this.fields[field]._options.multiple) multiple='multiple';
      if (flexyState.debug) console.log('isMultiple',field,multiple);
      return multiple;
    },
    
    isSelectedOption : function(field,value,option) {
      var selected = '';
      if (typeof(value)!=='object') {
        if (parseInt(value)===option || value===option) selected='selected';
      }
      else {
        for(var item in value) {
          var id = parseInt(value[item]['id']);
          if (id===option) selected='selected';
        }
      }
      return selected;
    },
    
    selectOption(field,option) {
      this.row[field] = option;
    },
    
    selectItem : function (value) {
      if (!value) return '';
      value = value.toString();
      value = value.replace(/\|/g,' | ').replace(/^\|/,'').replace(/\|$/,'');
      return value;
    },
    
    selectValue: function(field) {
      var value = this.row[field];
      if ( this.isMultiple(field) ) {
        value = [];
        var row = this.row[field];
        for (var i = 0; i < row.length; i++) {
          value.push( row[i].id );
        }
      }
      return value;
    },
    
    hasInsertRights : function(field) {
      if ( _.isUndefined(this.fields[field]) ) return false;
      if ( _.isUndefined(this.fields[field]._options) ) return false;
      if ( _.isUndefined(this.fields[field]._options.insert_rights) ) return false;
      var rights = this.fields[field]._options.insert_rights;
      return (rights===true || rights>=2);
    },
    
    // Pas kleur van optie aan als het een kleurenveld is
    selectStyle : function(field,option) {
      var style = '';
      if (field.substr(0,4)==='rgb_') {
        style="background-color:"+option +';color:'+jdb.complementColor(option)+';';
      }
      return style;
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
      var validationClass='';
      if ( this.validationErrors[field] ) validationClass = 'has-danger';
      if ( this.isRequired(field) ) {
        validationClass += ' required';
      }
      return validationClass.trim();
    },
    
    isRequired : function(field) {
      if ( _.isUndefined(this.fields[field].validation)) return false;
      var validation = this.fields[field].validation;
      if (validation.indexOf('required')>=0) {
        return true;
      }
      return false;
    },
    
    toggleInsertForm : function(field) {
      if (this.showInsertForm(field)) {
        this.insertForm[field].show = false;
      }
      else {
        this.$set(this.insertForm,field,{
          show  : true,
          field : field,
          table : this.fields[field]._options.table,
        });
      }
      // console.log('toggleInsertForm',field,this.insertForm);
    },
    
    showInsertForm : function(field) {
      var show = false;
      if ( !_.isUndefined(this.insertForm[field]) ) show = this.insertForm[field].show;
      // console.log('showInsertForm',field,show);
      return show;
    },
    
    subForm : function(field,property) {
      // console.log('subForm',field,property);
      if ( _.isUndefined(this.insertForm[field]) ) return '';
      return this.insertForm[field][property];
    },
    
    subFormAdded : function(field,event) {
      var self = this;
      self.insertForm[field].show = false;
      flexyState.api({
        // TODO: alleen opties van dit veld wellicht?
        url : 'table?table='+self.name+'&as_options=true',
      })
      .then(function(response){
        if (!_.isUndefined(response.data)) {
          // Vervang de opties 
          self.fields[field]._options = response.data.data[field];
          // Selecteer zojuist toegevoegde item
          self.addToSelect(field,event);
        }
        return response;
      });      
    },
    
    cancel : function() {
      var self=this;
      if (!this.isSaving) {
        tinyMCE.remove();
        self.$emit('formclose',self.row);
      }
    },
    
    submit : function() {
      var self=this;
      if (!self.isSaving) {
        var promise = self.postForm();
        if (promise) {
          promise.then(function (response) {
            if (!response.error) {
              tinyMCE.remove();
              self.$emit('formclose',self.row);
            }
          })
        }
      }
    },
    
    add : function() {
      var self=this;
      if (!this.isSaving) {
        self.postForm().then(function (response) {
          if (!response.error) {
            self.$emit('added',response.data.data.id);
          }
        })
      }
    },
    
    save : function() {
      if (!this.isSaving) {
        this.postForm();
      }
    },
    
    postForm : function() {
      var self=this;
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
          if (typeof(data[field])==='object' && this.isMultiple(field)) {
            var fieldData = [];
            for (var i = 0; i < data[field].length; i++) {
              if (!_.isUndefined(data[field][i])) fieldData.push( data[field][i].id );
            }
            data[field] = fieldData;
          }
        }
      }
      
      // Controleer of data niet leeg is
      var filled = false;
      for (var field in data) {
        if (typeof(data[field])==='object') {
          if (data[field].length>0) filled=true;
        }
        else {
          if (data[field] && data[field]!==this.fields[field]['default']) filled=true;
        }
      }
      
      // Als goed is ingevuld, ga dan door.
      if (filled) {
        return self._postForm(data);
      }
      
      // Als niet goed is ingevuld, vraag het
      flexyState.openModal( {'title':'','body':self.$lang.confirm_save_default}, function(event) {
        if ( event.state.type==='ok') {
          return self._postForm(data);
        }
      });
      return false;
    },
    _postForm : function(data) {
      var self = this;
      self.isSaving = true;
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
            // Update data (if prepped)
            for (var field in response.data.data) {
              if (!_.isUndefined(self.row[field])) {
                self.row[field] = response.data.data[field];
              }
            }
          }
          else {
            // Validation error
            response.error = true;
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
      // console.log('updateField',field,value);
      this.validationErrors = {};
      this.row[field] = value;
    },
    
    updateSelect : function( field, selected ) {
      var value = selected;
      if ( !this.isMultiple(field) ) {
        if (typeof(value)=='Array') {
          value = value[0];
        }
        if (value !== this.row[field]) {
          this.updateField(field,value);
        }
        return;
      }
      // Mulitple
      value = [];
      for (var i = 0; i < selected.length; i++) {
        value.push({'id':selected[i]});
      }
      var needsUpdate = (value.length !== this.row[field].length);
      if (!needsUpdate) {
        for (var i = 0; i < value.length; i++) {
          var id = value[i].id;
          var exists = jdb.indexOfProperty(this.row[field],'id',id);
          if ( exists<0 ) needsUpdate = true;
        }
        if (!needsUpdate) {
          for (var i = 0; i < this.row[field].length; i++) {
            var id = this.row[field][i].id;
            var exists = jdb.indexOfProperty(value,'id',id);
            if ( exists<0 ) needsUpdate = true;
          }
        }
      }
      if (needsUpdate) this.updateField(field,value);
    },
    
    addToSelect : function( field, value ) {
      // console.log('addToSelect',field,value);
      if ( this.isMultiple(field) ) {
        var currentSelection = this.row[field];
        // Als al bestaat, dan juist verwijderen
        var exists = jdb.indexOfProperty(currentSelection,'id',value);
        if (exists!==false) {
          // Verwijderen
          delete currentSelection[exists];
        }
        else {
          // Toevoegen
          currentSelection.push({'id':value});
        }
        value = currentSelection;
      }
      this.updateField(field,value);
    },
    

    // TinyMCE changed
    updateText : function(editor) {
      this.updateField(editor.id,editor.getContent());
    }
    
  }
  
}
</script>

<template>
<div class="card form">
  <div class="card-header">
    <h1>{{title}}</h1>
    <div>
      <flexy-button v-if="formtype!=='single'" @click.native="cancel()" :icon="{'long-arrow-left':formtype==='normal','':formtype==='subform'}" :text="$lang.cancel" :disabled="isSaving" class="btn-outline-danger"/>
      <flexy-button v-if="formtype!=='subform'" @click.native="save()"  icon="long-arrow-down" :text="$lang.save" :disabled="isSaving" class="btn-outline-warning"/>
      <flexy-button v-if="formtype==='normal'" @click.native="submit()" icon="level-down fa-rotate-90" :text="$lang.submit" :disabled="isSaving" class="btn-outline-info"/>
      <flexy-button v-if="formtype==='subform'" @click.native="add()" :text="$lang.add" :disabled="isSaving" class="btn-outline-warning"/>
    </div>
  </div>

  <div class="card-block">
    
    <tabs navStyle="tabs" class="tabs" :class="tabsClass()">
      <tab v-for="(fieldset,name) in fieldsets" :header="name">
        <template v-for="field in fieldset">
          <template v-if="!isType('hidden',field)">
          
            <div class="form-group row" :class="validationClass(field)">
              <div v-if="validationErrors[field]" class="validation-error"><span class="fa fa-exclamation-triangle"></span> {{validationErrors[field]}}</div>
              <label class="col-md-3 form-control-label" :for="field">{{label(field)}} <span v-if="isRequired(field)" class="required fa fa-exclamation text-warning"></span> </label>
              <div class="col-md-9">

                <template v-if="isType('textarea',field)">
                  <!-- Textarea -->
                  <textarea class="form-control" :id="field" :name="field" :value="row[field]" v-on:input="updateField(field,$event.target.value)" placeholder="">
                </template>
              
                <template v-if="isType('wysiwyg',field)">
                  <!-- WYSIWYG -->
                  <textarea class="form-control wysiwyg" :id="field" :name="field" :value="row[field]">
                </template>

                <template v-if="isType('checkbox',field)">
                  <!-- Checkbox -->
                  <input class="form-check-input" type="checkbox" :id="field" :name="field" :checked="row[field]" @input="updateField(field,$event.target.checked)">
                </template>

                <template v-if="isType('datepicker',field)">
                  <!-- Datepicker -->
                  <datepicker :id="field" :name="field" :value="row[field]" format="yyyy-MM-dd" @input="updateField(field,$event)"></datepicker>
                </template>

                <template v-if="isType('timepicker',field)">
                  <!-- Timepicker -->
                  <timepicker :id="field" :name="field" :value="row[field]" @input="updateField(field,$event)"></timepicker>
                </template>

                <template v-if="isType('datetimepicker',field)">
                  <!-- Datetimepicker -->
                  <datetimepicker :id="field" :name="field" :value="row[field]" @input="updateField(field,$event)"></datepicker>
                </template>

                <template v-if="isType('colorpicker',field)">
                  <!-- Colorpicker -->
                  <colorpicker :id="field" :name="field" :value="row[field]" v-on:input="updateField(field,$event)"></colorpicker>
                </template>

                <template v-if="isType('mediapicker',field)">
                  <!-- Mediapiacker -->
                  <mediapicker :id="field" :name="field" :value="row[field]" :path="fields[field].path" v-on:input="updateField(field,$event)"></mediapicker>
                </template>

                <template v-if="isType('select',field)">
                  <!-- Select -->
                  <vselect :name="field" 
                    :options="fields[field]._options.data" options-value="value" options-label="name" 
                    :value="selectValue(field)" 
                    :multiple="isMultiple(field)"
                    @change="updateSelect(field,$event)"
                    :insert="hasInsertRights(field)"
                    :insertText="$lang.add_item | replace(label(field))"
                    @insert="toggleInsertForm(field)"
                    >
                  </vselect>
                </template>

                <template v-if="isType('radio',field)">
                  <!-- Radio -->
                  <template v-for="option in fields[field]._options.data">
                    <div class="form-check form-check-inline form-subcheck" :class="{'checked':isSelectedOption(field,row[field],option.value)}" @click="selectOption(field,option.value)">
                      <label class="form-check-label" :title="selectItem(option.name)">
                      <flexy-button :icon="{'check-square-o':isSelectedOption(field,row[field],option.value),'square-o':!isSelectedOption(field,row[field],option.value)}" class="btn-outline-default"/>
                      <input  class="form-check-input"
                              :value="option.value"
                              :name="field"
                              :type="isMultiple(field)?'checkbox':'radio'"
                              :checked="isSelectedOption(field,row[field],option.value)"
                              v-on:input="addToSelect(field,option.value)"
                              >
                      {{selectItem(option.name)}}
                      </label>
                    </div>
                  </template>
                </template>
              
                <template v-if="isType('default',field)">
                  <!-- Default -->
                  <input type="text" class="form-control" :id="field" :name="field" :value="row[field]" v-on:input="updateField(field,$event.target.value)" placeholder="" @keyup.enter="submit">
                </template>

                <div v-if="showInsertForm(field)">
                  <flexy-form :title="$lang.add_item | replace(label(field))" :name="subForm(field,'table')" :primary="-1" formtype="subform" @added="subFormAdded(field,$event)" @formclose="toggleInsertForm(field)"></flexy-form>
                </div>

              </div>
              
              <!-- <div class="col-md-1" v-if="hasInsertRights(field)">
                <flexy-button @click.native="toggleInsertForm(field)" :icon="{'plus':!showInsertForm(field),'chevron-up':showInsertForm(field)}" class="btn-outline-warning" />
              </div> -->
            </div>
            
          </template>
        </template>
      </tab>
    </tabs>

  </div>
</div>
</template>
