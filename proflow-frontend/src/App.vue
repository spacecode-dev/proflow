<template>
  <div id="app">
    <router-view/>
  </div>
</template>

<script>
export default {
  name: 'App',

  created () {
    // success
    this.$bus.$on('success', message => {
      this.showToast('success', 'Success', message)
    })
    // error
    this.$bus.$on('error', message => {
      this.showToast('danger', 'Error', message)
    })
  },

  methods: {
    showToast (variant, title, message) {
      this.$bvToast.toast(message, {
        title: title,
        variant: variant,
        solid: true
      })
    }
  }
}
</script>

<style lang="scss" scoped>
  ::v-deep {
    &#app {
      height: 100vh;
    }
  }
</style>
