<template>
  <div><slot></slot></div>
</template>

<script>
export default {
  props: {
    type: {
      type: String,
      default: null
    },
    oneAtAtime: {
      type: Boolean,
      default: true
    }
  },
  created () {
    this._isAccordion = true
    this.$on('isOpenEvent', child => {
      if (this.oneAtAtime) {
        this.$children.forEach(item => {
          if (child !== item) {
            item.isOpen = false
          }
        })
      }
    })
  }
}
</script>
