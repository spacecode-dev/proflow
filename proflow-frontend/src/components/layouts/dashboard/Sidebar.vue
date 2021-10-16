<template>
  <div class="b-sidebar">
      <router-link :to="{ name: 'home' }" class="header-link">
        <AppLogo with-title class="logo b-sidebar-header p-3" height="24" width="24" clickable/>
      </router-link>
    <div class="b-sidebar-body thin-scroll sidebar-navigation d-flex flex-column px-0 pt-2">

      <b-nav-item
        :to="{ name: 'home' }"
        link-classes="pf-heading font-weight-semibold"
      >
        <AppIcon icon="icon-pf-user-single" iconClass=""/>
        My Issues
        <div v-if="'my_issues' in sidebarIssuesCount" class="count">{{ sidebarIssuesCount.my_issues }}</div>
      </b-nav-item>

      <b-nav-item
        :to="{ name: 'home-type', params: { type: 'private' } }"
        link-classes="pf-heading font-weight-semibold"
      >
        <AppIcon icon="icon-pf-lock" iconClass=""/>
        Private
        <div v-if="'private' in sidebarIssuesCount" class="count">{{ sidebarIssuesCount.private }}</div>
      </b-nav-item>

      <b-nav-item
        href="#link-2"
        link-classes="pf-heading font-weight-semibold"
      >
        <AppIcon icon="icon-pf-user-group" iconClass=""/>
        My Groups
      </b-nav-item>
      <div v-if="userGroups.length" class="group">
        <div
          v-for="group in userGroups"
          :key="group.id"
          @mouseover="showByIndex = group"
          @mouseout="showByIndex = null"
        >
          <div class="d-flex">
            <router-link :to="{ name: 'home-group', params: { type: 'group', groupId: group.id, groupName: group.name } }" class="d-flex">
              {{ group.name | stripTags | capitalize }}
              <div @click.prevent="toggleModal('group', {status: true, action: 'edit', id: group.id})" v-show="showByIndex === group" class="mr-2 ml-3 w-auto">
                <AppIcon icon="icon-pf-pencil" variant="dark" iconClass=""/>
              </div>
              <div v-if="'groups' in sidebarIssuesCount && sidebarIssuesCount.groups.length" class="ml-auto count">
                {{ sidebarIssuesCount.groups.find(x => x.id === group.id).count }}
              </div>
            </router-link>
          </div>
        </div>
      </div>

      <b-nav-item
        @click.prevent="toggleModal('group', {status: true, action: 'add'})"
        class="pt-0"
        link-classes="pf-heading py-0 font-weight-medium pt-0 add-item"
      >
        <AppIcon icon="plus"/>
        Add
      </b-nav-item>

      <b-nav-item
        :to="{ name: 'home-type', params: { type: 'company' } }"
        link-classes="pf-heading font-weight-semibold"
      >
        <AppIcon icon="icon-pf-company" iconClass=""/>
        Company
        <div v-if="'company' in sidebarIssuesCount" class="count">{{ sidebarIssuesCount.company }}</div>
      </b-nav-item>

      <b-nav-item
        :to="{ name: 'home-type', params: { type: 'unassigned' } }"
        link-classes="pf-heading font-weight-semibold"
      >
        <AppIcon icon="icon-pf-help1" iconClass=""/>
        Unassigned
        <div v-if="'unassigned' in sidebarIssuesCount" class="count">{{ sidebarIssuesCount.unassigned }}</div>
      </b-nav-item>

      <b-nav-item
        :to="{ name: 'home-type', params: { type: 'drafts' } }"
        link-classes="pf-heading font-weight-semibold"
      >
        <AppIcon icon="icon-pf-draft" iconClass=""/>
        Drafts
        <div v-if="'drafts' in sidebarIssuesCount" class="count">{{ sidebarIssuesCount.drafts }}</div>
      </b-nav-item>
