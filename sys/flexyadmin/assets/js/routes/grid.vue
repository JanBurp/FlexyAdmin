<script>
import FlexyGrid    from '../components/grid/flexy-grid.vue'

export default {
  name: 'grid',
  components: {FlexyGrid},
  props:{},
}
</script>

<template>
  <div>
    GRID: {{ $route.params.table }}
    <flexy-grid type="table" api="table" limit="10" :name="$route.params.table" :title="$route.params.table"></flexy-grid>
  </div>
</template>
