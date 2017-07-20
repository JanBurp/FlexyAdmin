<script>
import dbBackupTool 		from '../tools/db_backup.vue'
import dbRestoreTool 		from '../tools/db_restore.vue'
import dbExportTool 		from '../tools/db_export.vue'
import dbImportTool 		from '../tools/db_import.vue'
import fillTool 				from '../tools/fill.vue'
import searchTool 			from '../tools/search.vue'

export default {
  name: 'tools',
  components: {dbBackupTool,dbRestoreTool,dbExportTool,dbImportTool,searchTool,fillTool},
}
</script>

<template>
	<div>
		<div v-if="$route.params.tool==='db_export'"><db-export-tool></db-export-tool></div>
		<div v-if="$route.params.tool==='db_import'"><db-import-tool></db-import-tool></div>
		<div v-if="$route.params.tool==='db_backup'"><db-backup-tool></db-backup-tool></div>
		<div v-if="$route.params.tool==='db_restore'"><db-restore-tool></db-restore-tool></div>

		<div v-if="$route.params.tool==='fill'"><fill-tool></fill-tool></div>
		<div v-if="$route.params.tool==='search'"><search-tool></search-tool></div>
	</div>
</template>
