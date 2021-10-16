<template>
  <div class="d-flex align-items-center">
    <img
      src="@/assets/images/logo/logo-blue.svg"
      alt="log-blue"
      class="img-fluid"
      :class="{ 'cursor-pointer' : clickable }"
      :style="{ 'height': `${height}px`, 'width': `${width}px` }"
      @click="goToHome"
    >
    <span v-if="withTitle" class="ml-2 font-weight-bold brand-name">{{ appTitle }}</span>
  </div>
</template>

<script>
export default {
  name: 'AppLogo',
  props: {
    clickable: {
      type: Boolean,
      default: false
    },
    height: {
      type: [Number, String],
      default: 96
    },
    width: {
      type: [Number, String],
      default: 96
    },
    withTitle: {
      type: Boolean,
      default: true
    }
  },
  computed: {
    appTitle () {
      return process.env.VUE_APP_TITLE
    }
  },
  methods: {
    goToHome () {
      if (!this.clickable || this.$route.name === 'home') return
      this.$router.push({ name: 'home' })
    }
  }
}
</script>
