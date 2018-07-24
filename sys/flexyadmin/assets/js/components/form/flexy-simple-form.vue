<script>

import FlexyFormField  from './flexy-form-field.vue'

export default {
  name: 'Search',
  components: {FlexyFormField},
  props:{
    'fields':{
      type: [Array,Object],
      default: {},
    },
    'buttons' : {
      type: [Object],
      default: {'submit':'Submit'},
    }
  },
    
  data : function() {
    return {
      internalValues : {},
    }
  },

  created : function() {
    var self = this;
    var values = {};
    for (var field in this.fields) {
      var value = self.fields[field].value;
      values[field] = value;
    }
    self.internalValues = Object.assign( {}, values );
  },

  watch : {
    'internalValues' : function() {
      this.$emit('changed',this.internalValues);
    },
  },


  methods : {

    changed : function(field,$event) {
      this.$emit('changed',this.internalValues);
    },

    submit : function(event) {
      this.$emit('submit',this.internalValues);
    },

    isMultiple : function(field) {
      return this.fields[field].multiple;
    },

    show : function(field) {
      var show = true;
      if (!_.isUndefined(field.show)) {
        show = false;
        if (this.internalValues[field.show.field] == field.show.value) {
          show = true;
        }
      }
      return show;
    },

  },

}
</script>

<template>
  <form @submit.prevent.stop="submit($event)" class="flexy-simple-form">
    <template v-for="(field,name) in fields">
      <flexy-form-field v-show="show(field)" :name="name" :type="field.type" :label="field.label" :options="field.options" :multiple="isMultiple(name)" v-model="internalValues[name]" @input="changed(name,$event)"></flexy-form-field>
    </template>
    <template v-for="(name,type) in buttons">
      <button class="btn btn-primary" type="type">{{name}}</button>
    </template>
  </form>
</template>
