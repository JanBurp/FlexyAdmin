<script>
export default {
  name: 'flexyButton',
  props:{
    'icon':{
      type: [String,Object],
      default:''
    },
    'size': {
      type: String,
      default:''
    },
    'text':{
      type: String,
      default:''
    },
    'dropdown':{
      type:String,
      default:'',
    },
    'border': {
      type:[String,Boolean],
      default:false,
    },
  },
  
  computed: {
    
    iconComputed : function() {
      var iconComputed = this.icon;
      if (typeof(this.icon)==='object') {
        for (var icon in this.icon) {
          if (this.icon[icon]) iconComputed = icon;
        }
      }
      return iconComputed;
    },
    
    computedClass : function() {
      var computedClass='btn';
      if (this.iconComputed!=='')   computedClass += ' btn-icon';
      if (this.text!=='')           computedClass += ' btn-text';
      if (this.dropdown!=='')       computedClass += ' dropdown-toggle';
      if (!this.border)             computedClass += ' no-border';
      if (this.size)                computedClass += ' btn-'+this.size;
      return computedClass;
    },

    iconClass : function() {
      var iconClass = '';
      if (this.iconComputed!=='') {
        iconClass = 'fa fa-'+this.iconComputed;
      }
      return iconClass;
    },

  },
  
  methods :  {
    
    openDropdown : function() {
      if (this.dropdown!=='') {
        document.getElementById(this.dropdown).classList.toggle('open');
      }
    },
    
  },
  
}
</script>

<template>
  <button @click="openDropdown" type="button" class="flexy-button" :class="computedClass">
    <span v-if="iconComputed!==''" :class="iconClass" :disabled="computedClass.indexOf('disabled')"></span>
    <span v-if="text!==''" class="flexy-button-text">{{text}}</span>
  </button>
</template>

<style>
  .flexy-button {cursor:pointer;width:2rem;height:1.55rem;padding:.15rem .5rem 1rem .4rem;text-align:center;}
  .flexy-button.no-border {border-color:transparent;}
  .flexy-button.btn-outline-default {background-color:transparent;}
  .flexy-button.btn-icon.dropdown-toggle {width:3.2rem;}
  .flexy-button.btn-icon .fa {width:1rem;margin:0;}
  .flexy-button.btn-text {width:auto!important;padding-right:.55rem;}

  .flexy-button.btn-lg {width:3.25rem;height:3.15rem;padding:.5rem .5rem .5rem 0;}
  .flexy-button.btn-lg .fa {font-size:2rem;}

  .flexy-button.btn-xlg {width:5rem;height:5rem;padding:.7rem 2.5rem .7rem .5rem;}
  .flexy-button.btn-xlg .fa {font-size:3.5rem;}
  </style>
