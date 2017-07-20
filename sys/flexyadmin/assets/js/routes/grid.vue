<script>
import FlexyGrid    from '../components/grid/flexy-grid.vue'

export default {
  name: 'grid',
  components: {FlexyGrid},
}
</script>

<template>
  <div>
    <flexy-grid type="table" api="table" limit="10" :name="$route.params.table" :autoresize="true"></flexy-grid>
  </div>
</template>
