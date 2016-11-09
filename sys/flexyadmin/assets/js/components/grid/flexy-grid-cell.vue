<script>
import flexyState from '../../flexy-state.js'

export default {
  name: 'VueGridCell',
  props:['type','name','primary','value','level','editable'],
  computed:{
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
    <template v-if="type=='text'">{{item}}</template>
    <template v-if="type=='wysiwyg'">{{item}}</template>
    <template v-if="type=='media'">{{item}}</template>
    <template v-if="type=='checkbox'">
      <span v-if="item" class="fa fa-check text-success" :value="item"></span>
      <span v-else class="fa fa-minus text-warning" :value="item"></span>
    </template>
    <template v-if="type=='select'">{{item}}</template>
  </td>
</template>


<style>
  .grid td {overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:250px;}
  .grid td.grid-cell-type-checkbox {text-align:center;}
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