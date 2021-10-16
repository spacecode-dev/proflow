import Vue from 'vue'
import Vuex from 'vuex'
import auth from '@/store/auth'
import settings from '@/store/settings'
import dashboard from '@/store/dashboard'
import issue from '@/store/issue'
import createPersistedState from 'vuex-persistedstate'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    auth,
    settings,
    dashboard,
    issue
  },
  plugins: [createPersistedState()]
})
