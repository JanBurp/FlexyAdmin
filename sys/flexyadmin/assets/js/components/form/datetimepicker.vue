<script>
import datepicker       from '../../vue-strap-src/Datepicker.vue'
import timepicker       from './timepicker.vue'


export default {
  name: 'DateTimePicker',
  components: {timepicker,datepicker},
  props:{
    'value' : String,
    'name'  : String,
  },
  
  computed : {
    name_date : function() {
      return this.name + '_date';
    },
    name_time : function() {
      return this.name + '_time';
    },
  },
  
  data : function() {
    return {
      datetime : this.value,
    };
  },
  
  methods : {
    
    date : function() {
      return this.datetime.substr(0,10);
    },
    time : function() {
      return this.datetime.substr(11,8);
    },
    
    changeDate : function(date) {
      this.changeDateTime( date + ' ' + this.time() );
    },

    changeTime : function(time) {
      this.changeDateTime( this.date() + ' ' + time )
    },
    
    changeDateTime : function(datetime) {
      this.datetime = datetime;
      this.$emit('input',this.datetime);
    },
    
  },
  
}
</script>

<template>
  <div class="datetimepicker">
    <datepicker :id="name_date" :name="name_date" :value="date()" format="yyyy-MM-dd" @input="changeDate($event)"></datepicker>
    <timepicker :id="name_time" :name="name_time" :value="time()" @input="changeTime($event)"></timepicker>
  </div>
</template>

<style>
  .datetimepicker .datepicker {float:left;margin-right:1rem;}
  .datetimepicker .timepicker {float:left;}
</style>
