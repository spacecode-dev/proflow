<template>
  <div class="container">
    <b-row class="d-flex mt-3">

      <b-col cols="4" offset="8" lg="6" offset-lg="6" class="d-flex align-items-center justify-content-end"
             :class="!issues.length ? 'hide' : null">

        <b-button
          v-if="dataDue.selected.length || dataPriority.selected.length || dataTags.selected.length"
          variant="outline-secondary"
          size="sm"
          :class="['loading', 'd-flex', 'p-1', {'rotating': loading}]"
          @click="reset()"
        >
          <AppIcon icon="icon-pf-loading" variant="dark" iconClass=""/>
        </b-button>

        <div class="due d-flex align-items-center">
          <AppIcon icon="icon-pf-calendar" variant="dark"/>
          <Select
            :title="'dataDue'"
            :options="dataDue.options"
            :selected-string="dataDue.selected"
            @change="change($event, 'dataDue')"
            @remove="remove($event, 'dataDue')"
            placeholder="Due Date"
          />
        </div>

        <div class="priority d-flex">
          <!--TODO flag outline Iconmoon-->
          <Icon name="flag-outline" variant="dark" width="12" class="d-flex mr-2"/>
          <Select
            :title="'dataPriority'"
            :options="dataPriority.options"
            :selected-array="dataPriority.selected"
            @change="change($event, 'dataPriority')"
            @remove="remove($event, 'dataPriority')"
            :type="'icons'"
            :multi="true"
            placeholder="Priority"
          />
        </div>

        <div class="tags d-flex align-items-center">
          <AppIcon icon="icon-pf-tag" variant="dark"/>
          <Select
            :title="'dataTags'"
            :options="dataTags.options"
            :selected-array="dataTags.selected"
            @change="change($event, 'dataTags')"
            @remove="remove($event, 'dataTags')"
            :multi="true"
            :type="'tags'"
            :search="true"
            placeholder="Tag"
          />
        </div>

      </b-col>

      <b-col cols="12">

        <template v-if="resolveIssues.length">
          <router-link v-for="(issue, index) in resolveIssues"
                       :key="'card-' + issue.id"
                       :class="['card', 'card--block', index === resolveIssues.length - 1 ? 'mb-5' : 'mb-2']"
                       :to="{ name: 'issue-view', params: { id: issue.unique_id } }"
                       tag="div"
                       :data-id="issue.unique_id"
          >
            <div class="card-body">

              <div class="card--header mb-3 d-flex align-items-center">
                <h3 class="card-title mb-0">
                  {{ issue.title | stripTags }}
