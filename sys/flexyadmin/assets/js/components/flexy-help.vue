<script>
export default {
  name: 'FlexyHelp',
  props:['items'],
  
  data :function(){
    return {
      helpItems   : {},
      visibleItem : '',
    }
  },
  
  created : function(){
    this.helpItems = JSON.parse(this.items);
    this.visibleItem = 'over_help'
  },
  
}
</script>

<template>
  <div class="flexy-help row">

    <div class="flexy-help-menu col-sm-3">
      <ul class="nav nav-pills flex-column">
        <li v-for="(item,key) in helpItems" class="nav-item"><a @click="visibleItem=key" class="nav-link" :class="{'active':key===visibleItem}">{{item.title}}</a></li>
      </ul>
    </div>
    
    <div class="flexy-help-content col-sm-9">
      <div v-for="(item,key) in helpItems" v-show="key===visibleItem" class="card">
        <h1 class="card-header bg-primary text-white">{{item.title}}</h1>
        <div class="card-block"v-html="item.content"></div>
      </div>
    </div>

  </div>
  
</template>
