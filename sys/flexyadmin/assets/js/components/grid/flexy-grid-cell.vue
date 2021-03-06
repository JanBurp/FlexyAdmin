<script>

var he = require('he');

import jdb          from '../../jdb-tools.js'
import flexyState   from '../../flexy-state.js'
import flexyThumb   from '../flexy-thumb.vue'
import flexyVideo   from '../flexy-video.vue'
import flexyButton  from '../flexy-button.vue'

export default {
  name: 'FlexyGridCell',
  components: {flexyThumb,flexyVideo,flexyButton},
  props:{
    'type':String,
    'name':String,
    'primary':Object,
    'value':[Number,String,Boolean],
    'valuecomplete':{
      type:[Object,Array],
      default:null,
    },
    'level':Number,
    'editable':Boolean,
    'readonly':Boolean,
    'options':Object,
    'assetOptions' :{
      type : Object,
      default : {},
    },
    'focus':Boolean,
  },

  created : function() {
    // console.log('grid-cell',this.name,this.type);
  },

  computed:{

    inputID : function() {
      return jdb.createUUID();
    },

    fieldTypes : function() {
      var types = {
        checkbox    : ['checkbox'],
        media       : ['media','medias'],
        video       : ['video'],
        color       : ['color'],
        url         : ['url'],
        relation    : ['relation'],
        select      : ['select','radio'],
        abstract    : ['abstract'],
      };
      var defaultTypes = [];
      for(var type in types) {
        defaultTypes = defaultTypes.concat(types[type]);
      }
      types.default = defaultTypes;
      return types;
    },

    showTreeNode : function() {
      if (_.isUndefined(this.options)) return false;
      if (_.isUndefined(this.options.is_tree_field)) return false;
      return (this.options.is_tree_field===true && this.level>0);
    },

    scaleOptions : function() {
      if (this.assetOptions.scale) {
        var scale = this.assetOptions.scale.split(':');
        scale = Math.round(scale[0]/scale[1]*100)/100;
        return scale;
      }
      return false;
    },

    imgSize : function() {
      if (this.valuecomplete.width) {
        return {
          width:  this.valuecomplete.width.value,
          height: this.valuecomplete.height.value,
          scale:  Math.round(this.valuecomplete.width.value / this.valuecomplete.height.value * 100)/100,
          size:   this.valuecomplete.size.value,
        }
      }
      return {};
    },

    isFolder() {
        if ( _.isUndefined(this.valuecomplete.type) ) return false;
        if ( this.valuecomplete.type.value == 'dir') return true;
        return false;
    },

  },

  data : function() {
    return {
      item      : this.value,
      oldItem   : this.value,
      isEditing : false,
      showAll   : false,
    }
  },

  methods : {

    cellClass : function() {
      var c = [];
      c.push('grid-cell-type-'+this.type);
      if (this.editable) c.push('grid-cell-editable');
      if (this.readonly) c.push('text-muted');
      if (this.focus) c.push('has-focus');
      if (this.isEditing) c.push('is-editing');
      if (this.showAll) c.push('show-all');
      if (!_.isUndefined(this.options) && !_.isUndefined(this.options.is_tree_field) && this.options.is_tree_field) c.push('is-tree-field');
      return c;
    },

    isType : function( type, fieldType ) {
      var is = false;
      if (type==='default') {
        is = (this.fieldTypes['default'].indexOf(fieldType) === -1);
      }
      else {
        is = (this.fieldTypes[type].indexOf(fieldType) >= 0);
      }
      return is;
    },

    thumbs : function(media) {
      var path = this.options.path;
      var array = [];
      var src = '';
      var alt = '';
      if (typeof(media)=='number') {
        media += '';
      }
      if (typeof(media)=='string') {
        array = media.split('|');
        for (var i = 0; i < array.length; i++) {
          src = _flexy.media+'thumb/' + path +'/'+ array[i];
          if ( _.isUndefined(this.valuecomplete) && _.isUndefined(this.valuecomplete.alt)) {
            alt = this.valuecomplete.alt.value;
          }
          else {
            alt = src;
          }
          array[i] = {
            src : src,
            alt : alt,
          }
        }
      }
      return array;
    },

    decode : function(item) {
      if (_.isUndefined(item) || typeof(item)!=='string') {
        return item;
      }
      return he.decode(item,{});
    },

    complementColor : function(color) {
      return jdb.complementColor(color);
    },

    relationItems : function(string) {
      var items = string.split(',');
      for (var i = 0; i < items.length; i++) {
        items[i] = items[i].replace(/{/,'').replace(/}/,'').trim();
      }
      return items;
    },

    showAllItemsToggle : function() {
      this.showAll = !this.showAll;
    },

    selectItem : function (value) {
      if (!value) return '';
      if (value.substr(0,1)==='{' && value.substr(-1,1)==='}') {
        value = JSON.parse(value);
        var keys = Object.keys(value);
        value = value[keys[0]];
      }
      value = value.toString();
      return value;
    },

    itemObject : function(item) {
      if (typeof(item)==='string' && item.substr(0,1)==='{') {
        item = JSON.parse(item);
      }
      return item;
    },

    select : function() {
        if ( this.isFolder ) {
            this.$emit('select_folder');
        }
        else {
            this.$emit('select');
        }
    },

    saveEdit : function(value) {
      var self = this;
      self.isEditing = false;
      if (value!==this.oldItem) {
        self.postField(value).then(function(response){
          if (response.error) {
            self.cancelEdit();
          }
          else {
            self.item = value;
          }
        });
      }
    },

    startEdit : function() {
      console.log('startEdit');
      if (this.editable && !this.readonly) {
        this.isEditing = true;
        this.oldItem = this.item;
        var inputEL = document.getElementById(this.inputID);
        inputEL.style.display= "block";
        inputEL.focus();
      }
    },

    cancelEdit : function() {
      this.item = this.oldItem;
      this.isEditing = false;
    },

    clickEdit : function() {
      var self = this;
      var currentValue = self.item;
      if (this.editable && this.type==='checkbox') {
        var newValue = 1;
        if (currentValue) newValue=0;
        self.postField(newValue).then(function(response){
          if (!response.error) {
            self.item = newValue;
          }
        });
      }
    },

    postField : function(value) {
      var self=this;
      var data = {};
      data[self.name] = value;
      return flexyState.api({
        url : 'row',
        'data': {
          'table'   : this.primary.table,
          'where'   : this.primary.id,
          'data'    : data,
        },
      }).then(function(response){
        if (!response.error) {
          if ( !_.isUndefined(response.data.info.validation) && response.data.info.validation===false) {
            // Validaiton error
            response.error = true;
            for (var error in response.data.info.validation_errors) {
              flexyState.addMessage(response.data.info.validation_errors[error],'danger');
            }
          }
          else {
            // Ok!
            flexyState.addMessage( self.$lang.grid_edit_success);
          }
        }
        else {
          // Error
          flexyState.addMessage( self.$lang.form_save_error,'danger');
        }
        return response;
      });
    },


  },

};
</script>

