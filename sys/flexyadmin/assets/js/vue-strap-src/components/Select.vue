<template>
  <div class="vselect" :class="classes" v-click-outside="blur">
    <button type="button" class="form-control dropdown-toggle" :disabled="disabled || !hasParent" :readonly="readonly" @click="toggle()" @keyup.esc="show = false">
      <span v-if="loading" class="btn-content">{{showPlaceholder}}</span>
      <span v-else class="btn-content">
        <span v-show="multiple && selected.length>=2" class="selected-option selected-count">{{$lang.grid_total | replace(selected.length)}}</span>
        <select-option v-for="item in selected" :label="item" extra-class="selected-option"></select-option>
      </span>
      <span v-if="clearButton&&values.length" class="close" @click="clear()">&times;</span>
    </button>
    
    <select ref="sel" v-model="val" v-show="show" :name="name" class="secret" :multiple="multiple" :required="required" :readonly="readonly" :disabled="disabled">
      <option v-for="option in list" :value="option[optionsValue]">{{ option[optionsLabel] }}</option>
    </select>
    
    <ul class="dropdown-menu" v-show="show">
      <template v-if="list.length">
        <li v-if="canSearch || multiple" class="search-item">
          <flexy-button v-if="multiple" icon="square-o" class="btn-outline-default" @click.native="invertSelection()"/>
          <input v-if="canSearch" type="text" :placeholder="searchText||text.search" class="form-control" autocomplete="off" ref="search" v-model="searchValue" @keyup.esc="show = false" />
          <span v-if="list.length > minShow" class="search-totals">{{filteredAndMaxedOptions.length}}/{{list.length}}</span>
        </li>
        <li v-for="(option, index) in filteredAndMaxedOptions" :id="option[optionsValue]" :class="itemClass(index)">
          <flexy-button :icon="{'check-square-o':isSelected(option[optionsValue]),'square-o':!isSelected(option[optionsValue])}" class="btn-outline-default" @click.native="select(option[optionsValue])" />
          <flexy-button v-if="insert" icon="pencil" class="btn-outline-warning" @click.native="startEdit(option)" />
          <select-option :label="option[optionsLabel]" @dblclick.native="startEdit(option)"></select-option>
        </li>
        <li v-show="filteredOptions.length > this.showMax" class="pagination-item">
          <flexy-button :text="paginationText()" class="btn-outline-primary" @click.native="showAll()"/>
        </li>
      </template>
      <li v-if="insert" class="insert-item">
        <flexy-button @click.native="clickInsert()" icon="plus" class="btn-outline-warning" />{{insertText}}
      </li>
      <transition v-if="notify && !closeOnSelect" name="fadein"><div class="notify in">{{limitText}}</div></transition>
    </ul>
    <transition v-if="notify && closeOnSelect" name="fadein"><div class="notify out"><div>{{limitText}}</div></div></transition>
  </div>
</template>

<script>

import {translations}   from './utils/utils.js'
import ClickOutside     from '../directives/ClickOutside.js'
import flexyButton      from '../../components/flexy-button.vue'
import selectOption     from '../../components/form/select-option.vue'
import jdb              from '../../jdb-tools.js'
import flexyState       from '../../flexy-state.js'


