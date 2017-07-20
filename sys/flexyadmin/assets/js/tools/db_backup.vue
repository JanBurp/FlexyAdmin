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

  created : function() {
    var self = this;

    return flexyState.api({
      url : 'tools/db_backup',
    }).then(function(response){
      self.filename = response.data.data.filename;
      self.sql      = response.data.data.sql;
    });
  },


}
</script>

<template>
  <div class="flexy-tool flexy-tool-db">
    <div class="card">
      <h1 class="card-header">Backup</h1>
      <div v-if="filename!==''" class="card-block">
        <a :href="'data:text/plain;charset=utf-8,' + encodeURIComponent(sql)" :download="filename" class="btn btn-warning"><span class="fa fa-download"></span>Download Backup</a>
      </div>
    </div>
  </div>
</template>