<!--                  - {{ issue.id }}-->
                </h3>
                <template v-if="issue.tags.length">
                  <b-button v-for="tag in issue.tags" :key="'tag-' + tag.id" variant="light" pill>
                    {{ tag.title }}
                  </b-button>
                </template>
                <div v-if="issue.people_involved.length" class="ml-auto images d-flex">
                  <AppAvatar v-for="involved in issue.people_involved" :key="'involved-' + involved.id" size="20"
                             :src="involved.user_detail.profile_picture"
                             :text="(involved.name)?involved.name:involved.email"/>
                </div>
              </div>

              <div class="card--subheader mb-3 d-flex align-items-center">
                <div class="card--date d-flex align-items-center">
                  <AppIcon icon="icon-pf-calendar" variant="dark"/>
                  <span>{{ issue.due_date | formatDate('DD MMM YYYY') }}</span>
                </div>
                <div class="card--priority d-flex align-items-center">
                  <AppIcon icon="icon-pf-flag" :variant="dataPriority.options.find(x => x.id === issue.priority).type"/>
                  <span>{{ dataPriority.options.find(x => x.id === issue.priority).title }}</span>
                </div>
                <b-button variant="light" pill class="ml-auto card--button">
                  <AppIcon :icon="issue.visibility === 'private' ? 'icon-pf-lock' : 'icon-pf-' + issue.visibility"
                           variant="black" iconClass=""/>
                  {{ issue.visibility | capitalize }}
                </b-button>
              </div>

              <template v-if="$route.params && $route.params.type === 'unassigned'">
                <div v-if="issue.unassigned_next_steps && issue.unassigned_next_steps.length"
                     class="card--step unassigned mb-3">
                  <h4>Next Step:</h4>
                  <div v-for="step in issue.unassigned_next_steps" :key="issue.unique_id + '-' + step.id"
                       class="step mb-2 d-flex align-items-center justify-content-between">
                    <span class="text">{{ step.text | stripTags }}</span>
                    <span class="date d-flex align-items-center">?</span>
                  </div>
                </div>
              </template>
              <template v-else>
                <div v-if="issue.next_step" class="card--step mb-3">
                  <h4>Next Step:</h4>
                  <div class="step mb-2 d-flex align-items-center justify-content-between">
                    <span class="text">
                      {{ issue.next_step.text | stripTags }}
                    </span>
                    <span class="date d-flex align-items-center">
                      <AppIcon icon="icon-pf-card-success" :variant="issue.next_step.due_date | getVariantByDate"/>
                      <span v-if="issue.next_step.due_date" :class="issue.next_step.due_date | getColorByDate"
                            class="date">
                        {{ issue.next_step.due_date | formatDate('MMM DD') }}
                      </span>
                      <AppAvatar v-if="issue.next_step.assigned_user.length" size="20"
                                 :src="issue.next_step.assigned_user[0].user_detail.profile_picture"
                                 :text="(issue.next_step.assigned_user[0].name)?issue.next_step.assigned_user[0].name:issue.next_step.assigned_user[0].email"/>
                    </span>
                  </div>
                </div>
              </template>

              <div v-if="issue.steps && issue.steps.length" class="card--tasks">
                <h4>My Tasks:</h4>
                <ul v-if="issue.availableSteps.length" class="list-unstyled">
                  <li v-for="step in issue.availableSteps" :key="'step-' + step.id">

                      <span class="text">
                        {{ step.issue_step.text | stripTags }}
                      </span>
                    <span class="date">
                        <AppIcon icon="icon-pf-card-success"
                                 :variant="step.issue_step.due_date | getVariantByDate"/>
                        <span v-if="step.issue_step.due_date"
                              :class="step.issue_step.due_date | getColorByDate">
                          {{ step.issue_step.due_date | formatDate('MMM DD') }}
                        </span>
                      </span>
                  </li>
                </ul>
              </div>

              <div v-if="issue.steps && issue.steps.length > issue.showStepCount" class="card--more">
                <div class="card-link" @click.prevent="loadShowStepCount(issue)">
                  +{{ getMoreCount(issue) }} More
                </div>
              </div>

            </div>
          </router-link>
        </template>

        <div v-if="!resolveIssues.length && emptyResult" class="pf-empty">
          <img
            :src="emptyResult.image"
            class="img-fluid mx-auto d-block"
            alt="empty-state"
          />
          <p class="pf-emptytitle my-2">{{ emptyResult.title }}</p>
          <p class="pf-heading font-weight-medium text-center m-0">{{ emptyResult.subTitle }}</p>
        </div>

      </b-col>

    </b-row>
  </div>
</template>
<script>
import Select from '@/components/general/Select'
import Icon from '@/components/general/Icon'
/* eslint-disable */
import store from '@/store'
import moment from 'moment-timezone'
import {mapGetters, mapActions} from 'vuex'

