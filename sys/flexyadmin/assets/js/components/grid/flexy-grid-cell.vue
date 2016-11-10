<script>
import flexyState from '../../flexy-state.js'

export default {
  name: 'VueGridCell',
  props:['type','name','primary','value','level','editable','options'],
  
  // created : function() {
  //   console.log(this.options);
  // },

  computed:{
    
    fieldTypes : function() {
      var types = {
        checkbox  : ['checkbox'],
        media     : ['media','medias'],
        color     : ['color'],
        url       : ['url'],
        relation  : ['relation'],
      };
      types.default = [].concat( types.checkbox, types.media, types.color, types.url );
      return types;
    },
    
    cellClass : function() {
      var c = [];
      c.push('grid-cell-type-'+this.type);
      if (this.editable) c.push('grid-cell-editable');
      return c;
    },
    showTreeNode : function() {
      return (this.name==="str_title" && this.level>0);
    },
  },
  
  data : function() {
    return {
      item : this.value,
    }
  },
  
  methods : {
    
    isType : function( type, fieldType ) {
      if (type==='default') {
        return this.fieldTypes['default'].indexOf(fieldType) === -1;
      }
      return this.fieldTypes[type].indexOf(fieldType) >= 0;
    },
    
    thumbs : function(media) {
      var array = media.split('|');
      for (var i = 0; i < array.length; i++) {
        array[i] = '_media/thumb/' + this.options['path'] +'/'+ array[i];
      }
      return array;
    },
    
    complementColor : function(color) {
      var complement = '#'+(0xffffff ^ color).toString(16);
      return complement;
    },
    
    relationItems : function(string) {
      var items = string.split(',');
      for (var i = 0; i < items.length; i++) {
        items[i] = items[i].replace(/{/,'').replace(/}/,'');
      }
      return items;
    },
    
    edit : function() {
      var self = this;
      if (this.editable) {
        switch(this.type) {

          case 'checkbox':
            var currentValue = self.item;
            var newValue = 1;
            if (currentValue) newValue=0;
            self.postField(newValue).then(function(response){
              if (!response.error) {
                self.item = newValue;
              }
            });
            break;
          
          default:
            var currentValue = self.item;
            // var newValue = ....
            // self.postField(newValue).then(function(response){
            //   if (!response.error) {
            //     self.item = newValue;
            //   }
            // });
            break;
        }
      }
    },
    
    postField : function(value) {
      var self=this;
      var data = {};
      data[self.name] = value;
      return this.api({
        url : 'row',
        'data': {
          'table'   : this.primary.table,
          'where'   : this.primary.id,
          'data'    : data,
        },
      }).then(function(response){
        if (!response.error) {
          if ( !_.isUndefined(response.data.info.validation) && response.data.info.validation===false) {
            flexyState.addMessage(response.data.info.validation_errors,'danger');
          }
        }
        else {
          flexyState.addMessage( self.$lang.vue_form_save_error,'danger');
        }
        return response;
      });
    },    
    
    
  },
  
}
</script>

<template>
  <td v-if="type!=='hidden'"  :type="type" :name="name" value="item" :class="cellClass" :level="level" v-on:click="edit()">
    <span v-if="showTreeNode" class="fa fa-level-up fa-rotate-90 text-muted"></span>

    <template v-if="isType('relation',type)">
      <span class="grid-relation-item" v-for="item in relationItems(item)">{{item}}</span>
    </template>

    <template v-if="isType('media',type)">
      <template v-if="item !==''"><img class="media-thumb-sm" v-for="img in thumbs(item)" :src="img"></template>
    </template>

    <template v-if="isType('color',type)">
      <div class="color-thumb-sm" :style="'color:'+complementColor(item)+';background-color:'+item">{{item}}</div>
    </template>

    <template v-if="isType('checkbox',type)">
      <span v-if="item" class="fa fa-check text-success" :value="item"></span>
      <span v-else class="fa fa-minus text-warning" :value="item"></span>
    </template>
    
    <template v-if="isType('url',type)">
      <a :href="item" target="_blank">{{item}}</a>
    </template>

    <template v-if="isType('default',type)">{{item}}</template>

  </td>
</template>


<style>
  .grid td {overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:250px;}
  .grid td.grid-cell-type-checkbox, .grid td.grid-cell-type-media {text-align:center;}
  .grid .color-thumb-sm {padding:0.125rem .5rem;margin:0;}
  .grid td.grid-cell-editable {cursor:pointer;}
  /* tree, branches & nodes */
  .grid-type-tree tbody td[level="1"][name="str_title"] {padding-left:1rem;}
  .grid-type-tree tbody td[level="2"][name="str_title"] {padding-left:2rem;}
  .grid-type-tree tbody td[level="3"][name="str_title"] {padding-left:3rem;}
  .grid-type-tree tbody td[level="4"][name="str_title"] {padding-left:4rem;}
  .grid-type-tree tbody td[level="5"][name="str_title"] {padding-left:5rem;}
  .grid-type-tree tbody td[level="6"][name="str_title"] {padding-left:6rem;}
  .grid-type-tree tbody td[level="7"][name="str_title"] {padding-left:7rem;}
</style>