<template>
  <span v-if="isCustomIcon" :class="[icon, `text-${variant}`, iconClass]"/>
  <b-icon v-else :icon="icon" :variant="variant" :class="iconClass"/>
</template>

<script>
export default {
  name: 'AppIcon',
  props: {
    icon: {
      type: String,
      require: true,
      default: ''
    },
    variant: {
      type: String,
      default: ''
    },
    iconClass: {
      type: String,
      default: 'mr-2'
    }
  },
  computed: {
    isCustomIcon () {
      return this.icon.indexOf('pf') > -1
    },
  }
}
</script>
