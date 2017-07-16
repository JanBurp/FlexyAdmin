<script>
export default {
  name: 'Db',
  props:['action'],
  
  data :function(){
    return {
      // helpItems   : {},
      // visibleItem : '',
    }
  },
  
  // created : function(){
  //   this.helpItems = JSON.parse(this.items);
  //   this.visibleItem = 'over_help'
  // },
  
}
</script>

<template>
  <div class="flexy-tool">
    <div class="card">
      <h1 class="card-header">Database Tools - {{action}}</h1>
      <div class="card-block">
      </div>
    </div>

  </div>
  
</template>
