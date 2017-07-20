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

  },

}
</script>

<template>
  <form @submit.prevent.stop="submit($event)" class="flexy-simple-form">
    <template v-for="(field,name) in fields">
      <flexy-form-field :name="name" :type="field.type" :label="field.label" :options="field.options" :multiple="isMultiple(name)" v-model="internalValues[name]" @input="changed(name,$event)"></flexy-form-field>
    </template>
    <button class="btn btn-primary" type="submit">Submit</button>
  </form>
</template>
