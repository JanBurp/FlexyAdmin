<script>

import flexyButton      from '../flexy-button.vue'
// import timepicker       from './timepicker.vue'
// import datetimepicker   from './datetimepicker.vue'
// import colorpicker      from './colorpicker.vue'
// import mediapicker      from './mediapicker.vue'
// import vselect           from '../../vue-strap-src/components/Select.vue'
// import datepicker       from '../../vue-strap-src/Datepicker.vue'

export default {
  name: 'FlexyFormField',
  components: {flexyButton},//,timepicker,datetimepicker,colorpicker,mediapicker,datepicker,vselect},
  props:{
    'name':String,
    'type':{
      type: String,
      default: 'input',
    },
    'label':{
      type: String,
      default: '',
    },
    'value' :{
      type: [Number,String,Boolean,Array],
      default: null,
    },
    'options' :{
      type: [Array,Object],
      default: null,
    },
    'multiple' :{
      type: [Boolean],
      default: false,
    },
    'placeholder' :{
      type: [Number,String,Boolean],
      default: null,
    },
    'validation'  :{
      type: String,
      default: '',
    },
  },

  data : function() {
    return {
      data        : this.value,
      isUpdating  : false,
    }
  },


  updated : function() {
    if (!this.isUpdating) this.data = this.value;
    this.isUpdating = false;
  },

  
  methods:{
    
    title : function() {
      if (this.label==='') return this.name;
      return this.label;
    },

    update : function(value) {
      this.data = value;
      this.isUpdating = true;
      this.$emit('changed',value);
    },

    isSelected : function(option) {
      var selected = '';
      if (option.value == this.value) selected = 'selected';
      return selected;
    },

    isMultiple : function() {
      var multiple = '';
      if (this.multiple) multiple = 'multiple';
      return this.multiple;
    },

  },

}
</script>

<template>
  <div class="form-group row">
    <label class="col-md-3 form-control-label" :for="name">{{title()}}</label>
    <div class="col-md-9">
      <input v-if="type=='input'"     type="input"    class="form-control" :id="name" :name="name" :placeholder="placeholder" :value="data" @input="update($event.target.value)" />
      <input v-if="type=='checkbox'"  type="checkbox" class="form-control" :id="name" :name="name" v-model="data" @click="update($event.target.checked)" />
      <input v-if="type=='file'"      type="file"     class="form-control" :id="name" :name="name" @change="update($event.target.value)" />

      <select v-if="type=='select'" class="form-control" :id="name" :name="name" v-model="data" :multiple="isMultiple()" @input="update($event.target.checked)">
        <option v-for="option in options" :value="option.value" :selected="isSelected(option)">{{option.title}}</option>
      </select>
    </div>
  </div>  
</template>
