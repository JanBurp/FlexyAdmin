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
      data : [],
    }
  },

  created : function() {
    var self = this;
    for (var field in this.fields) {
      var value = self.fields[field].value;
      // if (this.fields[field].multiple) {
      //   if (value===null) value = [];
      //   if (typeof(value)!=='string') value = [value];
      // }
      self.$set(self.data, field, value);
    }
  },

  // beforeUpdate : function() {
  //   var self = this;
  //   for (var field in self.fields) {
  //     var value = self.fields[field].value;
  //     if (self.data[field] !== value) {
  //       self.$set(self.data, field, value);
  //     }
  //   }
  // },

  updated : function() {
    var self = this;
    for (var field in self.fields) {
      var value = self.fields[field].value;
      if (self.data[field] !== value) {
        self.$set(self.data, field, value);
      }
    }
  },


  methods : {

    changed : function(field,value) {
      this.$set(this.data, field, value);
      this.$emit('changed',this.data);
    },

    submit : function(event) {
      this.$emit('submit',this.data);
    },

    isMultiple : function(field) {
      return this.fields[field].multiple;
    },

  },

}
</script>

<template>
  <form @submit.prevent.stop="submit($event)">
    <template v-for="(field,name) in fields">
      <flexy-form-field :name="name" :type="field.type" :label="field.label" :value="field.value" :options="field.options" :multiple="isMultiple(name)" @changed="changed(name,$event)"></flexy-form-field>
    </template>
    <button class="btn btn-primary" type="submit">Submit</button>
  </form>
</template>
