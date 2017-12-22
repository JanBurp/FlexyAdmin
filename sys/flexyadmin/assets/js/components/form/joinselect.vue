<script>

import flexyState       from '../../flexy-state.js'
import flexyButton      from '../flexy-button.vue'

export default {
  name: 'Joinselect',
  components: {flexyButton},
  props:{
    'value'   : Array,
  },
  
  data : function() {
    return {
      data    : [],
      fields  : [],
      default : {},
    };
  },

  
  created : function() {
    var self = this;
    self.data = self.value;
    var first = _.clone(self.data[0]);
    self.fields = Object.keys(first);
    self.default = first;
    _.each(self.default,function(value,field){
      if (field=='id') {
        value = -1;
      }
      else {
        value = '';
      }
      self.default[field] = value;
    }); 
  },

  beforeUpdate : function() {
    this.data = this.value;
  },


  methods : {

    label : function(field) {
      return field.replace(/.*_/gi, "");
    },

    addItem : function() {
      this.data.push(_.clone(this.default));
      this._emit();
    },

    changedItem : function(index,field,value) {
      this.data[index][field] = value;
      this._emit();
    },

    removeItem : function(index) {
      var self = this;
      // check if it is empty
      if (self.isEmpty(index)) {
        self._removeItem(index);
      }
      else {
        flexyState.openModal( {'title':'','body':self.$lang['confirm_delete_one'],'size':'modal-sm'}, function(event) {
          if ( event.state.type==='ok') {
            self._removeItem(index);
          }
        });
      }
    },
    _removeItem : function(index) {
      var self = this;
      self.data.splice(index,1);
      // If no items exists, add one empty
      if (self.data.length===0) {
        self.addItem();
      }
      this._emit();
    },

    _emit : function() {
      // var self = this;
      // var emitData = _.clone(self.data);
      // // cleanup empty data
      // for (var index = self.data.length - 1; index >= 0; index--) {
      //   if ( self.isEmpty(index) ) {
      //     emitData.splice(index,1);
      //   }
      // }
      // console.log(emitData);
      this.$emit('change',this.data);
    },

    isEmpty : function(index) {
      var empty = true;
      _.each(this.data[index],function(value,field){
        if (value!=='' && field!=='id') {
          empty = false;
        }
      });
      return empty;
    },

    
  },
  
}
</script>

<template>
  <div class="joinselect">
    <div class="joinselect-group row" :class="{'last':index==data.length-1}" v-for="(item,index) in data" :index="index" :data-id="item.id">
      <div v-for="(value,field) in item" v-if="field!=='id'" class="joinselect-item col-md-10">
        <div :data-field="field" class="form-group form-group-small">
          <input :value="value" class="form-control" :placeholder="label(field)" @change="changedItem(index,field,$event.target.value)">
        </div>
      </div>
      <div class="col-md-1">
        <flexy-button icon="remove" class="btn-outline-danger" @click.native="removeItem(index)" />
      </div>
      <div class="col-md-1">
        <flexy-button v-show="index==value.length-1" icon="plus" class="btn-outline-warning"  @click.native="addItem()"/>
      </div>
    </div>
  </div>
</template>

<style>
  .joinselect-group {margin-bottom:.5rem;}
  .joinselect-group.last {margin-bottom:0;}
  .joinselect-item .form-group {margin-bottom:.25rem;}

</style>
