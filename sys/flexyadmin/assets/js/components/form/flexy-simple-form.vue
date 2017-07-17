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
    for (var field in this.fields) {
      this.data[field] = this.fields[field].value;
    }
  },

  beforeUpdate : function() {
    for (var field in this.fields) {
      this.data[field] = this.fields[field].value;
    }
  },


  methods : {

    update : function(name,value) {
      this.data[name] = value;
    },

    submit : function(event) {
      this.$emit('submit',this.data);
    },

  },

}
</script>

<template>
  <form @submit.prevent.stop="submit($event)">
    <flexy-form-field v-for="(field,name) in fields" :name="name" :type="field.type" :label="field.label" :value="field.value" @changed="update(name,$event)"></flexy-form-field>
    <button class="btn btn-primary" type="submit">Submit</button>
  </form>
</template>
