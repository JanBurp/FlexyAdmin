<script>
export default {
  name: 'TimePicker',
  props:{
    'value' : String,
    'name'  : String,
  },
  
  computed : {
    name_hours : function() {
      return this.name + '_hours';
    },
    name_minutes : function() {
      return this.name + '_minutes';
    },
  },
  
  data : function() {
    return {
      time : this.value,
    };
  },
  
  methods : {
    
    hours : function() {
      return this.time.substr(0,2);
    },
    minutes : function() {
      return this.time.substr(3,2);
    },
    
    range : function(len) {
      var range = [];
      for (var i = 0; i <= len; i++) {
        range.push({
          value : i,
          text  : this.precedingZero(i),
        });
      }
      return range;
    },
    
    precedingZero : function(num) {
      var s = num.toString();
      if (num<10) s='0'+s;
      return s;
    },
    
    changeHours : function(hours) {
      this.changeTime( this.precedingZero(hours) + ':' + this.minutes() );
    },

    changeMinutes : function(minutes) {
      this.changeTime( this.hours() + ':' + this.precedingZero(minutes) )
    },
    
    changeTime : function(time) {
      this.time = time + ':00';
      this.$emit('input',this.time);
    },
    
  },
  
}
</script>

<template>
  <div class="timepicker">
    <select class="custom-select timepicker-hours" :id="name_hours" :value="hours()" v-on:input="changeHours($event.target.value)">
      <option v-for="hour in range(23)" :selected="(hour.value==hours())" :value="hour.value">{{hour.text}}</option>
    </select>
    :
    <select class="custom-select timepicker-minutes" :id="name_minutes" :value="minutes()" v-on:input="changeMinutes($event.target.value)">
      <option v-for="minute in range(59)" :selected="(minute.value==minutes())" :value="minute.value">{{minute.text}}</option>
    </select>
  </div>
</template>


<style>
  .timepicker-hours, .timepicker-minutes {padding-left:.25rem;padding-right:.25rem;}
</style>