export default {

  computed: {
    ...mapGetters([
      'isLoggedIn',
      'issues',
      'issuesAllCount',
      'issuesShowCount',
      'issueTakeCount',
      'tags',
      'user'
    ]),
    ...mapActions([
      'logout'
    ]),
    emptyResult() {
      if (this.dataDue.selected.length || this.dataPriority.selected.length || this.dataTags.selected.length) {
        return this.emptyStates.find((data) => data.type === 'filter')
      } else if (this.$route.name === 'home') {
        return this.emptyStates.find((data) => data.type === this.$route.name)
      } else if (this.$route.name === 'home-group') {
        return this.emptyStates.find((data) => data.type === 'group')
      }
      return this.emptyStates.find((data) => data.type === this.$route.params.type)
    }
  },

  components: {
    Select,
    Icon
  },

  mounted() {
    this.$nextTick(function () {
      this.scroll()
    })
    const scroll = document.querySelectorAll("[data-action='scroll']");
    if(scroll.length) {
      scroll[0].addEventListener('scroll', this.scroll)
    }
  },
  beforeDestroy() {
    const scroll = document.querySelectorAll("[data-action='scroll']");
    if(scroll.length) {
      scroll[0].removeEventListener('scroll', this.scroll)
    }
  },

  beforeRouteEnter(to, from, next) {
    const getters = store.getters
    store.dispatch('toggleShow', false)
    let data = {
      type: to.name === 'home' ? null : to.params.type,
      companyId: getters.user.company_detail.id,
      take: getters.issueTakeCount,
      skip: 0
    }
    if (!getters.tags.length) {
      store.dispatch('getTags')
    }
    if (to.name === 'home-group') {
      data = {...data, ...{groupId: to.params.groupId}};
    }
    if (to.name === 'home-type') {
      to.meta.title = to.params.type
    } else if (to.name === 'home-group') {
      to.meta.title = to.params.groupName
    }
    store.dispatch('getIssues', data).then(() => {
      store.dispatch('toggleShow', true)
      next(vm => {
        vm.setData()
      })
      return true
    })
  },

  beforeRouteUpdate(to, from, next) {
    const getters = store.getters
    store.dispatch('toggleShow', false)
    let data = {
      type: to.params.type,
      companyId: getters.user.company_detail.id,
      take: getters.issueTakeCount,
      skip: 0
    }
    if (to.name === 'home-group') {
      data = {...data, ...{groupId: to.params.groupId}};
    }
    if (to.name === 'home-type') {
      to.meta.title = to.params.type
    } else if (to.name === 'home-group') {
      to.meta.title = to.params.groupName
    }
    store.dispatch('getIssues', data).then(() => {
      this.setData()
      this.toggleShow(true)
      next()
      return true
    })
  },

  data() {
    return {
      showSendFeedbackModal: false,
      loading: false,
      canLoadMore: true,
      dataDue: {
        selected: '',
        options: ['Overdue', 'Due in 24hrs', 'Due this week']
      },
      dataPriority: {
        selected: [],
        options: [
          {title: 'Urgent', icon: 'flag', type: 'danger', id: 0},
          {title: 'High', icon: 'flag', type: 'warning', id: 1},
          {title: 'Medium', icon: 'flag', type: 'success', id: 2},
          {title: 'Low', icon: 'flag', type: 'primary', id: 3}
        ]
      },
      dataTags: {
        selected: [],
        options: []
      },
      resolveIssues: store.getters.issues,
      emptyStates: [
        {
          title: 'Nothing to see here...',
          subTitle: 'Get started by raising an issue in the bottom left corner',
          image: require('@/assets/images/illustrations/my-issue.svg'),
          type: 'home'
        },
        {
          title: 'Nothing to see here...',
          subTitle: 'Get started by raising an issue in the bottom left corner',
          image: require('@/assets/images/illustrations/my-issue.svg'),
          type: 'company'
        },
        {
          title: 'Nothing to see here...',
          subTitle: 'Get started by raising an issue in the bottom left corner',
          image: require('@/assets/images/illustrations/my-issue.svg'),
          type: 'unassigned'
        },
        {
          title: 'Nothing to see here...',
          subTitle: 'Get started by raising an issue in the bottom left corner',
          image: require('@/assets/images/illustrations/my-issue.svg'),
          type: 'drafts'
        },
        {
          title: 'No private issues',
          subTitle: "Private issues are only visible to people they're shared with",
          image: require('@/assets/images/illustrations/private.svg'),
          type: 'private'
        },
        {
          title: 'No Resolved Issues',
          subTitle: 'When Issues get resolved they will appear here',
          image: require('@/assets/images/illustrations/resolved.svg'),
          type: 'resolved'
        },
        {
          title: 'No Results',
          subTitle: 'Try refining your filters',
          image: require('@/assets/images/illustrations/no-results.svg'),
          type: 'filter'
        },
        {
          title: 'No Archived Issues',
          subTitle: 'When Issues get archived they will appear here',
          image: require('@/assets/images/illustrations/no-archived.svg'),
          type: 'archived'
        },
        {
          title: 'No Issues in this group',
          subTitle: 'There are no issues associated with members of this group',
          image: require('@/assets/images/illustrations/no-group.svg'),
          type: 'group'
        }
      ]
    }
  },

  methods: {
    ...mapActions(['toggleShow']),
    updateSendFeedbackStatus() {
      this.showSendFeedbackModal = false
    },
    scroll(event) {
      if (this.canLoadMore && this.issuesAllCount > this.issuesShowCount && event && event.target.scrollTop + event.target.clientHeight >= event.target.scrollHeight) {
        this.canLoadMore = false
        const type = !this.$route.params.type ? null : this.$route.params.type
        const groupId = !this.$route.params.groupId ? null : this.$route.params.groupId
        let data = {type: type, companyId: this.user.company_detail.id, take: 10, skip: this.issuesShowCount}
        if (groupId) {
          data = {
            type: type,
            companyId: this.user.company_detail.id,
            groupId: groupId,
            take: 10,
            skip: this.issuesShowCount
          }
        }
        this.$store.dispatch('loadMoreIssues', data).then(() => {
          this.resolveIssues = this.issues
          this.canLoadMore = true
        })
      }
    },
    setData() {
      this.dataTags.options = this.tags
      this.resolveIssues = this.issues
    },
    loadShowStepCount(issue) {
      this.$store.dispatch('loadShowStepCount', {id: issue.id, count: this.getMoreCount(issue)}).then(() => {
        this.resolveIssues = this.issues
      })
    },
    getMoreCount(issue) {
      const count = issue.steps.length - issue.showStepCount
      return count > 5 ? 5 : count
    },
    async onSubmit() {
      await this.logout()
    },
    filter(e) {
      const dataDue = this.dataDue.selected
      const dataPriorityKeys = []
      const dataTagsKeys = []
      if (this.dataPriority.selected.length) {
        for (const value of e) {
          dataPriorityKeys.push(value.id)
        }
      }
      if (this.dataTags.selected.length) {
        for (const value of e) {
          dataTagsKeys.push(value.id)
        }
      }
      this.resolveIssues = this.issues
      if (dataDue && this.resolveIssues.length) {
        this.resolveIssues = this.resolveIssues.filter(function (el) {
          const _today_ = moment().utc().format('YYYY-MM-DD')
          const _dueDate_ = moment(el.due_date).utc().format('YYYY-MM-DD')
          if (dataDue === 'Overdue') {
            return moment(_dueDate_).isBefore(_today_)
          } else if (dataDue === 'Due in 24hrs') {
            return moment(_dueDate_).isSame(_today_)
          } else if (dataDue === 'Due this week') {
            const _thisWeek_ = moment().utc().endOf('isoWeek').add(1, 'day').format('YYYY-MM-DD')
            return moment(_dueDate_).isSameOrAfter(_today_) && moment(_dueDate_).isSameOrBefore(_thisWeek_)
          }
        })
      }
      if (dataPriorityKeys.length && this.resolveIssues.length) {
        this.resolveIssues = this.resolveIssues.filter(function (el) {
          return dataPriorityKeys.includes(el.priority)
        })
      }
      if (dataTagsKeys.length && this.resolveIssues.length) {
        this.resolveIssues = this.resolveIssues.filter(function (el) {
          if (el.tags.length) {
            let condition = ''
            for (const tag of el.tags) {
              condition += 'dataTagsKeys.includes(' + tag.id + ') || '
            }
            return eval(condition.slice(0, -4))
          } else {
            return false
          }
        })
      }
    },
    change(e, el) {
      this[el].selected = e
      this.filter(e)
    },
    remove(e, el) {
      if (el === 'dataDue') {
        this.loading = true
        setTimeout(function () {
          this.dataDue.selected = ''
          this.$bus.$emit('clearSelected', ['dataDue'])
          this.resolveIssues = this.issues
          this.loading = false
        }.bind(this), 500)
      }
    },
    reset() {
      this.loading = true
      setTimeout(function () {
        this.dataDue.selected = ''
        this.dataPriority.selected = []
        this.dataTags.selected = []
        this.$bus.$emit('clearSelected', ['dataDue', 'dataPriority', 'dataTags'])
        this.resolveIssues = this.issues
        this.loading = false
      }.bind(this), 500)
    }
  }
}
</script>

