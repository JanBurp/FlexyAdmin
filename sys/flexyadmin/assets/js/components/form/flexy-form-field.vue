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
      type: [Number,String,Boolean],
      default: null,
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
    </div>
  </div>  
</template>
