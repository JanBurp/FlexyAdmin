<script>
export default {
  name: 'FlexyAccordion',
  props :{
    'items':Object,
  },
  data :function(){
    return {
      visibleItem : '',
    }
  },
  created : function() {
    this.visibleItem = Object.keys(this.items)[0];
  },
}
</script>

<template>
  <div class="flexy-accordion">
    <div class="card" v-for="(item,key) in items" :class="{'active':visibleItem==key}">
      <h2 @click="visibleItem=key" class="card-header"><span class="fa" :class="{'fa-chevron-down':visibleItem==key,'fa-chevron-right':visibleItem!=key}"></span>{{item.title}}</h2>
      <div v-show="visibleItem==key" class="card-block"v-html="item.content"></div>
    </div>
  </div>
</template>
