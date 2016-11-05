<template>
  <td v-if="type!=='hidden'"  :type="type" :name="name" value="value" :class="cellClass" :level="level">
    <span v-if="showTreeNode" class="fa fa-level-up fa-rotate-90 text-muted"></span>
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
  props:['type','name','value','level'],
  computed:{
    cellClass : function() {
      return 'grid-cell-type-'+this.type;
    },
    showTreeNode : function() {
      return (this.name==="str_title" && this.level>0);
    }
  },
}
</script>

<style>
  .grid td {overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:250px;}
  .grid td.grid-cell-type-checkbox {text-align:center;}
  /* tree, branches & nodes */
  .grid-type-tree tbody td[level="1"][name="str_title"] {padding-left:1rem;}
  .grid-type-tree tbody td[level="2"][name="str_title"] {padding-left:2rem;}
  .grid-type-tree tbody td[level="3"][name="str_title"] {padding-left:3rem;}
  .grid-type-tree tbody td[level="4"][name="str_title"] {padding-left:4rem;}
  .grid-type-tree tbody td[level="5"][name="str_title"] {padding-left:5rem;}
  .grid-type-tree tbody td[level="6"][name="str_title"] {padding-left:6rem;}
  .grid-type-tree tbody td[level="7"][name="str_title"] {padding-left:7rem;}
</style>