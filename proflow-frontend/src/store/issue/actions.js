import axios from 'axios'

const actions = {

  async createIssueData ({ dispatch }, issueData) {
    const { data } = await axios.post('/create-issue', issueData)
    // await dispatch('prependToDraftIssues', data)
    await dispatch('getIssuesCount')
    return data
  },

  async updateIssueData ({ dispatch, commit }, issueData) {
    const { data } = await axios.post(`/update-issue/${issueData.id}`, issueData)
    return data
  },

  async saveIssueComment (context, commentData) {
    const { data } = await axios.post('/create-comment', commentData)
    return data
  },

  async updateIssueStepsData (context, stepsData) {
    const { data } = await axios.post('/update-issue-step', stepsData)
    return data
  },

  async deleteIssueStepsData (context, id) {
    const { data } = await axios.delete(`/delete-issue-step/${id}`)
    return data
  },

  async savePositionData (context, stepsData) {
    const { data } = await axios.post('/update-step-position', stepsData)
    return data
  },

  async getComments (context, issueId) {
    const { data } = await axios.get(`/get-comments/${issueId}`)
    return data
  },

  async getDefaultTags (context) {
    const { data } = await axios.get('/get-default-tag')
    return data
  },

  async updateTag (context, updateTagData) {
    const { data } = await axios.post('/update-tag', updateTagData)
    return data
  },

  async deleteTag (context, deleteTagData) {
    const { data } = await axios.post('/delete-tag', deleteTagData)
    return data
  },

  async getMentionsList (context) {
    const { data } = await axios.get('/mentions-list')
    return data
  },

  async addMembersToIssue (context, invitedMemberdata) {
    const { data } = await axios.post('/add-members', invitedMemberdata)
    return data
  },

  async saveSummaryList (context, summaryData) {
    const { data } = await axios.post('/add-summary', summaryData)
    return data
  },

  async updateIssueStatus (context, issueData) {
    const { data } = await axios.post('/update-issue-status', issueData)
    return data
  },

  async saveUploadedFile (context, fileData) {
    const config = {
      headers: {
        'content-type': 'multipart/form-data'
      }

    }
    const { data } = await axios.post('/save-file', fileData, config)
    return data
  },

  async updateUploadedFile (context, fileData) {
    const config = {
      headers: {
        'content-type': 'multipart/form-data'
      }
    }
    const { data } = await axios.post('/update-file', fileData, config)
    return data
  },

  async removeUploadedFile (context, fileId) {
    const { data } = await axios.post('/delete-file', fileId)
    return data
  },

  async saveMentionMember (context, memberData) {
    const { data } = await axios.post('/save-mention', memberData)
    return data
  },

  async deleteIssue (context, issueData) {
    const { data } = await axios.post('/delete-issue', issueData)
    return data
  }

}

export default actions
