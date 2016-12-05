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
    'text':{
      type: String,
      default:''
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
      if (this.iconComputed!=='') buttonClass += ' btn-icon';
      if (this.text!=='') buttonClass += ' btn-text';
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
  
}
</script>

<template>
  <button type="button" class="flexy-button" :class="buttonClass"><span v-if="iconComputed!==''" :class="iconClass"></span><span v-if="text!==''">{{text}}</span></button>
</template>

<style>
  .flexy-button.disabled {opacity:.3;}
  .flexy-button.btn-icon {width:1.85rem;height:1.6rem;padding:.1rem 0 1.4rem;text-align:center;}
  .flexy-button.btn-icon .fa {width:1rem;}
  .flexy-button.btn-text {width:auto!important;padding-right:.55rem;text-transform:uppercase;}
</style>