<style lang="scss" scoped>
::v-deep {

  .hide {
    visibility: hidden;
  }

  .pf-empty {
    height: calc(100vh - 120px);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
  }

  .loading {
    margin-right: 28px;

    span {
      font-size: 10px;
    }

    &.rotating {
      span {
        animation: rotating 1s linear infinite;
      }
    }
  }

  .due {
    cursor: pointer;
    margin-right: 28px;

    span[class^="icon-pf-"] {
      font-size: 14px;
      color: #606974;
    }
  }

  .priority {
    cursor: pointer;
    margin-right: 28px;

    i.icon svg path {
      fill: #606974 !important;
    }

    .text-danger {
      color: #DC1B50 !important;
    }

    .text-success {
      color: #00A811 !important;
    }

    .text-warning {
      color: #DC781B !important;
    }

    .text-primary {
      color: #3860FF !important;
    }

    .text-secondary {
      color: #8D99A8 !important;
    }

    .multiselect .multiselect__content-wrapper .multiselect__content .multiselect__element .multiselect__option {
      padding: 9px 12px;
    }

    .multiselect__content .multiselect__element .multiselect__option {
      display: flex;
      align-items: center;

      &.multiselect__option--selected {
        &:before {
          content: '';
          background-image: url("data:image/svg+xml,%3Csvg width='11' height='9' viewBox='0 0 11 9' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10.1723 1.14878L9.54423 0.643303C9.23466 0.394493 9.05473 0.397374 8.78968 0.724755L4.22495 6.35806L2.10064 4.59309C1.80756 4.3469 1.62423 4.35999 1.38328 4.66118L0.898493 5.29237C0.652565 5.60194 0.683993 5.77768 0.974708 6.02047L4.00233 8.5248C4.31399 8.78671 4.48947 8.75973 4.73042 8.46666L10.2511 1.90306C10.5104 1.5914 10.4947 1.40518 10.1723 1.14878Z' fill='%238D99A8'/%3E%3C/svg%3E");
          position: absolute;
          right: 10px;
          top: 13px;
          width: 11px;
          height: 9px;
        }
      }

      .option__image {
        font-size: 14px;
        margin-right: 14px;
      }
    }
  }

  .tags {
    cursor: pointer;
    margin-right: 24px;

    span[class^="icon-pf-"] {
      color: #606974 !important;
    }

    .multiselect {
      .multiselect__content-wrapper {
        width: 250px !important;
      }

      &.with-search {
        min-width: 21px;
        &.__empty.multiselect--active {
          &:before {
            content: 'Tag';
            padding: 11px 0 0 0;
            text-align: left;
            font-weight: 500;
            font-size: 12px;
            position: absolute;
            right: .5px;
            top: 0;
            color: #606974;
          }
        }
      }

      .multiselect__tags {
        .multiselect__input {
          width: 230px !important;
        }
      }
    }
  }

  .card {
    cursor: pointer;

    &.card--block {
      .card-body {
        padding: 14px 20px;

        .text-danger {
          color: #E03766 !important;
        }

        .text-success {
          color: #00A811 !important;
        }

        .text-warning {
          color: #DC781B !important;
        }

        .text-primary {
          color: #3860FF !important;
        }

        .text-secondary {
          color: #8D99A8 !important;
        }

        .card--header {
          .card-title {
            font-weight: 600;
            font-size: 15px;
            line-height: 18px;
            color: #000000;
            margin-right: 12px;
          }

          button {
            margin-right: 8px;
            padding-top: 4px;
            padding-bottom: 4px;
            font-size: 11px;
            background: #F0F3F6;
            font-weight: 600;
            line-height: 1;
            min-width: 92px;
          }

          .images {
            div {
              &:not(:first-child) {
                margin-left: -5px;
              }

              img {
                border: 1px solid white;
                opacity: .6;
              }
            }
          }
        }

        .card--subheader {
          .card--date {
            margin-right: 25px;

            span[class^="icon-pf-"] {
              color: #8D99A8 !important;
            }
          }

          .card--date, .card--priority {

            span[class^="icon-pf-"] {
              font-size: 13px;
              margin-right: 7px !important;
            }

            span:not([class^="icon-pf-"]) {
              font-size: 11px;
            }
          }

          button {
            font-size: 11px;
            padding-top: 4px;
            padding-bottom: 4px;
            line-height: 1;
            background: #F0F3F6;
            font-weight: 600;
            min-width: 92px;

            span[class^="icon-pf-"] {
              font-size: 14px;
              margin-right: 5px;
              vertical-align: middle;
            }
          }
        }
      }

      .card--step {
        &.unassigned {
          .step {
            .date {
              width: 20px;
              height: 20px;
              background: #F0F3F6;
              display: flex;
              align-items: center;
              justify-content: center;
              border-radius: 20px;
              color: #8D99A8;
              font-weight: 600;
            }
          }
        }

        h4 {
          color: var(--gray);
          font-size: 11px;
          padding-bottom: 6px;
          margin: 0;
        }

        .step {
          font-size: 12px;
          color: black;
          border: 1px solid var(--gray-outline);
          height: auto;
          padding: 7px 7px 7px 12px;
          max-height: 33px;
          border-radius: 6px;

          .text {
            opacity: .6;
          }

          .date {
            position: relative;

            span[class^="icon-pf-"] {
              display: flex;
              font-size: 10px;
              margin-right: 7px !important;
            }

            span.date {
              margin-right: 12px;
            }

            span:not([class^="icon-pf-"]) {
              font-size: 11px;
            }
          }
        }
      }

      .card--tasks {
        h4 {
          color: var(--gray);
          font-size: 11px;
          padding-bottom: 6px;
          margin: 0;
        }

        ul {
          li {
            display: flex;
            align-items: center;
            justify-content: space-between;

            span.text {
              font-size: 12px;
            }

            span.date {
              min-width: 92px;
              text-align: right;
              display: flex;
              justify-content: flex-end;
              height: 22px;
              align-items: center;

              span[class^="icon-pf-"] {
                font-size: 11px;
                margin-right: 7px !important;
              }

              span:not([class^="icon-pf-"]) {
                font-size: 11px;
              }
            }
          }
        }
      }

      .card--more {
        .card-link {
          cursor: pointer;
          display: inline-flex;
          font-size: 12px;
          color: #3860FF;
        }
      }
    }
  }
}

@-webkit-keyframes rotating {
  from {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  to {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

@keyframes rotating {
  from {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  to {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }

}
</style>
