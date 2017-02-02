<script>

import jdb          from '../../jdb-tools.js'
import flexyState   from '../../flexy-state.js'
import flexyThumb   from '../flexy-thumb.vue'
import flexyButton  from '../flexy-button.vue'

export default {
  name: 'VueGridCell',
  components: {flexyThumb,flexyButton},
  props:['type','name','primary','value','level','editable','readonly','options','focus'],
  
  // created : function() {
  //   console.log(this.options);
  // },

  computed:{
    
    inputID : function() {
      return jdb.createUUID();
    },
    
    fieldTypes : function() {
      var types = {
        checkbox  : ['checkbox'],
        media     : ['media','medias'],
        color     : ['color'],
        url       : ['url'],
        relation  : ['relation'],
        select    : ['select'],
        abstract  : ['abstract'],
      };
      var defaultTypes = [];
      for(var type in types) {
        defaultTypes = defaultTypes.concat(type);
      }
      types.default = defaultTypes;
      return types;
    },
    
    showTreeNode : function() {
      return (this.name==="str_title" && this.level>0);
    },
  },
  
  data : function() {
    return {
      item      : this.value,
      oldItem   : this.value,
      isEditing : false,
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
      if (_.isUndefined(path)) path=this.options.schema.path;
      var array = media.split('|');
      for (var i = 0; i < array.length; i++) {
        array[i] = {
          src : '_media/thumb/' + path +'/'+ array[i],
          alt : array[i],
        }
      }
      return array;
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
    
    selectItem : function (value) {
      if (!value) return '';
      value = value.toString();
      return value.replace(/\|/g,'<span class="grid-cell-seperator">|</span>');
    },
    
    itemObject : function(item) {
      if (typeof(item)!=='Object') {
        item = JSON.parse(item);
      }
      return item;
    },
    
    select : function() {
      this.$emit('select');
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
  
}
</script>

<template>
  <td v-if="type!=='hidden'" :type="type" :name="name" :value="item" :class="cellClass()" :level="level" @keyup.esc="cancelEdit()">
    <span v-if="showTreeNode" class="fa fa-level-up fa-rotate-90 text-muted"></span>

    <template v-if="isType('relation',type)">
      <template v-for="item in relationItems(item)">
        <span class="grid-relation-item" v-if="item!==''">{{item}}</span>
      </template>
    </template>

    <template v-if="isType('abstract',type)">
      <span v-for="(abstract,key) in itemObject(item)">{{abstract}}</span>
    </template>

    <template v-if="isType('media',type)">
      <template v-if="item !==''">
        <flexy-thumb @click.native="select()"  v-for="img in thumbs(item)" :src="img.src" :alt="img.alt">
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
      <span v-show="!isEditing" @click="startEdit()">{{item}}</span>
      <input v-if="editable" v-show="isEditing" :id="inputID" :value="item" @change="saveEdit($event.target.value)" @keyup.esc="cancelEdit()"/>
    </template>

  </td>
</template>
