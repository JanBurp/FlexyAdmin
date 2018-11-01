<script>

import flexyState       from './../flexy-state.js'

export default {
  name: 'dbBackupTool',
  
  data :function(){
    return {
      fields   : false,
      filename : '',
      sql      : '',
    }
  },

}
</script>

<template>
  <div class="flexy-tool flexy-tool-db">
    <div class="card">
      <h1 class="card-header">Backup</h1>
      <div class="card-body">
        <a href="_admin/load/plugin/db/backup" class="btn btn-warning"><span class="fa fa-download"></span>Download Backup</a>
      </div>
    </div>
  </div>
</template>
