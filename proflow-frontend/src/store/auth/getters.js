const getters = {
  isLoggedIn: state => !!state.user.token,
  user: state => state.user
}

export default getters
