import Vue from 'vue'
import GAuth from 'vue-google-oauth2'

Vue.use(GAuth, {
  clientId: process.env.VUE_APP_GOOGLE_CLIENT_ID,
  scope: 'profile email',
  prompt: 'select_account'
})
