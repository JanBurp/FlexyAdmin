<template>
  <div :class="['card',panelType]">
    <div :class="['card-header',{'accordion-toggle':inAccordion}]" class="bg-primary text-white" @click.prevent="inAccordion&&toggle()">
      <slot name="header"><h1 class="card-title text-white">{{ header }}</h1></slot>
    </div>
    <transition name="collapse">
      <div class="card-collapse" v-if="open">
        <div class="card-block">
          <slot></slot>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  props: {
    header: {type: String},
    isOpen: {type: Boolean, default: null},
    type: {type: String, default : null}
  },
  data() {
    return {
      open: this.isOpen
    }
  },
  watch: {
    isOpen( val ) {
      this.open = val
    }
  },
  computed: {
    inAccordion () { return this.$parent && this.$parent._isAccordion },
    panelType () { return 'card-' + (this.type || (this.$parent && this.$parent.type) || 'default') }
  },
  methods: {
    toggle () {
      this.open = !this.open
      this.$emit('open', this)
    }
  },
  transitions: {
    collapse: {
      afterEnter (el) {
        el.style.maxHeight = ''
        el.style.overflow = ''
      },
      beforeLeave (el) {
        el.style.maxHeight = el.offsetHeight + 'px'
        el.style.overflow = 'hidden'
        // Recalculate DOM before the class gets added.
        return el.offsetHeight
      }
    }
  },
  created () {
    if (this.isOpen === null) {
      this.open = !this.inAccordion
    }
  }
}
</script>