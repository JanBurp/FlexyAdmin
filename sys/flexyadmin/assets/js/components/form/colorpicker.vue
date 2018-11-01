<script>
export default {
  name: 'ColorPicker',
  props:{
    'value'   : String,
    'name'    : String,
  },
  computed : {
    name_input : function() {
      return this.name + '_input';
    },
    name_picker : function() {
      return this.name + '_picker';
    },
  },
  
  data : function() {
    return {
      color : this.value,
    };
  },
  
  methods : {
    
    changePicker : function(color) {
      this.changeColor(color);
    },

    changeColor : function(color) {
      this.color = color;
      this.$emit('input',this.color);
    },
    
  },
  
}
</script>

<template>
  <div class="colorpicker">
    <input class="form-control" :id="name_input" :name="name_input" :value="color" v-on:input="changeColor($event.target.value)">
    <input class="form-control form-picker" type="color" :id="name_picker" :name="name_picker" :value="color" v-on:input="changePicker($event.target.value)">
  </div>
</template>


<style>
  .colorpicker .form-control {width:6rem;margin-right:.5rem;float:left;}
  .colorpicker .form-picker {height:2.1rem;width:3rem;padding:0;margin-right:0;}
</style>

