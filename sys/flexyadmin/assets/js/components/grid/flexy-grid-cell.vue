<template>
  <td v-if="type!=='hidden'"  :type="type" value="value" :class="cellClass">
    <template v-if="type=='text'">{{value}}</template>
    <template v-if="type=='wysiwyg'">{{value}}</template>
    <template v-if="type=='media'">{{value}}</template>
    <template v-if="type=='checkbox'">
      <span v-if="value" class="fa fa-check text-success" :value="value"></span>
      <span v-else class="fa fa-minus text-warning" :value="value"></span>
    </template>
    <template v-if="type=='select'">{{value}}</template>
  </td>
</template>

<script>
export default {
  name: 'VueGridCell',
  props:['type','name','value'],
  computed:{
    cellClass : function() {
      return 'grid-cell-type-'+this.type;
    },
  },
}
</script>

<style>
  .grid td {overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:250px;}
  .grid td.grid-cell-type-checkbox {text-align:center;}
</style>