var timeout = {}
export default {
  name : 'vselect',
  components: {flexyButton,selectOption},
  directives: {
    ClickOutside
  },
  props: {
    clearButton: {type: Boolean, default: false},
    closeOnSelect: {type: Boolean, default: false},
    disabled:  {type: Boolean, default: false},
    lang:  {type: String, default: navigator.language},
    limit: {type: Number, default: 999999},
    minSearch: {type: Number, default: 8},
    minShow: {type: Number, default: 10},
    maxShow: {type: Number, default: 20},
    multiple:  {type: Boolean, default: false},
    name:  {type: String, default: null},
    options: {type: [Array,Object], default () { return [] }},
    optionsLabel:  {type: String, default: 'label'},
    optionsValue:  {type: String, default: 'value'},
    optionsAjax:  {type: String, default: ''},
    primary:      {type: [String,Number], default: ''},
    parent:  {default: true},
    placeholder: {type: String, default: null},
    readonly:  {type: Boolean, default: null},
    required:  {type: Boolean, default: null},
    insert : {type:Boolean,default:false},
    insertText : {type:String,default:''},
    search:  {type: Boolean, default: false},
    searchText:  {type: String, default: null},
    url: {type: String, default: null},
    value: null
  },
  data () {
    return {
      list: [],
      loading: null,
      optionsAjaxLoaded: false,
      optionsAjaxApi:this.optionsAjax,
      searchValue: null,
      show: false,
      showMax : this.maxShow,
      editing:false,
      notify: false,
      val: null,
      valid: null,
    }
  },
  computed: {
    canSearch () { return (this.list.length > this.minSearch) },
    classes () { return [
      {
        'show': this.show,
        'disabled': this.disabled,
        'multiple': this.multiple,
      },
      this.class,
      'dropdown'
    ]},
    filteredOptions () {
      var self = this;
      var search = (self.searchValue || '').toLowerCase()
      var list = self.list;
      if (search) {
        list = self.list.filter( function(el){
          var label = el[self.optionsLabel];
          if (typeof(label)!=='string') label = label.toString();
          return (label.toLowerCase().search(search) >= 0) ;
        });
      }
      return list;
    },
    filteredAndMaxedOptions() {
      return this.filteredOptions.slice(0,this.showMax);
    },
    hasParent () { return this.parent instanceof Array ? this.parent.length : this.parent },
    limitText () { return this.text.limit.replace('{{limit}}', this.limit) },
    selected () {
      var values = this.values.map(val => (this.list.find(o => o[this.optionsValue] === val) || {})[this.optionsValue]).filter(val => val !== undefined);
      this.$emit('selected', values);
      if (this.list.length === 0) {
        return '';
      }
      var labels = this.values.map(val => (this.list.find(o => o[this.optionsValue] === val) || {})[this.optionsLabel]).filter(val => val !== undefined);
      labels.sort();
      return labels;
    },
    showPlaceholder () { return (this.values.length === 0 || !this.hasParent) ? (this.placeholder || this.text.notSelected) : null },
    text () { return translations(this.lang) },
    values () { return this.val instanceof Array ? this.val : ~[null, undefined].indexOf(this.val) ? [] : [this.val] },
    valOptions () { return this.list.map(el => el[this.optionsValue]) }
  },
  watch: {
    options (options) {
      if (this.optionsAjaxLoaded==false) {
        if (options instanceof Array) this.setOptions(options)
      }
    },
    show (val) {
      if (val) {
        this.$refs.sel.focus()
        this.$refs.search && this.$refs.search.focus()
      } else {
      }
    },
    url () {
      this.urlChanged()
    },
    valid (val, old) {
      this.$emit('isvalid', val)
      this.$emit(!val ? 'invalid' : 'valid')
      if (val !== old && this._parent) this._parent.validate()
    },
    value (val, old) {
      if (val !== old) { this.val = val }
    },
    val (val, old) {
      // Alleen emit als waarde bestaat en één van options is
      if (!_.isUndefined(val)) this.$emit('change', val);
    }
  },
  
  created () {
    this.setOptions(this.options)
    this.val = this.value
    this._select = true
    if (this.val === undefined || !this.parent) { this.val = null }
    if (!this.multiple && this.val instanceof Array) {
      this.val = this.val[0]
    }
    this.checkData()
    if (this.url) this.urlChanged()
    let parent = this.$parent
    while (parent && !parent._formGroup) { parent = parent.$parent }
    if (parent && parent._formGroup) {
      this._parent = parent
    }
  },
  mounted () {
    if (this._parent) this._parent.children.push(this)
  },
  beforeUpdate() {
    if (this.optionsAjax!==this.optionsAjaxApi) {
      this.loadAjaxOptions();
      this.optionsAjaxApi = this.optionsAjax;
    }
  },
  beforeDestroy () {
    if (this._parent) {
      var index = this._parent.children.indexOf(this)
      this._parent.children.splice(index, 1)
    }
  },
  
  methods: {
    blur () {
      this.show = false
    },
    checkData () {
      if (this.multiple) {
        if (this.limit < 1) { this.limit = 1 }
        if (!(this.val instanceof Array)) {
          this.val = (this.val === null || this.val === undefined) ? [] : [this.val]
        }
        var values = this.valOptions
        this.val = this.val.filter(el => ~values.indexOf(el))
        if (this.values.length > this.limit) {
          this.val = this.val.slice(0, this.limit)
        }
      } else {
        if (!~this.valOptions.indexOf(this.val)) { this.val = null }
      }
    },
    clear () {
      if (this.disabled || this.readonly) { return }
      this.val = this.val instanceof Array ? [] : null
      this.toggle()
    },
    clearSearch () {
      this.searchValue = ''
      this.$refs.search.focus()
    },
    itemClass(index) {
      return 'select-item-'+index;
    },
    isSelected (v) {
      return this.values.indexOf(v) > -1
    },
    select (v) {
      if (this.val instanceof Array) {
        if (~this.val.indexOf(v)) {
          var index = this.val.indexOf(v)
          this.val.splice(index, 1)
        } else {
          this.val.push(v)
        }
        if (this.closeOnSelect) {
          this.toggle()
        }
      } else {
        this.val = v
        this.toggle()
      }
    },
    invertSelection() {
      var self = this;
      for (var i = self.list.length - 1; i >= 0; i--) {
        self.select(self.list[i].value);
      }
    },
    loadAjaxOptions () {
      var self = this;
      if (self.optionsAjax!='') {
        // Load options
        var url = self.optionsAjax;
        if (self.primary>0) url += '&where='+self.primary;
        flexyState.api({
          url  : url,
        }).then(function(response){
          if (!_.isUndefined(response.data.data)) {
            var loadedOptions = response.data.data.data;
            self.setOptions(loadedOptions);
            self.optionsAjaxLoaded = true;
          }
        });
      }
    },
    setOptions (options) {
      this.list = this._mapOptions(options);
      this.$emit('options', this.list)
      // if (this.name=='id_adressen') {
      //   console.log(this.name);
      //   jdb.vueLog(this.list);
      // }
    },
    _mapOptions(options) {
      var self = this;
      options.map(el => {
        if (el instanceof Object) { return el }
        let obj = {}
        obj[self.optionsLabel] = el
        obj[self.optionsValue] = el
        return obj
      });
      return options;
    },
    toggle (event) {
      // Load Ajax options?
      if (this.optionsAjax!=='' && !this.optionsAjaxLoaded) {
        this.loadAjaxOptions();
      }
      this.show = !this.show
    },
    urlChanged () {
      if (!this.url || !this.$http) { return }
      this.loading = true
      this.$http.get(this.url).then(response => {
        var data = response.data instanceof Array ? response.data : []
        try { data = JSON.parse(data) } catch (e) {}
        this.setOptions(data)
        this.loading = false
        this.checkData()
      }, response => {
        this.loading = false
      })
    },
    validate () {
      return !this.required ? true : this.val instanceof Array ? this.val.length > 0 : this.val !== null
    },
    clickInsert: function() {
      if (this.insert) {
        this.show = false;
        this.$emit('insert', true);
        self.optionsAjaxLoaded = false;
      }
    },
    startEdit: function(item) {
      if (this.insert) {
        this.editing = item.value;
        this.show = false;
        this.$emit('update', item.value );
        self.optionsAjaxLoaded = false; 
      }
    },
    // cancelEdit: function(item) {
    //   console.log('cancelEdit',item.name,item.value);
    //   this.editing = false;
    // },
    // updateItem: function(event,item) {
    //   this.editing = false;
    //   if (item.value!==event.target.value) {
    //     var newItem = {
    //       'value' : item.value,
    //       'name'  : event.target.value, 
    //     }
    //     this.$emit('update', newItem );
    //   }
    // },
    paginationText() {
      var total = this.list.length;
      if (this.filteredOptions.length > this.showMax) total = this.filteredOptions.length;
      return this.text.show_all.replace('{total}', total);
    },
    showAll() {
      this.showMax = this.list.length;
    },

  },

}
</script>

