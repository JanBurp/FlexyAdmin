<script>

var he = require('he');

import jdb              from '../../jdb-tools.js'

import flexyState       from '../../flexy-state.js'
import flexyButton      from '../flexy-button.vue'

import flexyThumb       from '../flexy-thumb.vue'

import timepicker       from './timepicker.vue'
import datetimepicker   from './datetimepicker.vue'
import colorpicker      from './colorpicker.vue'
import mediapicker      from './mediapicker.vue'
import joinselect       from './joinselect.vue'
import radioimage       from './radio-image.vue'
import markdown         from './markdown.vue'

import tab              from '../../vue-strap-src/components/Tab.vue'
import tabs             from '../../vue-strap-src/components/Tabs.vue'
import vselect          from '../../vue-strap-src/components/Select.vue'
import datepicker       from '../../vue-strap-src/Datepicker.vue'

export default {
  name: 'FlexyForm',
  components: {flexyButton,flexyThumb,timepicker,datetimepicker,colorpicker,mediapicker,joinselect,radioimage,tab,tabs,datepicker,vselect,markdown},
  props:{
    'name'    :String,
    'primary' :{
      type: [Number,String],
      default: -1,
    },
    'action'  :{
      type: String,
      default: '',
    },
    'title'   : {
      type: String,
      default: '',
    },
    'fields' : {
      type: [Boolean,Array,Object],
      default: false,
    },
    'formtype':{
      type:String,
      default:'normal', // normal|single|subform
    },
    'parent_data':{
      type:[Boolean,Object],
      default:false,
    },
    'disabled':{
      type:Boolean,
      default:true,
    },
    'message':{
      type:[Object,String],
      default:'',
    }
  },


  computed : {
    fieldTypes : function() {
      var types = {
        primary           : ['primary'],
        hidden            : ['hidden','primary','uri','order'],
        abstract          : ['abstract','show','disabled'],
        checkbox          : ['checkbox'],
        datepicker        : ['date'],
        timepicker        : ['time'],
        datetimepicker    : ['datetime'],
        colorpicker       : ['color','rgb'],
        mediapicker       : ['media','medias'],
        thumb             : ['thumb'],
        select            : ['select'],
        radio             : ['radio','radio_image'],
        joinselect        : ['joinselect'],
        textarea          : ['textarea'],
        wysiwyg           : ['wysiwyg'],
        markdown          : ['markdown'],
      };
      var defaultTypes = [];
      for(var type in types) {
        defaultTypes = defaultTypes.concat(types[type]);
      }
      types.default = defaultTypes;
      return types;
    },
  },

  watch : {
    '$route.params.table' : function(val) {
      if (val !== this.currentName) {
        this.reloadForm();
      }
    }
  },

  // Copy of props.data (& more)
  data : function() {
    return {
      uiTitle             : this.title,
      abstract_fields     : [],
      abstract_delimiter  : '|',
      currentName      : '',
      activeTab        : 0,
      row              : {},
      form_groups      : {},
      fieldsets        : {},
      validationErrors : {},
      isSaving         : false,
      subForm          : {},
      isEdited         : !this.disabled,
      wysiwygJustReady : false,
      displayedMessage : '',
    }
  },

  created : function() {
    // Api form
    if ( this.fields===false ) {
      this.reloadForm();
    }
    // Normal form
    else {
      // Fields
      var fields = Object.keys(this.fields);
      // Fieldsests
      var fieldset = this.uiTitle;
      this.fieldsets = { fieldset : fields };
      this.form_groups = this.fields;
      for (var field in this.fields) {
        // value
        var value = '';
        if (!_.isUndefined(this.fields[field].value)) {
          value = this.fields[field].value;
        }
        this.row[field] = value;
        // validation error
        if (!_.isUndefined(this.fields[field].validation_error)) {
          this.$set( this.validationErrors, field, this.fields[field].validation_error );
        }
        // options in name->value pairs
        if (!_.isUndefined(this.form_groups[field].options)) {
          var options = this.form_groups[field].options;
          var optionsArray = [];
          Object.keys(options).forEach(function(key) {
            if (_.isUndefined(options[key].name)) {
              optionsArray.push({name:options[key],value:key});
            }
            else {
              optionsArray.push(options[key]);
            }
          });
          this.form_groups[field].options = {};
          this.form_groups[field].options.data = optionsArray;
        }
      }
      this.createWysiwyg();
    }
    this.currentName = this.name;
  },

  mounted : function() {
    var options = location.search;
    if (options!=='' && options.substr(0,9)==='?options=') {
      options = options.substr(9);
      options = decodeURIComponent(options);
      options = JSON.parse(options);
      this.activeTab = options.tab || 0;
    }
  },

  beforeUpdate : function() {
    if (this.name !== this.currentName) {
      this.reloadForm();
    }
    this.currentName = this.name;

    // message
    if (this.message!='') {
      if (typeof(this.message)=='string') {
        this.displayedMessage = this.message;
      }
      else {
        if (!_.isUndefined(this.message.message)) {
          if (!_.isUndefined(this.message.condition)) {
            var show = false;
            var condition = this.message.condition;
            if (condition=='own_user') {
              show = !_.isUndefined(this.row['_own_user']);
            }
            else {
              condition = this._replace_field_in_func(condition);
              show = eval(condition);
            }
            if (show) {
              this.displayedMessage = this.message.message;
            }
          }
          else {
            this.displayedMessage = this.message.message;
          }
        }
      }
    }
  },


  methods:{

    reloadForm : function() {
      if (this.action==='') {
        var self = this;
        return flexyState.api({
          url       : self.apiUrl(),
        })
        .then(function(response){
          if (!_.isUndefined(response.data)) {
            if (response.data.success) {
              // Zijn er settings meegekomen?
              if ( !_.isUndefined(response.data.settings) ) {
                self.uiTitle          = response.data.settings.form_set.title;
                self.form_groups      = Object.assign({},response.data.settings.form_set.field_info);
                self.fieldsets        = Object.assign({},response.data.settings.form_set.fieldsets);
                self.abstract_fields  = response.data.settings.abstract_fields;
                self.abstract_delimiter = response.data.settings.abstract_delimiter;
              }
              // Data en die aanvullen met data
              self.row = response.data.data;
              self.processDataAfterLoad();
            }
          }
          // TinyMCE
          self.createWysiwyg();
          return response;
        });
      }
    },

    processDataAfterLoad : function() {
      var self = this;
      // Als data foreignkeys met een json bevat, pas dat aan tot een simpele id en voeg de abstract toe aan de opties
      for(var field in self.row) {
        var value = _.clone(self.row[field]);
        if (value = jdb.isJsonString(value)) {
          var key = _.parseInt(Object.keys(value)[0]);
          value   = Object.values(value)[0];
          if (!_.isUndefined(value) && key>0) {
            var options = self.form_groups[field]['options']['data'] || [];
            var index = jdb.indexOfProperty(options,'value',key);
            if (index===false) options.push({'value' : key,'name'  : value});
            self.form_groups[field]['options']['data'] = options;
            self.row[field] = key;
          }
        }
      }
    },

    createWysiwyg: function() {
      var self=this;

      // Init settings
      var init = _flexy.tinymceOptions;
      init = _.extend(_flexy.tinymceOptions,{
        setup : function(ed){
          ed.on('NodeChange', function(e){ self.updateText(e,ed); })
          ed.on('keyup', function(e){ self.updateText(e,ed); });
        }
      });

      // Need to remove?
      var exists = document.querySelector('.mce-tinymce');
      this.wysiwygJustReady = false;
      tinymce.remove();

      // Init (try untill its ready)
      var timer = window.setInterval(function(){
        if ( !self.wysiwygJustReady ) {
          tinymce.init(init);
          self.wysiwygJustReady=1;
        }
        exists = document.querySelector('.mce-tinymce');
        if ( !_.isUndefined(exists) && exists!==null ) {
          clearInterval(timer)
          self.wysiwygJustReady = 2;
        };
      }, 100 );
    },

    apiUrl : function() {
      var parts = _.extend( this.apiParts );
      this.apiParts = parts;
      var url = 'row?table='+this.name + '&where='+this.primary + '&as_form=true&settings=form_set';
      if (this.parent_data) url+='&parent_data='+JSON.stringify(this.parent_data);
      return url;
    },

    formTitle : function() {
      var title = this.uiTitle;
      var itemTitle = [];
      for (var i in this.abstract_fields) {
        var field = this.abstract_fields[i];
        if (!_.isUndefined(this.row[field]) && this.row[field]!='') {
          itemTitle.push(this.row[field]);
        }
      }
      if (itemTitle) {
        title += ' - '+itemTitle.join(this.abstract_delimiter);
      }

      return he.decode(title,{});
      // return title;
    },

    label : function(field) {
      if (_.isUndefined(this.form_groups[field])) return field;
      return this.form_groups[field].label;
    },

    placeholder : function(field) {
      return '';
      // if (this.formtype!=='subform') return '';
      // return this.label(field);
    },

    selectTab : function(tab) {
      this.activeTab = tab;
      if (this.formtype!=='subform') {
        var options = location.search;
        if (options!=='' && options.substr(0,9)==='?options=') {
          options = options.substr(9);
          options = decodeURIComponent(options);
          options = JSON.parse(options);
          // update with current tab
          options.tab = this.activeTab;
          history.pushState(options, '', location.pathname+'?options='+JSON.stringify(options));
        }
      }
    },

    selectedTab : function() {
      return this.activeTab;
    },

    tabsClass : function() {
      if (Object.keys(this.fieldsets).length<2) return 'single-tab';
      return '';
    },

    tabHeaderClass : function(fieldset) {
      for(var index in fieldset) {
        var field=fieldset[index];
        if (this.validationError(field)) return 'text-danger';
      }
      return '';
    },

    isType : function( type,field ) {
      if (_.isUndefined(this.form_groups[field])) {
        if (type==='default') {
          console.error('FlexyAdmin error: No (existing) field type defined for `'+field+'`. Set to `default`.');
          return true;
        }
        return false;
      }
      if (type==='default') {
        return this.fieldTypes['default'].indexOf(this.form_groups[field]['type']) === -1;
      }
      // // wysiwyg is textarea as long as editor is not present
      // if (this.form_groups[field]['type']==='wysiwyg') {
      //   console.log(type,field,this.wysiwygJustReady);
      //   if (this.wysiwygJustReady!==false) {
      //     if (type==='wysiwyg') {
      //       return true;
      //     }
      //   }
      //   else {
      //     if (type==='textarea') {
      //       return true;
      //     }
      //   }
      //   return false;
      // }
      return this.fieldTypes[type].indexOf(this.form_groups[field]['type']) >= 0;
    },

    editTypeIcon() {
      if (this.primary==-1) {
        return 'fa-plus';
      }
      if (this.primary.substr(0,1)=='_') {
        return 'fa-paste';
      }
      if (this.primary>=0) {
        return 'fa-pencil';
      }
      return '';
    },

    isRadioImage : function(field) {
      return (this.form_groups[field]['type']=='radio_image');
    },

    isMultiple : function( field ) {
      var multiple = false;
      if (_.isUndefined(this.form_groups[field])) return false;
      if (_.isUndefined(this.form_groups[field].options)) return false;
      if (this.form_groups[field].options.multiple) {
        multiple='multiple';
      }
      else {
        if (_.isUndefined(this.form_groups[field].multiple)) return false;
        if (this.form_groups[field].multiple) multiple='multiple';
      }
      if (flexyState.debug) console.log('isMultiple',field,multiple);
      return multiple;
    },

    isSelectedOption : function(field,value,option) {
      var selected = '';
      if (typeof(value)!=='object') {
        if (typeof(value)==='string') {
          value = value.split('|');
          for (var i = 0; i < value.length; i++) {
            if (value[i]==option) selected='selected';
          }
        }
        else {
          if (parseInt(value)===option || value===option) selected='selected';
        }
      }
      else {
        for(var item in value) {
          var id = parseInt(value[item]['id']);
          if (id===option) selected='selected';
        }
      }
      return selected;
    },

    fieldOptionsAjax: function(field) {
      if (_.isUndefined(this.form_groups[field])) return '';
      if (_.isUndefined(this.form_groups[field].options)) return '';
      if (_.isUndefined(this.form_groups[field].options.api)) return '';
      return this.form_groups[field].options.api;
    },

    fieldOptions: function(field) {
      var self = this;
      var options = [];
      if (_.isUndefined(this.form_groups[field])) return [];
      if (_.isUndefined(this.form_groups[field].options)) return [];
      if (_.isUndefined(this.form_groups[field].options.data)) {
        if ((_.isUndefined(this.form_groups[field].options.api))) {
          return [];
        }
        // Options loaded with AJAX?
        var apiCall = this.form_groups[field].options.api;
        // Current values as options included
        var currentOptions = _.clone(this.row[field]);
        // options['data'] = [];
        for (var option in currentOptions) {
          options.push({'name':currentOptions[option]['abstract'],'value':currentOptions[option]['id']});
        }
        return options;
      }

      options = _.clone(this.form_groups[field].options.data);
      if ( !_.isUndefined(this.form_groups[field]['dynamic']) && !_.isUndefined(this.form_groups[field]['dynamic']['options']) ) {
        var filter_field = this.form_groups[field]['dynamic']['options']['filter_by'];
        var filter = this.row[filter_field];
        if (filter) {
          var index = jdb.indexOfProperty(options,'value',filter);
          if (!_.isUndefined(options[index])) {
            var options_object = _.clone(options[index]['name']);
            options = [];
            for (var opt in options_object) {
              options.push( {'name':opt,'value':opt} );
            }
          }
          else {
            options = [];
          }
        }
        else {
          options = [];
        }
      }
      return options;
    },

    selectOption: function(field,option) {
      // console.log('selectOption',field,option);
      this.row[field] = option;
    },

    selectItem : function (field,value) {
      if (!value) return '';
      value = value.toString();
      value = value.replace(/\|/g,' | ').replace(/^\|/,'').replace(/\|$/,'');
      return value;
    },

    selectItemImg : function(field,value) {
      var optionsSettings = this.form_groups[field].options.settings;
      return {
        'src'   : 'assets/img/' + optionsSettings.src,
        'width' : optionsSettings.width,
        'height': optionsSettings.height,
      };
    },

    thumbValue : function(field) {
      var value = _flexy.media+this.row['path']+'/'+this.row[field];
      return value;
    },

    abstractValue : function(field) {
      if (!_.isUndefined(this.form_groups[field]['options']['data'])) {
        var index = jdb.indexOfProperty(this.form_groups[field]['options']['data'],'value',this.row[field]);
        return this.form_groups[field]['options']['data'][index]['name'];
      }
      return this.row[field];
    },

    selectValue: function(field) {
      var value = this.row[field];
      if ( this.isMultiple(field) ) {
        value = [];
        var row = this.row[field];
        if (typeof(row)==='string') {
          value = row.split('|');
        }
        else {
          for (var i = 0; i < row.length; i++) {
            if ( _.isUndefined(row[i].id) ) {
              value.push( row[i] );
            }
            else {
              value.push( row[i].id );
            }
          }
        }
      }
      return value;
    },

    hasInsertRights : function(field) {
      if ( _.isUndefined(this.form_groups[field]) ) return false;
      if ( _.isUndefined(this.form_groups[field].options) ) return false;
      if ( _.isUndefined(this.form_groups[field].options.insert_rights) ) return false;
      var rights = this.form_groups[field].options.insert_rights;
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

    formGroupClass : function(field) {
      var formGroupClass='';
      // type
      formGroupClass += 'form-group-type-' + this.form_groups[field].type + ' ';
      // validation
      if ( this.validationErrors[field] ) {
        formGroupClass += 'has-danger';
      }
      if ( this.isRequired(field) ) {
        formGroupClass += ' required';
      }
      // subform
      if (!_.isUndefined(this.subForm[field])) {
        if (this.subForm[field].show) formGroupClass += ' has-subform';
      }
      return formGroupClass.trim();
    },

    validationError : function(field) {
      var error = false;
      if (!_.isUndefined(this.validationErrors[field])) error = this.validationErrors[field];
      return error;
    },

    isRequired : function(field) {
      if ( _.isUndefined(this.form_groups[field])) return false;
      if ( _.isUndefined(this.form_groups[field].validation)) return false;
      var validation = this.form_groups[field].validation;
      if (validation.indexOf('required')>=0) {
        return true;
      }
      return false;
    },

    showFormGroup : function(field) {
      var show = true;
      if (_.isUndefined(this.form_groups[field])) return show;
      if (!_.isUndefined(this.form_groups[field].dynamic) && !_.isUndefined(this.form_groups[field].dynamic.show) ) {
        var func = this.form_groups[field].dynamic.show;
        func = this._replace_field_in_func(func);
        show = eval(func);
      }
      return show;
    },

    _replace_field_in_func : function(func) {
      var fields = Object.keys(this.row);
      var normalReplace = true;
      if (func.indexOf('%')>=0) normalReplace = false;
      var newFunc = '';
      var regex = '';
      for (var i = fields.length - 1; i >= 0; i--) {
        if (normalReplace) {
          regex = new RegExp('\\b'+fields[i]+'\\b','');
        }
        else {
          regex = new RegExp('%'+fields[i]+'%','');
        }
        newFunc = func.replace(regex, "this.row['" + fields[i] + "']");
        if (newFunc!==func) func = newFunc;
      }
      return func;
    },

    valueFromApi : function(field) {
      var value = this.row[field];
      if (_.isUndefined(this.form_groups[field])) return value;
      if (!_.isUndefined(this.form_groups[field].value_eval)) {
        var value_eval = this.form_groups[field].value_eval;
        // console.log(field,value_eval);
      }
      return value;
    },

    toggleSubForm : function(field,id) {
      if (this.showSubForm(field)) {
        this.subForm[field].show = false;
      }
      else {
        this.$set(this.subForm,field,{
          show  : true,
          table : this.form_groups[field].options.table,
          field : field,
          id    : id,
        });
      }
    },

    showSubForm : function(field) {
      var show = false;
      if ( !_.isUndefined(this.subForm[field]) ) show = this.subForm[field].show;
      return show;
    },

    hasVisibleSubform : function(field) {
      if ( !_.isUndefined(this.subForm[field]) ) return this.subForm[field].show;
      return false;
    },

    // hideSubForm : function(field) {
    //   if ( !_.isUndefined(this.subForm[field]) ) this.subForm[field].show = false;
    //   return false;
    // },

    subFormData : function(field,property) {
      if ( _.isUndefined(this.subForm[field]) ) return '';
      return this.subForm[field][property];
    },

    // Geeft zinvolle data (table,id) van de parent form mee naar subform
    parentData : function() {
      return { table:this.name, id:this.primary };
    },

    subFormAdded : function(field,event) {
      var self = this;
      self.subForm[field].show = false;
      flexyState.api({
        url : 'row?table='+self.name+'&where='+self.primary+'&as_form=true&settings=form_set',
      })
      .then(function(response){
        if (!_.isUndefined(response.data)) {
          // Pas huidige 'value' aan
          self.row[field] = response.data.data[field];
          // Vervang de opties
          self.form_groups[field].options = response.data.settings.form_set.field_info[field].options;
          // Stamp bij api, zodat reload wordt geforceerd
          if (!_.isUndefined(self.form_groups[field].options.api)) {
            var api = self.form_groups[field].options.api;
            api = api.replace(/&stamp=\d*/g, ""); // oude stamp verwijderen
            api += '&stamp=' + Date.now(); // nieuwe stamp
            self.form_groups[field].options.api = api;
          }
          // Selecteer zojuist toegevoegde/aangepaste item
          self.addToSelect(field,event);
          self.$emit('formclose');
        }
        return response;
      });
    },

    cancel : function() {
      var self=this;
      if (this.isEdited && this.formtype!=='subform') {
        flexyState.openModal( {
            'title':'',
            'body':self.$lang['confirm_cancel'],
            'size':'modal-sm',
            'buttons' : [
              {
                type   : 'no',
                title  : _flexy.language_keys.no,
                class  : 'btn-outline-primary',
                close  : true,
              },
              {
                type   : 'yes',
                title  : _flexy.language_keys.yes,
                class  : 'btn-outline-danger',
                close  : true,
              },
            ],
          }, function(event) {
          if ( event.state.type==='yes') {
            self._cancel();
          }
        });
      }
      else {
        this._cancel();
      }
    },
    _cancel : function() {
      var self=this;
      if (this.formtype==='subform') {
        self.$emit('formclose');
      }
      else {
        if (!this.isSaving) {
          tinyMCE.remove();
          var name = this.name;
          var url = '/edit/'+name + location.search;
          if ( name.match(/^media_(.*?)/i) ) {
            name = name.replace(/^media_(.*?)/gi, "$1");
            url = '/media/'+name  + location.search;
          }
          this.$router.push(url);
        }
      }
    },

    submit : function() {
      var self=this;
      if (!self.isSaving) {
        var promise = self.postForm();
        if (promise) {
          promise.then(function (response) {
            if (!response.error) {
              if (self.formtype=='subform') {
                self.$emit('added',response.data.data.id);
              }
              else {
                self._cancel();
              }
            }
          })
        }
      }
    },

    // add : function() {
    //   var self=this;
    //   if (!this.isSaving) {
    //     var promise = self.postForm();
    //     if ( typeof(promise.then)=='function') {
    //       promise.then(function (response) {
    //         if (!response.error) {
    //           self.$emit('added',response.data.data.id);
    //         }
    //       })
    //     }
    //   }
    // },

    save : function() {
      if (!this.isSaving) {
        this.postForm();
      }
    },

    postForm : function() {
      var self=this;
      var data = JSON.parse(JSON.stringify(this.row)); // Deep copy

      // Prepare data
      for (var field in data) {
        if (field.indexOf('.abstract')>0) {
          delete(data[field]);
        }
        else {
          // Checkbox
          if (this.isType('checkbox',field)) {
            data[field] = (data[field]?1:0);
          }
          // Multiple
          if (typeof(data[field])==='object' && this.isMultiple(field) && !this.isType('joinselect',field)) {
            var fieldData = [];
            for (var i = 0; i < data[field].length; i++) {
              if ( !_.isUndefined(data[field][i]) ) {
                if ( !_.isUndefined(data[field][i].id) ) {
                  fieldData.push( data[field][i].id );
                }
                else {
                 fieldData.push( data[field][i] );
                }
              }
            }
            data[field] = fieldData;
          }

          // Joinselect -> maak een post ready array (en lege items verwijderen)
          if (this.isType('joinselect',field)) {
            // cleanup empty data
            function isEmpty(item) {
              var empty = true;
              _.each(item,function(value,field){
                if (value!=='' && field!=='id') {
                  empty = false;
                }
              });
              return empty;
            };
            for (var index = data[field].length - 1; index >= 0; index--) {
              if ( isEmpty(data[field][index]) ) {
                data[field].splice(index,1);
              }
            }
            // JSONify
            data[field+'__array'] = JSON.stringify(data[field]);
            delete(data[field]);
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
          if (data[field] && !_.isUndefined(this.form_groups[field]) && data[field]!==this.form_groups[field]['default']) filled=true;
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


    /**
     * Post form door api aan te roepen
     *
     * @param      {<type>}  data    The data
     * @return     {<type>}  { description_of_the_return_value }
     */
    _postForm : function(data) {
      this.validationErrors = {};

      // Normale form?
      if (this.action !=='' ) {
        jdb.submitWithPost(this.action, data );
        return null;
      }

      // Ajax post naar API
      var self = this;
      self.isSaving = true;
      var postData = {
        'table'   : self.name,
        'where'   : self.row['id'],
        'data'    : data,
      }
      if (this.formtype==='subform') {
        postData.parent_data = self.parent_data;
      }
      return flexyState.api({
        url  : 'row',
        data : postData,
      }).then(function(response){
        self.isSaving = false;
        self.isEdited = false;
        if (!response.error) {
          if ( _.isUndefined(response.data.info) || response.data.info.validation!==false) {
            if ( !_.isUndefined(response.data.data)) {
              flexyState.addMessage('Item saved');
              self._updateDataAfterPost(response.data.data);
            }
          }
          else {
            // Validation error
            response.error = true;
            flexyState.addMessage( self.$lang.form_validation_error, 'danger');
            if ( !_.isUndefined(response.data.info) ) self.validationErrors = response.data.info.validation_errors;
            // self._updateDataAfterPost(response.data.data);
          }
        }
        else {
          flexyState.addMessage( self.$lang.form_save_error, 'danger');
          self.isEdited = true;
        }
        return response;
      });
    },
    _updateDataAfterPost : function(postdata) {
      var self = this;
      // Update all fields in row
      for (var field in postdata) {
        if ( !_.isUndefined(self.row[field]) ) {
          self.row[field] = postdata[field];
        }
      }
      self.processDataAfterLoad();
      // Update url (id)
      var url = location.pathname;
      var newUrl = url.replace( '/'+self.name+'/-1', '/'+self.name+'/'+postdata['id'] ) + location.search;
      history.pushState( location.search, '', newUrl);
    },

    isNewItem : function() {
      return this.row['id'] === -1;
    },

    updateField : function( field, value ) {
      // console.log('updateField',field,value);
      // this.validationErrors = {};
      if (this.row[field]!==value) {
        this.row[field] = value;
        this.dynamicWatch(field,value);
        this.isEdited = true;
      }
    },

    dynamicWatch : function(field,value) {
      var self = this;
      if ( !_.isUndefined(self.fields[field]) && !_.isUndefined(self.fields[field].dynamic) && !_.isUndefined(self.fields[field].dynamic.watch)) {
        var api = self.fields[field].dynamic.watch.api;
        var update = self.fields[field].dynamic.watch.update;
        if ( !_.isUndefined(api) && !_.isUndefined(update)) {
          // Load dynamic data
          if ( _.isObject(value)) {
            // Array van select omzetten
            var valueArray = [];
            for (var index in value) {
              if (!_.isUndefined(value[index].id)) {
                valueArray.push(value[index].id);
              }
              else {
                valueArray.push(value[index]);
              }
            }
            value = valueArray;
          }
          // Formdata meegeven?
          var formdata = {};
          if (!_.isUndefined(self.fields[field].dynamic.watch.data)) {
            for(var i in self.fields[field].dynamic.watch.data) {
              var key = self.fields[field].dynamic.watch.data[i];
              formdata[key] = this.row[key];
            }
          }
          // Doe de api call
          return flexyState.api({
            url : api + '&where=' + value + '&formdata=' + JSON.stringify(formdata),
          }).then(function(response){
            if (!_.isUndefined(response.data.data)) {
              var data = response.data.data
              // Update dynamic data
              for(var update_field in update) {
                if (!_.isUndefined(self.row[update_field])) {
                  var data_field = update[update_field];
                  self.$delete( self.row, update_field );
                  self.$set( self.row, update_field, data[data_field] );
                  // update wysiwyg
                  if (self.isType('wysiwyg',update_field)) {
                    tinymce.activeEditor.setContent(self.row[update_field]);
                  }
                }
              }
            }
          });
        }
      }
    },

    updateJoinSelect : function(field,value) {
      this.isEdited = true;
      return this.updateField(field,value);
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
      if ( this.isMultiple(field) ) {
        var currentSelection = this.row[field];
        if ( typeof(currentSelection)==='string' ) {
          currentSelection = currentSelection.split('|');
          // Als bestaat: verwijderen
          var exists = currentSelection.indexOf(value);
          if (exists>=0) {
            currentSelection.splice(exists,1);
          }
          else {
            currentSelection.push(value);
          }
          value = currentSelection.join('|');
          value = value.replace(/\|+/, '|');
          value = _.trim(value,'|');
        }
        else {
          // Alleen toevoegen als nog niet bestaat
          var exists = jdb.indexOfProperty(currentSelection,'id',value);
          if (!exists) {
            // Toevoegen
            currentSelection.push({'id':value});
          }
          value = currentSelection;
        }
      }
      this.updateField(field,value);
    },


    // TinyMCE changed
    updateText : function(event,editor) {
      // console.log('updateText',this.row[editor.id]!==editor.getContent(),event.type);
      if (this.wysiwygJustReady==0) {
        this.updateField(editor.id,editor.getContent());
      }
      else {
        if (event.type=='nodechange') {
          this.wysiwygJustReady--;
        }
      }
    }

  }

}
</script>

<template>
<div class="card form" :class="'form-'+formtype">
  <div v-if="formtype!=='subform'" class="card-header">
    <h1><span class="form-edit-type fa" :class="editTypeIcon()"></span>{{formTitle()}}</h1>
    <div>
      <flexy-button v-if="formtype!=='single'"                 @click.native="cancel()" :icon="{'long-arrow-left':formtype==='normal','':formtype==='subform'}" :text="$lang.cancel" :disabled="isSaving" class="btn-outline-danger"/>
      <flexy-button v-if="formtype!=='subform' && action===''" @click.native="save()"   icon="long-arrow-down" :text="$lang.save" :disabled="isSaving || !isEdited" class="btn-outline-warning"/>
      <flexy-button v-if="action !==''"                        @click.native="save()"   :text="$lang.submit" :disabled="isSaving || !isEdited" class="btn-outline-info"/>
      <flexy-button v-if="formtype==='normal'"                 @click.native="submit()" icon="level-down fa-rotate-90" :text="$lang.submit" :disabled="isSaving || !isEdited" class="btn-outline-info"/>
      <flexy-button v-if="formtype==='subform'"                @click.native="submit()" :text="$lang.submit" :disabled="isSaving || !isEdited" class="btn-outline-warning"/>
    </div>
  </div>

  <div class="card-body">

    <div v-if="displayedMessage!==''" class="text-danger">{{displayedMessage}}</div>

    <tabs navStyle="tabs" class="tabs" :class="tabsClass()" @tab="selectTab($event)" :value="selectedTab()">
      <tab v-for="(fieldset,name) in fieldsets" :key="fieldset.name" :header="name" :headerclass="tabHeaderClass(fieldset)">
        <template v-for="field in fieldset">
          <template v-if="!isType('hidden',field)">

            <div class="form-group row" :class="formGroupClass(field)" v-show="showFormGroup(field)">
              <div v-if="validationError(field)!==false" class="validation-error"><span class="fa fa-exclamation-triangle"></span> {{validationError(field)}}</div>
              <label class="form-control-label col-md-3" :for="field" :title="label(field)">{{label(field)}} <span v-if="isRequired(field)" class="required fa fa-sm fa-asterisk text-warning"></span> </label>

              <div class="col-md-9">
              <template v-if="isType('textarea',field)">
                  <!-- Textarea -->
                  <textarea class="form-control" :id="field" :name="field" :value="row[field]" v-on:input="updateField(field,$event.target.value)" placeholder=""></textarea>
                </template>

                <template v-if="isType('wysiwyg',field)">
                  <!-- WYSIWYG -->
                  <textarea class="form-control wysiwyg" :id="field" :name="field" :value="row[field]"></textarea>
                </template>

                <template v-if="isType('markdown',field)">
                  <!-- MARKDOWN -->
                  <markdown :id="field" :name="field" :value="row[field]" @input="updateField(field,$event)"></markdown>
                </template>

                <template v-if="isType('checkbox',field)">
                  <!-- Checkbox -->
                  <input class="form-check-input" type="checkbox" :id="field" :name="field" :checked="row[field]" @click="updateField(field,$event.target.checked)">
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
                  <datetimepicker :id="field" :name="field" :value="row[field]" @input="updateField(field,$event)"></datetimepicker>
                </template>

                <template v-if="isType('colorpicker',field)">
                  <!-- Colorpicker -->
                  <colorpicker :id="field" :name="field" :value="row[field]" v-on:input="updateField(field,$event)"></colorpicker>
                </template>

                <template v-if="isType('mediapicker',field)">
                  <!-- Mediapiacker -->
                  <mediapicker :id="field" :name="field" :value="row[field]" :path="form_groups[field].path" :multiple="field.substr(0,7) === 'medias_'" v-on:input="updateField(field,$event)"></mediapicker>
                </template>

                <template v-if="isType('thumb',field)">
                  <!-- Thumb -->
                  <flexyThumb :src="thumbValue(field)" size="lg" :alt="row[field]"></flexyThumb><span>{{row['path']}}/{{row[field]}}</span>
                </template>

                <template v-if="isType('select',field)">
                  <!-- Select -->
                  <vselect :name="field"
                    :options="fieldOptions(field)" options-value="value" options-label="name" :options-ajax="fieldOptionsAjax(field)"
                    :primary="primary"
                    :value="selectValue(field)"
                    :multiple="isMultiple(field)"
                    @change="updateSelect(field,$event)"
                    :insert="hasInsertRights(field)"
                    :insertText="$lang.add_item | replace(label(field))"
                    @insert="toggleSubForm(field)"
                    @update="toggleSubForm(field,$event)"
                    :disabled="hasVisibleSubform(field)"
                    >
                  </vselect>
                </template>

                <template v-if="isType('radio',field)">
                  <!-- Radio -->
                  <template v-for="(option,index) in fieldOptions(field)">
                    <div class="form-check form-check-inline form-subcheck" :class="{'checked':isSelectedOption(field,row[field],option.value)}" @click="addToSelect(field,option.value)">
                      <label class="form-check-label" :title="selectItem(field,option.name)">
                      <flexy-button :icon="{'check-square-o':isSelectedOption(field,row[field],option.value),'square-o':!isSelectedOption(field,row[field],option.value)}" class="btn-outline-default"/>
                      <input  class="form-check-input"
                              :value="option.value"
                              :name="field"
                              :type="isMultiple(field)?'checkbox':'radio'"
                              :checked="isSelectedOption(field,row[field],option.value)"
                              >
                      <template v-if="isRadioImage(field)">
                        <radioimage :settings="selectItemImg(field,option.name)" :index="index"></radioimage>
                      </template>
                      <template v-else>
                        {{selectItem(field,option.name)}}
                      </template>
                      </label>
                    </div>
                  </template>
                </template>

                <template v-if="isType('joinselect',field)">
                  <!-- Joinselect -->
                  <joinselect :id="field" :name="field" :value="row[field]" @change="updateJoinSelect(field,$event)"></joinselect>
                </template>

                <template v-if="isType('abstract',field)">
                  <!-- Only show -->
                  <span class="text-muted">{{abstractValue(field)}}</span>
                </template>


                <template v-if="isType('default',field)">
                  <!-- Default -->
                  <input type="text" class="form-control" :id="field" :name="field" :value="row[field]" v-on:input="updateField(field,$event.target.value)" :placeholder="placeholder(field)" @keyup.enter="submit">
                </template>

                <div v-if="showSubForm(field)" class="subform">
                  <flexy-form :title="label(field)" :name="subFormData(field,'table')" :primary="subFormData(field,'id')" formtype="subform" :parent_data="parentData()" @added="subFormAdded(field,$event)" @formclose="toggleSubForm(field)"></flexy-form>
                </div>

              </div>

            </div>

          </template>
        </template>
      </tab>
    </tabs>

  </div>

  <div v-if="formtype==='subform'" class="card-header">
    <h1></h1>
    <div>
      <flexy-button @click.native="cancel()" :icon="{'long-arrow-left':formtype==='normal','':formtype==='subform'}" :text="$lang.cancel" :disabled="isSaving" class="btn-outline-danger"/>
      <flexy-button @click.native="submit()" :text="$lang.submit" :disabled="isSaving || !isEdited" class="btn-outline-warning"/>
    </div>
  </div>

</div>
</template>
