<template>
  <b-button @click="handleClickGAuthSignIn" class="btn-block" variant="light">
    <img src="@/assets/images/logo/google.svg" class="img-fluid mr-2" alt="google logo"/>
    <span class="pf-heading">Connect with google</span>
  </b-button>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
  name: 'ConnectWithGoogle',
  computed: {
    ...mapGetters(['isLoggedIn'])

  },
  watch: {
    isLoggedIn: function (val) {
    }
  },

  methods: {
    ...mapActions(['gAuthRedirection']),

    async handleClickGAuthSignIn () {
      const { data } = await this.gAuthRedirection()
      window.location.href = data.url
    },

    openWindow (url, title, options = {}) {
      if (typeof url === 'object') {
        options = url
        url = ''
      }

      options = { url, title, width: 600, height: 720, ...options }

      const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screen.left
      const dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screen.top
      const width = window.innerWidth || document.documentElement.clientWidth || window.screen.width
      const height = window.innerHeight || document.documentElement.clientHeight || window.screen.height

      options.left = ((width / 2) - (options.width / 2)) + dualScreenLeft
      options.top = ((height / 2) - (options.height / 2)) + dualScreenTop

      const optionsStr = Object.keys(options).reduce((acc, key) => {
        acc.push(`${key}=${options[key]}`)
        return acc
      }, []).join(',')

      const newWindow = window.open(url, title, optionsStr)
      if (this.isLoggedIn) {
        return newWindow.close()
      }
      if (window.focus) {
        newWindow.focus()
      }

      return newWindow
    }
  }
}
</script>

<style lang="scss" scoped>
  .btn-block {
    span {
      color: #0A0A0A;
    }
  }
</style>
