import store from '@/store'

export default {
  // Do not allow guest users to access auth routes
  //
  isLoggedIn: (to, from, next) => {
    if (store.getters.isLoggedIn) {
      next()
    } else {
      next({ name: 'login' })
    }
  },

  // Check all the steps are completed
  //
  checkStep: (to, from, next) => {
    if (!store.getters.isLoggedIn) {
      next({ name: 'login' })
    } else if (store.getters.user.user_detail.signup_step === 3) {
      next({ name: 'home' })
    } else if (store.getters.user.email_verified_at === null && to.name !== 'verify-email') {
      next({ name: 'verify-email', params: { email: store.getters.user.email } })
    } else if (store.getters.user.user_detail.signup_step === 0 && to.name !== 'company-detail') {
      next({ name: 'company-detail' })
    } else if (!to.params.forceRedirect && store.getters.user.user_detail.signup_step === 1 && to.name !== 'personal-detail') {
      next({ name: 'personal-detail' })
    } else if (!to.params.forceRedirect && store.getters.user.user_detail.signup_step === 2 && to.name !== 'invite-user') {
      next({ name: 'invite-user' })
    } else {
      next()
    }
  },

  // Do not allow logged-in users to access guest routes
  //
  isGuest: (to, from, next) => {
    if (store.getters.user.user_detail.signup_step === 3) {
      next({ name: 'home' })
    } else if (store.getters.user.email_verified_at === null && to.name !== 'verify-email') {
      next({ name: 'verify-email', params: { email: store.getters.user.email } })
    } else if (store.getters.user.user_detail.signup_step === 0 && to.name !== 'company-detail') {
      next({ name: 'company-detail' })
    } else if (!to.params.forceRedirect && store.getters.user.user_detail.signup_step === 1 && to.name !== 'personal-detail') {
      next({ name: 'personal-detail' })
    } else if (!to.params.forceRedirect && store.getters.user.user_detail.signup_step === 2 && to.name !== 'invite-user') {
      next({ name: 'invite-user' })
    } else if (store.getters.isLoggedIn) {
      next({ name: 'home' })
    } else {
      next()
    }
  }
}
