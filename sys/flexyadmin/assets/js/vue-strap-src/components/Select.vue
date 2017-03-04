<template>
  <div class="vselect" :class="classes" v-click-outside="blur">
    <button type="button" class="form-control dropdown-toggle" :disabled="disabled || !hasParent" :readonly="readonly" @click="toggle()" @keyup.esc="show = false">
      <span class="btn-content" v-html="loading ? text.loading : showPlaceholder || selected"></span>
      <span v-if="clearButton&&values.length" class="close" @click="clear()">&times;</span>
    </button>
    <select ref="sel" v-model="val" v-show="show" :name="name" class="secret" :multiple="multiple" :required="required" :readonly="readonly" :disabled="disabled">
      <option v-for="option in list" :value="option[optionsValue]">{{ option[optionsLabel] }}</option>
    </select>
    <ul class="dropdown-menu">
      <template v-if="list.length">
        <li v-if="canSearch" class="search-item">
          <input type="text" :placeholder="searchText||text.search" class="form-control" autocomplete="off" ref="search" v-model="searchValue" @keyup.esc="show = false" />
        </li>
        <li v-for="option in filteredOptions" :id="option[optionsValue]">
          <a @mousedown.prevent="select(option[optionsValue])">
            <flexy-button :icon="{'check-square-o':isSelected(option[optionsValue]),'square-o':!isSelected(option[optionsValue])}" class="btn-outline-default"/>
            <span v-html="option[optionsLabel]"></span>
          </a>
        </li>
        <li v-if="insert" class="insert-item">
          <flexy-button @click.native="clickInsert()" icon="plus" class="btn-outline-warning" />{{insertText}}
        </li>
      </template>
      <transition v-if="notify && !closeOnSelect" name="fadein"><div class="notify in">{{limitText}}</div></transition>
    </ul>
    <transition v-if="notify && closeOnSelect" name="fadein"><div class="notify out"><div>{{limitText}}</div></div></transition>
  </div>
</template>

<script>

import {translations}   from './utils/utils.js'
import ClickOutside     from '../directives/ClickOutside.js'
import flexyButton      from '../../components/flexy-button.vue'
import jdb              from '../../jdb-tools.js'


var timeout = {}
export default {
  name : 'vselect',
  components: {flexyButton},
  directives: {
    ClickOutside
  },
  props: {
    clearButton: {type: Boolean, default: false},
    closeOnSelect: {type: Boolean, default: false},
    disabled:  {type: Boolean, default: false},
    lang:  {type: String, default: navigator.language},
    limit: {type: Number, default: 8},
    minSearch: {type: Number, default: 8},
    multiple:  {type: Boolean, default: false},
    name:  {type: String, default: null},
    options: {type: Array, default () { return [] }},
    optionsLabel:  {type: String, default: 'label'},
    optionsValue:  {type: String, default: 'value'},
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
      searchValue: null,
      show: false,
      notify: false,
      val: null,
      valid: null
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
      return !search ? self.list : self.list.filter( function(el){
        return (el[self.optionsLabel].toLowerCase().search(search) >= 0) ;
      })
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
      return '<span class="selected-option">' + labels.join('</span><span class="selected-option">') + '</span>';
    },
    showPlaceholder () { return (this.values.length === 0 || !this.hasParent) ? (this.placeholder || this.text.notSelected) : null },
    text () { return translations(this.lang) },
    values () { return this.val instanceof Array ? this.val : ~[null, undefined].indexOf(this.val) ? [] : [this.val] },
    valOptions () { return this.list.map(el => el[this.optionsValue]) }
  },
  watch: {
    options (options) {
      if (options instanceof Array) this.setOptions(options)
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
    setOptions (options) {
      this.list = options.map(el => {
        if (el instanceof Object) { return el }
        let obj = {}
        obj[this.optionsLabel] = el
        obj[this.optionsValue] = el
        return obj
      })
      this.$emit('options', this.list)
    },
    toggle () {
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
      this.show = false;
      this.$emit('insert', true);
    },
  },

}
</script>

<style scoped>
button.form-control.dropdown-toggle{
  height: auto;
  padding-right: 24px;
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



</style>