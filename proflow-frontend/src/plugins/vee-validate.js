import Vue from 'vue'
import {
  ValidationObserver,
  ValidationProvider,
  extend,
  localize
} from 'vee-validate'
import en from 'vee-validate/dist/locale/en.json'
import * as rules from 'vee-validate/dist/rules'

// Install VeeValidate rules and localization
Object.keys(rules).forEach(rule => {
  extend(rule, rules[rule])
})
extend('password', {
  params: ['target'],
  validate (value, { target }) {
    return value === target
  },
  message: 'Password confirmation does not match'
})
localize('en', en)

const ValidationState = {
  install: function (Vue) {
    Vue.prototype.getValidationState = ({ dirty, validated, valid = null }) => {
      return dirty || validated ? valid : null
    }
  }
}

Vue.use(ValidationState)

Vue.component('ValidationObserver', ValidationObserver)
Vue.component('ValidationProvider', ValidationProvider)