<style scoped>
button.form-control.dropdown-toggle{
  height: auto;
  padding-right: 24px;
  max-height: 36px;
  overflow:hidden;
}
.selected-count {
  float:right!important;
}
button.form-control.dropdown-toggle:after{
  content: ' ';
  position: absolute;
  right: 1rem;
  top: 50%;
  margin: -1px 0 0;
  border-top: 4px dashed;
  border-top: 4px solid \9;
  border-right: 4px solid transparent;
  border-left: 4px solid transparent;
}
.bs-searchbox {
  position: relative;
/*  margin: 4px 8px;*/
}
.bs-searchbox .close {
  position: absolute;
  top: 0;
  right: 0;
  z-index: 2;
  display: block;
  width: 34px;
  height: 34px;
  line-height: 34px;
  text-align: center;
}
.bs-searchbox input:focus,
.secret:focus + button {
  outline: 0;
  border-color: #66afe9 !important;
  box-shadow: inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(102,175,233,.6);
}
.secret {
  border: 0;
  clip: rect(0 0 0 0);
  height: 1px;
  margin: -1px;
  overflow: hidden;
  padding: 0;
  position: absolute;
  width: 1px;
}
button>.close { margin-left: 5px;}
.notify.out { position: relative; }
.notify.in,
.notify>div {
  position: absolute;
  width: 96%;
  margin: 0 2%;
  min-height: 26px;
  padding: 3px 5px;
  background: #f5f5f5;
  border: 1px solid #e3e3e3;
  box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
  pointer-events: none;
}
.notify>div {
  top: 5px;
  z-index: 1;
}
.notify.in {
  opacity: .9;
  bottom: 5px;
}
.btn-group-justified .dropdown-toggle>span:not(.close) {
  width: calc(100% - 18px);
  display: inline-block;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  margin-bottom: -4px;
}
.btn-group-justified .dropdown-menu { width: 100%; }



.search-item {
  width: 100%;
  padding-bottom:5px!important;
  margin-bottom:0px;
  margin-top:2px;
  border-bottom:solid 1px;
}
.vselect.multiple .search-item input {
  width: calc(100% - 35px);
  margin-left:35px;
  margin-top:-26px;
}
.pagination-item {
  padding-left:4.5rem!important;
}

</style>