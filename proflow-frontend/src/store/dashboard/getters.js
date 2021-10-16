const getters = {
  userGroups: state => state.userGroups,
  tags: state => state.tags,
  issues: state => state.issues,
  // draftIssues: state => state.draftIssues,
  sidebarIssuesCount: state => state.sidebarIssuesCount,
  issueTakeCount: state => state.issueTakeCount,
  issuesAllCount: state => state.issuesCount.all,
  issuesShowCount: state => state.issuesCount.show
}

export default getters
