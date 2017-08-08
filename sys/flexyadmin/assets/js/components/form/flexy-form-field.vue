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
      type: [Number,String,Boolean,Array,FileList],
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
      internalValue   : null,
    }
  },

  watch : {
    'value' : function() {
      if (this.value!==this.internalValue) {
        this.initInternalValue(this.value);
      }
    },
    'internalValue' : function() {
      this.$emit('input',this.internalValue);
    },
  },

  created : function() {
    this.initInternalValue(this.value);
  },

  methods: {

    initInternalValue : function(value) {
      var value = this.value;
      if (this.type=='select' && this.multiple) {
        if (value===null) {
          value = [];
        }
        else {
          switch (typeof(value)) {
            case 'undefined':
              value = [];
              break;
            default:
              value = [value];
              break;
          }
        }
      }
      this.internalValue = value;
    },
    
    title : function() {
      if (this.label==='') return this.name;
      return this.label;
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

    fileChange : function(event) {
      this.$emit('input',event);
    },

  },

}
</script>

<template>
  <div class="form-group row">
    <label class="col-md-3 form-control-label" :for="name">{{title()}}</label>
    <div class="col-md-9">
      <input v-if="type=='input'"     type="input"    class="form-control" :id="name" :name="name" :placeholder="placeholder" v-model="internalValue" />
      <input v-if="type=='checkbox'"  type="checkbox" class="form-control" :id="name" :name="name" v-model="internalValue" />
      <input v-if="type=='file'"      type="file"     class="form-control" :id="name" :name="name" @change="fileChange($event.target.files)" />
      <textarea v-if="type=='textarea'" class="form-control" :id="name" :name="name" :placeholder="placeholder" v-model="internalValue"></textarea>
      <select v-if="type=='select'" class="form-control" :id="name" :name="name" v-model="internalValue" :multiple="isMultiple()">
        <option v-for="option in options" :value="option.value" :selected="isSelected(option)">{{option.title||option.name}}</option>
      </select>
    </div>
  </div>  
</template>
