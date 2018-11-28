<script>
import FlexyGrid    from '../components/grid/flexy-grid.vue'
import FlexyForm  from '../components/form/flexy-form.vue'

export default {
  name: 'edit',
  components: {FlexyGrid,FlexyForm},
  computed : {
  	message : function() {
  		var message = '';
  		var table = this.$route.params.table;
      // var id    = this.$route.params.id;
      // console.log(id);
  		if (table=='cfg_users') {
  			message = this.$lang.edit_users_warning;
  		}
  		return message;
  	},
  }

}
</script>

<template>
  <div>
    <flexy-grid v-if="!$route.params.id" type="table" api="table" limit="10" :name="$route.params.table" :autoresize="true"></flexy-grid>
    <flexy-form v-else :name="$route.params.table" :primary="$route.params.id" :formtype="$route.params.type?$route.params.type:'normal'" :message="message"></flexy-form>
  </div>
</template>