<!--      <div v-if="draftIssues.length" class="group">-->
<!--        <div v-for="draft in draftIssues" :key="draft.id">-->
<!--          <router-link :to="{ name: 'issue-view', params: { id: draft.unique_id } }">-->
<!--            {{ draft.title | stripTags | capitalize }}-->
<!--          </router-link>-->
<!--        </div>-->
<!--      </div>-->

      <div class="invite-users mt-auto">
        <img src="@/assets/images/illustrations/InviteImage.svg" alt="invite-users"/>
        <p>Invite your teammates and start collaborating!</p>
        <b-button @click.prevent="toggleModal('setting', {status: true, tab: 'members', initModal: 'add-user-modal'})" variant="light">Invite</b-button>
      </div>

      <!-- <b-nav-item
        :to="{ name: 'account-settings' }"
        link-classes="pf-heading font-weight-semibold"
      >
        <AppIcon icon="icon-pf-cog"/>
        Setting
      </b-nav-item> -->
    </div>

    <div class="b-sidebar-footer sidebar-navigation px-0 py-2">

      <b-nav-item
        :to="{ name: 'home-type', params: { type: 'resolved' } }"
        link-classes="pf-heading font-weight-semibold"
      >
        <AppIcon icon="icon-pf-check-square" iconClass=""/>
        Resolved
      </b-nav-item>

      <b-nav-item
        :to="{ name: 'home-type', params: { type: 'archived' } }"
        link-classes="pf-heading font-weight-semibold"
      >
        <AppIcon icon="icon-pf-archived" iconClass=""/>
        Archived
      </b-nav-item>

      <b-nav-item
        id="create-new-issue"
        class="pt-2"
        link-classes="pf-heading font-weight-semibold"
        @click="createIssue"
      >
        <b-icon icon="plus-square-fill" aria-hidden="true"/>
        Create new Issue
      </b-nav-item>

      <b-tooltip
        ref="tooltip"
        show
        placement="left"
        variant="primary"
        v-if="user.user_detail.on_boarding_step === 2"
        target="create-new-issue"
      >
        <div class="text-left px-2">
          <p class="text-white mt-2">Raise Issue</p>
          <p class="pf-subheading text-white">Get started by raising your first issue</p>
        </div>
      </b-tooltip>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import AppLogo from '@/components/general/AppLogo'

