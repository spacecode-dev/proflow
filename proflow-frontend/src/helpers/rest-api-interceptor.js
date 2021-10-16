import store from '@/store'
import axios from 'axios'
import { bus } from '@/event-bus'
import { subdomain } from '@/helpers/subdomain'

const moment = require('moment-timezone')
const DEFAULT_ERROR_MESSAGE = 'Something went wrong'

// set headers
axios.defaults.headers.common.Accept = 'application/json'

axios.defaults.headers.common['Content-Type'] = 'application/json'
axios.defaults.headers.common.subdomain = subdomain // custom param
axios.defaults.headers.common.timezone = moment.tz.guess(true)

// set base url
axios.defaults.baseURL = process.env.VUE_APP_API_BASE_URL

// set header to request
axios.interceptors.request.use(
  config => {
    if (store.getters.isLoggedIn) {
      config.headers.common = {
        Authorization: `Bearer ${store.getters.user.token}`,
        subdomain: subdomain,
        Accept: 'application/json'
      }
    }
    return config
  },
  err => Promise.reject(err)
)

// get header from response
axios.interceptors.response.use(
  response => {
    // show 'success' toast if response.data has a message
    const message = response.data.message
    if (message) {
      bus.$emit('success', message)
    }

    return response
  },
  error => {
    // logout if it's 401
    if (error.response && error.response.status === 401) {
      store.dispatch('logout')
    }
    // show 'error' toast
    const message = (error.response && error.response.data.message) || DEFAULT_ERROR_MESSAGE
    bus.$emit('error', message)

    return Promise.reject(error)
  }
)
