<script>
export default {
  name: 'flexyButton',
  props:{
    'icon':{
      type: [String,Object],
      default:''
    },
    'class':{
      type: String,
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
    
    buttonClass : function() {
      var buttonClass='btn';
      if (this.iconComputed!=='')   buttonClass += ' btn-icon';
      if (this.text!=='')           buttonClass += ' btn-text';
      if (this.dropdown!=='')       buttonClass += ' dropdown-toggle';
      if (!this.border)             buttonClass += ' no-border';
      if (this.size)                buttonClass += ' btn-'+this.size;
      return buttonClass;
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
  <button @click="openDropdown" type="button" class="flexy-button" :class="buttonClass"><span v-if="iconComputed!==''" :class="iconClass" :disabled="buttonClass.indexOf('disabled')"></span><span v-if="text!==''">{{text}}</span></button>
</template>

<style>
  .dropdown {position:absolute!important;margin-left:.35rem;}
  .flexy-button.no-border {border-color:transparent;}
  .flexy-button.btn-icon {width:1.85rem;height:1.6rem;padding:.1rem 0 1.4rem;text-align:center;}
  .flexy-button.btn-icon.dropdown-toggle {width:3.2rem;}
  .flexy-button.btn-icon .fa {width:1rem;}
  .flexy-button.btn-text {width:auto!important;padding-right:.55rem;text-transform:uppercase;}
  .flexy-button.btn-lg {width:3.25rem;height:3.15rem;padding:.5rem .5rem .5rem 0;}
  .flexy-button.btn-lg .fa {font-size:2rem;}
</style>
