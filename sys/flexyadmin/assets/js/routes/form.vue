<script>
import FlexyForm  from '../components/form/flexy-form.vue'

export default {
  name: 'form',
  components: {FlexyForm},
}
</script>

<template>
  <div>
  	<flexy-form :name="$route.params.table" :primary="$route.params.id" :formtype="$route.params.type?$route.params.type:'normal'"></flexy-form>
  </div>
</template>
