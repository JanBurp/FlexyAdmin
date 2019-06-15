<template>
  <li v-if="dropdown" class="nav-item dropdown">
    <a class="nav-link" :class="attr" :href="href">
      {{title}}
      <span class="dropdown-toggle dropdown-toggle-split" @click.stop.prevent="showDropdown"></span>
      <slot v-if="show"></slot>
    </a>
  </li>
  <li v-else class="nav-item">
    <a class="nav-link" :class="attr" :href="href">
      {{title}}
    </a>
  </li>
</template>

<script>
export default {
  name: 'dropdown',
  props : ['href','title','attr','dropdown'],
  data: function () {
    return {
      show : false,
    }
  },
  methods : {
    showDropdown : function(event) {
      this.show = ! this.show;
      if (this.show==false) {
        var url = event.target.href;
      }
    },
  },
};
</script>
