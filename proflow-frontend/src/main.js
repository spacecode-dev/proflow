import Vue from 'vue'
import store from '@/store'
import router from '@/router'

// plug-ins
import '@babel/polyfill'
import 'mutationobserver-shim'
import '@/plugins/bootstrap-vue'
import '@/plugins/vee-validate'
import '@/plugins/ckeditor'
import '@/plugins/mix-panel'
import '@/helpers/rest-api-interceptor'
import '@/plugins/shared-components'
import { bus } from '@/event-bus'

// css
import '@/assets/scss/style.scss'
import '@/assets/fonts/icomoon/style.css'

import App from '@/App.vue'

// register event bus
Object.defineProperties(Vue.prototype, {
  $bus: {
    get: function () {
      return bus
    }
  }
})

Vue.config.productionTip = false

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