<template>
  <td v-if="type!=='hidden'" :type="type" :name="name" :value="item" :class="cellClass()" :level="level" @keyup.esc="cancelEdit()">

    <span v-if="showTreeNode" class="fa fa-level-up fa-rotate-90 text-muted"></span>

    <template v-if="isType('relation',type)">
      <div v-for="item in relationItems(item)" @click="showAllItemsToggle()">
        <span class="grid-relation-item" v-if="item!==''" v-html="selectItem(item)"></span>
      </div>
    </template>

    <template v-if="isType('abstract',type)">
      <span v-for="(abstract,key) in itemObject(item)">{{abstract}}</span>
    </template>

    <template v-if="isType('media',type)">
      <template v-if="item !==''">
        <flexy-thumb @click.native="select()" v-for="img in thumbs(item)" :key="img.src" :src="img.src" :alt="img.alt" :scale="scaleOptions" :sizes="imgSize" :isFolder="isFolder" />
      </template>
    </template>

    <template v-if="isType('video',type)">
      <template v-if="item !==''">
        <flexy-video :video="item" />
      </template>
    </template>


    <template v-if="isType('color',type)">
      <div class="color-thumb-sm" :style="'color:'+complementColor(item)+';background-color:'+item">{{item}}</div>
    </template>

    <template v-if="isType('checkbox',type)">
      <div @click="clickEdit">
        <span v-if="item" class="fa fa-check text-success" :value="item"></span>
        <span v-else class="fa fa-minus text-warning" :value="item"></span>
      </div>
    </template>

    <template v-if="isType('url',type)">
      <a :href="item" target="_blank">{{item}}</a>
    </template>

    <template v-if="isType('select',type)">
      <span v-html="selectItem(item)"></span>
    </template>

    <template v-if="isType('default',type)">
      <span v-show="!isEditing" @click="startEdit()">{{decode(item)}}</span>
      <input v-if="editable" v-show="isEditing" :id="inputID" :value="item" @change="saveEdit($event.target.value)" @keyup.esc="cancelEdit()"/>
    </template>

  </td>
</template>