export default {
  name: 'Sidebar',
  components: { AppLogo },
  data () {
    return {
      form: {
        title: '<h4>Untitled</h4>',
        visibility: 'draft',
        priority: 3,
        due_date: new Date(),
        people_involved: [],
        issue_summary: [
          {
            type: 'summary',
            text: ''
          },
          {
            type: 'summary-imp',
            text: ''
          },
          {
            type: 'summary-output',
            text: ''
          },
          {
            type: 'additional-info',
            text: ''
          }
        ],
        issue_step: [
          { text: '' }
        ]
      },
      showByIndex: null
    }
  },
  computed: {
    // ...mapGetters(['userGroups', 'draftIssues', 'user', 'sidebarIssuesCount'])
    ...mapGetters(['userGroups', 'user', 'sidebarIssuesCount'])
  },
  beforeCreate () {
    // this.$store.dispatch('getDraftIssues')
    this.$store.dispatch('getIssuesCount')
  },
  mounted () {
    this.showTooltip()
  },
  methods: {
    ...mapActions(['createIssueData', 'saveOnboardingDetail', 'toggleModalSetting', 'toggleModalGroup']),
    toggleModal (modalName, data) {
      if (modalName === 'setting') {
        if(!data.status) {
          this.$root.$emit('bv::hide::modal', 'settingsModal')
          setTimeout(function () {
            this.toggleModalSetting(data)
          }.bind(this), 500)
        } else if (data.status) {
          this.toggleModalSetting(data).then(() => {
            this.$root.$emit('bv::show::modal', 'settingsModal')
            if('initModal' in data) {
              setTimeout(function () {
                this.$root.$emit('bv::show::modal', data.initModal)
              }.bind(this), 500)
            }
          })
        }
      } else if (modalName === 'group') {
        if(!data.status) {
          this.$root.$emit('bv::hide::modal', 'groupsModal')
          setTimeout(function () {
            this.toggleModalGroup(data)
          }.bind(this), 500)
        } else if (data.status) {
          this.toggleModalGroup(data).then(() => {
            this.$root.$emit('bv::show::modal', 'groupsModal')
          })
        }
      }
    },
    showTooltip () {
      if (this.$refs.tooltip) {
        this.$refs.tooltip.$emit('open')
      }
    },
    closeTooltip () {
      if (this.$refs.tooltip) {
        this.$refs.tooltip.$emit('close')
      }
    },
    async createIssue () {
      if (this.user.user_detail.on_boarding_step !== 6) {
        await this.saveOnboardingDetail({ on_boarding_step: 3 })
      }
      const data = await this.createIssueData(this.form)
      await this.$router.push({
        name: 'issue-view',
        params: {
          type: 'create',
          id: data.unique_id
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
  $border: 1px solid #E3E3E3;

  .b-sidebar {
    border-right: $border;
    position: relative !important;
    width: 221px;
    z-index: 99;
  }
  .header-link {
    text-decoration: none;
    color:black;

  }
  .b-sidebar-header {
    border-bottom: $border;
    font-size: inherit;
  }

  .b-sidebar-body, .b-sidebar-footer {
    .nav-link {
      display: flex;
      align-items: center;

      .icon {
        display: flex;
        margin-right: .6rem;
      }
    }
  }

  .b-sidebar-footer, .b-sidebar-footer .nav-item:last-child {
    border-top: $border;
  }

  .sidebar-navigation {
    .nav-item {
      color: #606974;
      font-weight: 600;
      font-size: 13px;
      line-height: 16px;
      padding: 8px 0;
      &#create-new-issue {
        svg {
          font-size: 22px;
          margin-left: -4px;
          margin-right: 11px;
          color: #7D8794;
        }
      }
      span[class^="icon-pf-"] {
        font-size: 14px;
        margin-right: 10px;
      }
      .count {
        margin-left: auto;
        font-weight: 400;
      }
    }

    .invite-users {
      padding: 28px 0;
      text-align: center;
      img {
        width: 130px;
      }
      p {
        margin: 15px auto;
        display: block;
        width: 145px;
        font-weight: 500;
        font-size: 12px;
        color: #606974;
        line-height: 15px;
      }
      .btn {
        padding-top: 2px;
        padding-bottom: 2px;
        font-size: 12px;
        width: 80px;
        color: #1E1E1F !important;
        background: white !important;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1) !important;
        border-radius: 6px;
      }
    }

    .group {
      padding-left: 2rem;
      font-size: 13px;
      font-weight: 500;
      color: #606974;
      margin-bottom: 8px;

      & > div {
        position: relative;

        &:not(:last-of-type) {
          margin-bottom: 8px;
        }

        a {
          width: 170px;
          display: block;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
          text-decoration: none;
        }

        &:before {
          content: '';
          width: 4px;
          height: 4px;
          background: #606974;
          border-radius: 4px;
          position: absolute;
          left: -10px;
          top: 8px;
        }
      }
    }

    /*.group-items .nav-item, .add-item {*/
    /*  font-weight: 500;*/
    /*  padding-top: 0;*/
    /*  padding-bottom: 0;*/

    /*  a {*/
    /*    padding-top: 2px;*/
    /*    padding-bottom: 2px;*/
    /*  }*/
    /*}*/

    /*.group-items .icon-pencil {*/
    /*  margin-bottom: 6px;*/
    /*}*/

    .text-truncate {
      max-width: 140px;
    }

    .add-item a:not(.router-link-exact-active) {
      color: rgba(96, 105, 116, 0.5);
    }

    a:not(.router-link-exact-active):hover {
      color: #232A33;
      font-weight: 500;
    }

    .router-link-exact-active {
      color: var(--primary);
    }
  }

  ::v-deep .tooltip-inner {
    max-width: 500px !important;
    width: 291px !important;
  }
</style>
