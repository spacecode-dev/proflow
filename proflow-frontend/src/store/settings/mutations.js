const mutations = {

  setUploadProgress (state, upload) {
    state.upload = upload
  },

  toggleModalSetting (state, data) {
    state.modalSettingStatus = data.status
    state.modalSettingActiveTab = !data.status ? null : data.tab
  },

  toggleModalGroup (state, data) {
    state.modalGroupStatus = data.status
    state.modalGroupAction = !data.status ? null : data.action
    state.modalGroupId = !data.status || !data.id ? null : data.id
  },

  toggleShow (state, data) {
    if(data) {
      setTimeout(() => {
        state.dashboardShow = data
      }, 100)
    } else {
      state.dashboardShow = data
    }
  }
}

export default mutations
