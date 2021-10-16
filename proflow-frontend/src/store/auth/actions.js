import axios from 'axios'
import { domain, subdomain } from '@/helpers/subdomain'
import router from '@/router'

const actions = {
  async login ({ dispatch }, credentials) {
    const { data } = await axios.post('/login', credentials)

    const token = data.token
    const workspaceUrl = (data.company_detail) ? data.company_detail.workspace_url : ''
    if (subdomain && workspaceUrl && workspaceUrl !== subdomain) return window.location = `//${workspaceUrl}.${domain}/oauth/${token}` // eslint-disable-line no-return-assign
    await dispatch('setUserDetails', data)
    await dispatch('setUserGroupDetails', data.groups)
    return data
  },

  async googleAuth ({ dispatch }, credentials) {
    const { data } = await axios.post('/google-auth', credentials)

    const token = data.token
    const workspaceUrl = data.company_detail.workspace_url
    if (subdomain && workspaceUrl !== subdomain) return window.location = `//${workspaceUrl}.${domain}/oauth/${token}` // eslint-disable-line no-return-assign
    await dispatch('setUserDetails', data)
    return data
  },

  async gAuthRedirection (context) {
    return await axios.get('/auth/google')
  },
  async resendEmail (context, credentials) {
    return await axios.post('/email/resend', credentials)
  },

  async register (context, credentials) {
    return await axios.post('/register', credentials)
  },

  async getUserDetailByToken ({ dispatch }, token) {
    const { data } = await axios.get('/get-profile', {
      headers: { Authorization: `Bearer ${token}` }
    })
    await dispatch('setUserDetails', { ...data, token })
    return data
  },

  async saveCompanyDetail ({ dispatch }, companyDetail) {
    const { data } = await axios.post('/company', companyDetail)
    await dispatch('setUserDetails', data)
    return data
  },

  async saveUserDetail ({ dispatch }, userDetail) {
    const { data } = await axios.post('/update-user', userDetail)
    await dispatch('setUserDetails', data)
    return data
  },

  async inviteEmail ({ dispatch }, inviteDetail) {
    const { data } = await axios.post('/invite-email', inviteDetail)
    const token = data.token
    const workspaceUrl = (data.company_detail) ? data.company_detail.workspace_url : ''
    if (subdomain && workspaceUrl && workspaceUrl !== subdomain) return window.location = `//${workspaceUrl}.${domain}/oauth/${token}` // eslint-disable-line no-return-assign
    await dispatch('setUserDetails', data)
    await dispatch('setUserGroupDetails', data.groups)
    return data
  },

  async forgotPassword (context, email) {
    return await axios.post('/password/email', email)
  },

  async resetPassword (context, data) {
    return await axios.post('/password/reset', data)
  },

  setUserDetails ({ commit }, user) {
    commit('setUser', user)
  },

  logout ({ commit }) {
    commit('clearUser')
    return router.push({ name: 'login' })
  }
}

export default actions
