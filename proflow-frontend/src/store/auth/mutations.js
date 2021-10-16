const mutations = {
  setUser (state, user) {
    state.user = { token: state.user.token, ...user }
  },

  clearUser (state) {
    state.user = {
      user_detail: {},
      company_detail: {}
    }
  }
}

export default mutations
