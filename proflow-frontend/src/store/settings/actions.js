import axios from 'axios'
import router from '@/router'

const actions = {
  async updateProfile ({ dispatch, commit }, updateProfile) {
    const config = {
      headers: {
        'content-type': 'multipart/form-data'
      },
      onUploadProgress: function (progressEvent) {
        const uploadPercentage = parseInt(Math.round((progressEvent.loaded / progressEvent.total) * 100))
        commit('setUploadProgress', uploadPercentage)
      }
    }
    const { data } = await axios.post('/update-account', updateProfile, config)
    await dispatch('setUserDetails', data)
    return data
  },

  async updatePassword (context, credentials) {
    return await axios.post('/update-password', credentials)
  },

  async getTimezoneList (context) {
    const { data } = await axios.get('/get-timezone')
    return data
  },

  async updateCompanyDetail ({ dispatch, commit }, companyDetail) {
    const config = {
      headers: {
        'content-type': 'multipart/form-data'
      },
      onUploadProgress: function (progressEvent) {
        const uploadPercentage = parseInt(Math.round((progressEvent.loaded / progressEvent.total) * 100))
        commit('setUploadProgress', uploadPercentage)
      }
    }
    const { data } = await axios.post(`/company/${companyDetail.get('id')}`, companyDetail, config)
    await dispatch('setUserDetails', data)
  },

  async getMembersList (context) {
    const { data } = await axios.get('/get-members')
    return data
  },

  async addMember (context, memberData) {
    const { data } = await axios.post('/add-member', memberData)
    return data
  },

  async removeMember (context, memberData) {
    const { data } = await axios.post('/remove-member', memberData)
    return data
  },

  async changeRoleMember (context, memberData) {
    const { data } = await axios.post('/change-member-role', memberData)
    return data
  },

  toggleModalSetting ({ commit, getters }, data) {
    if (getters.isLoggedIn) {
      return new Promise((resolve) => {
        commit('toggleModalSetting', data)
        resolve()
      })
    } else {
      return router.push({ name: 'login' })
    }
  },

  toggleModalGroup ({ commit, getters }, data) {
    if (getters.isLoggedIn) {
      return new Promise((resolve) => {
        commit('toggleModalGroup', data)
        resolve()
      })
    } else {
      return router.push({ name: 'login' })
    }
  },

  toggleShow ({ commit, getters }, data) {
    return new Promise((resolve) => {
      commit('toggleShow', data)
      resolve()
    })
  }
}

export default actions
