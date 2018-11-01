<script>
import flexyPlugin from '../components/flexy-plugin.vue'

export default {
  name: 'plugin',
    components: {flexyPlugin},
}
</script>

<template>
  <flexy-plugin :plugin="$route.params.plugin"></flexy-plugin>
</template>
