const mutations = {

  setUserGroups (state, userGroups) {
    state.userGroups = userGroups
  },

  setTags (state, tags) {
    state.tags = tags
  },

  setIssues (state, issues) {
    if (issues.length) {
      state.issues = issues.map(function (issue) {
        if('assigned_issue_steps' in issue) {
          delete Object.assign(issue, {['steps']: issue['assigned_issue_steps'] })['assigned_issue_steps'];
        } else if ('issue_step' in issue) {
          delete Object.assign(issue, {['steps']: issue['issue_step'] })['issue_step'];
        }
        // if (issue.next_step && issue.steps.length) {
        //   issue.steps = issue.steps.filter(function (step) {
        //     return step.id !== issue.next_step.id
        //   })
        // }
        const length = issue.steps && issue.steps.length
        issue.availableSteps = length ? issue.steps.slice(0, 3) : []
        issue.showStepCount = length >= 3 ? 3 : length
        return issue
      })
    } else {
      state.issues = []
    }
  },

  concatIssues (state, issues) {
    state.issues = state.issues.concat(issues.map(function (issue) {
      // if (issue.next_step && issue.steps.length) {
      //   issue.steps = issue.steps.filter(function (step) {
      //     return step.id !== issue.next_step.id
      //   })
      // }
      const length = issue.steps && issue.steps.length
      issue.availableSteps = length ? issue.steps.slice(0, 3) : []
      issue.showStepCount = length >= 3 ? 3 : length
      return issue
    }))
  },

  setIssuesCount (state, count) {
    state.issuesCount = count
  },

  setSidebarIssuesCount (state, counts) {
    state.sidebarIssuesCount = counts
  },

  // setDraftIssues (state, issues) {
  //   state.draftIssues = issues
  // },

  // prependToDraftIssues (state, issue) {
  //   const array = state.draftIssues
  //   array.pop()
  //   array.unshift(issue)
  //   state.draftIssues = array
  // },

  // updateDraftIssueTitle (state, issue) {
  //   state.draftIssues.find(x => x.unique_id === issue.id).title = issue.title
  // },

  loadShowStepCount (state, data) {
    const issue = state.issues.find(x => x.id === data.id)
    issue.showStepCount = issue.showStepCount + data.count
    issue.availableSteps = issue.steps.slice(0, issue.showStepCount)
  }
}

export default mutations
