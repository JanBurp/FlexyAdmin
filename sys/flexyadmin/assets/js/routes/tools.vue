<script>
import dbTool 		from '../tools/db.vue'
import fillTool 	from '../tools/fill.vue'
import searchTool from '../tools/search.vue'

export default {
  name: 'tools',
  components: {dbTool,searchTool,fillTool},
}
</script>

<template>
	<div>
		<div v-if="$route.params.tool==='db_export'"><db-tool action="export"></db-tool></div>
		<div v-if="$route.params.tool==='db_import'"><db-tool action="import"></db-tool></div>
		<div v-if="$route.params.tool==='db_backup'"><db-tool action="backup"></db-tool></div>
		<div v-if="$route.params.tool==='db_restore'"><db-tool action="restore"></db-tool></div>

		<div v-if="$route.params.tool==='fill'"><fill-tool></fill-tool></div>
		<div v-if="$route.params.tool==='search'"><search-tool></search-tool></div>
	</div>
</template>
