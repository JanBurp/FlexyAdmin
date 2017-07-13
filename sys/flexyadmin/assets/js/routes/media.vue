<script>
import FlexyGrid    from '../components/grid/flexy-grid.vue'

export default {
  name: 'media',
  components: {FlexyGrid},
}
</script>

<template>
  <div>
    <flexy-grid type="media" api="table" limit="10" :name="$route.params.path"></flexy-grid>
  </div>
</template>
