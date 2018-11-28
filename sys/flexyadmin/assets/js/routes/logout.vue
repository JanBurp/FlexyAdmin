<script>
import flexyState from '../flexy-state.js'
export default {
  name: 'logout',

  created : function() {
  	return flexyState.api({
    	url : 'auth/logout'
    }).then(function(response){
    	location.href = '_admin';
      return response;
    });
  },

}
</script>

<template>
  <div></div>
</template>
