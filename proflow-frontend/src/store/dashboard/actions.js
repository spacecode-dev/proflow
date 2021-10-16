import axios from 'axios'
import _ from 'lodash'

const actions = {

  async editGroupDetail ({ dispatch }, groupData) {
    const { data } = await axios.post('/update-group', groupData)
    await dispatch('createOrUpdateUserGroup', { id: data.id, name: data.name })
    return data
  },

  async getGroupDetail ({ dispatch }, groupId) {
    const { data } = await axios.get(`/edit-group/${groupId}`)
    return data
  },

  async removeGroupMember (context, groupId) {
    const { data } = await axios.delete(`/remove-group/${groupId}`)
    return data
  },

  setUserGroupDetails ({ commit }, userGroups) {
    commit('setUserGroups', userGroups)
  },

  getTags ({ commit }) {
    return new Promise((resolve) => {
      axios.get('/get-tags').then(response => {
        commit('setTags', response.data)
        resolve()
      })
    })
  },

  getIssues ({ commit }, data) {
    return new Promise((resolve) => {
      axios.post('/get-issues', data).then(response => {
        commit('setIssues', response.data.issues)
        commit('setIssuesCount', response.data.issuesCount)
        resolve(response.data.issues)
      })
    })
  },

  loadMoreIssues ({ commit }, data) {
    return new Promise((resolve) => {
      axios.post('/get-issues', data).then(response => {
        commit('concatIssues', response.data.issues)
        commit('setIssuesCount', response.data.issuesCount)
        resolve(response.data.issues)
      })
    })
  },

  // getDraftIssues ({ commit }) {
  //   return new Promise((resolve) => {
  //     axios.get('/get-draft-issues').then(response => {
  //       commit('setDraftIssues', response.data)
  //       resolve()
  //     })
  //   })
  // },

  // updateDraftIssueTitle ({ commit }, data) {
  //   return new Promise((resolve) => {
  //     commit('updateDraftIssueTitle', data)
  //     resolve()
  //   })
  // },

  getIssuesCount ({ commit, getters }) {
    return new Promise((resolve) => {
      axios.post('/get-issues-count', { companyId: getters.user.company_detail.id, groupIds: _.map(getters.userGroups, 'id') }).then(response => {
        commit('setSidebarIssuesCount', response.data)
        resolve()
      })
    })
  },

  // prependToDraftIssues ({ commit }, data) {
  //   return new Promise((resolve) => {
  //     commit('prependToDraftIssues', { unique_id: data.unique_id, title: data.title })
  //     resolve()
  //   })
  // },

  loadShowStepCount ({ commit, getters }, data) {
    return new Promise((resolve) => {
      commit('loadShowStepCount', data)
      resolve()
    })
  },

  async createOrUpdateUserGroup ({ state, dispatch }, userGroup) {
    const index = state.userGroups.findIndex(g => g.id === userGroup.id)
    if (index > -1) {
      state.userGroups[index] = userGroup
    } else {
      state.userGroups.push(userGroup)
    }
    await dispatch('setUserGroupDetails', state.userGroups)
  },

  async sendFeedback (context, feedbackData) {
    const { data } = await axios.post('/send-feedback', feedbackData)
    return data
  },

  async saveOnboardingDetail ({ dispatch }, userDetail) {
    const { data } = await axios.post('/update-onboarding', userDetail)
    await dispatch('setUserDetails', data)
    return data
  }

}

export default actions
