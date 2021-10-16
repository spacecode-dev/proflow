<template>
  <div class="d-flex flex-column h-100">
    <p class="modal-title">Members</p>
    <p class="font-weight-bold">Members({{membersList.length}})</p>
    <p class="text-black-90 font-weight-normal">
      Manage users, anyone with
      <span class="font-weight-bold">"{{user.company_detail.name}}"</span> domain name email
      can join.
    </p>

    <b-row class="mt-3">
      <b-col>
        <label class="px-0">User</label>
      </b-col>
      <b-col>
        <label class="px-0">Access Level</label>
      </b-col>
      <b-col cols="12">
        <hr class="mt-0 mb-2" />
      </b-col>
    </b-row>

    <div class="d-flex flex-column flex-grow-1">
      <b-row v-for="(member) in membersList" :key="`member-${member.id}`" class="mt-1">
        <b-col>
          <div class="d-flex align-content-center">
            <b-avatar class="user-avatar" size="2rem" :src="member.profile_picture" />
            <div class="pl-2 text-truncate">
              <p class="mb-0 user-name">{{member.name}}</p>
              <small class="text-muted">{{member.email}}</small>
            </div>
          </div>
        </b-col>
        <b-col class="align-self-center">
          <div
            v-if="(member.id === user.user_detail.user_id ||  (numberOfAdminsInList == 1)) "
            variant="text"
          >
            <span class="member-action pl-0 text-black-90">{{ (member.role_id === 2)?'Admin':'User' }}</span>
          </div>
          <b-dropdown v-else variant="text" no-caret class="btn-change-role">
            <template v-slot:button-content>
              <div v-if="member.role_id === 2">
                <span>Admin</span>
                <b-icon icon="chevron-down" class="ml-2 mb-px" font-scale="0.9" aria-hidden="true" />
              </div>
              <div v-else>
                <span>User</span>
                <b-icon icon="chevron-down" class="ml-2 mb-px" font-scale="0.9" aria-hidden="true" />
              </div>
            </template>
            <b-dropdown-item @click="changeRole(member.role_id, member.id)">
              Make {{ member.role_id === 2 ? 'User' :
              'Admin' }}
            </b-dropdown-item>
            <hr class="my-0" />
            <b-dropdown-item>
              <span class="text-danger" @click="removeUser(member.id)">Remove User</span>
            </b-dropdown-item>
          </b-dropdown>
        </b-col>
        <b-col cols="12">
          <hr class="my-2" />
        </b-col>
      </b-row>
    </div>

    <hr class="action-divider" />
    <div class="d-flex justify-content-end modal-action mb-1">
      <b-button
        class="mr-3"
        type="submit"
        variant="outline-secondary"
        @click="$emit('cancel')"
      >Cancel</b-button>

      <AddUser @output-data="getMembersListing" />
    </div>
  </div>
</template>

<script>
import AddUser from '@/views/settings/AddUser'

import { mapGetters, mapActions } from 'vuex'

export default {
  name: 'MembersSettings',
  components: { AddUser },
  data () {
    return {
      membersList: []
    }
  },

  computed: {
    ...mapGetters(['user']),
    initial () {
      return this.item.role_id > 0 ? this.text.charAt(0).toUpperCase() : ''
    },
    numberOfAdminsInList () {
      return (
        this.user.role_id === 3 ||
        this.membersList.filter((x) => x.role_id === 2).length
      )
    }
  },

  mounted () {
    this.getMembersListing()
    // success
  },

  methods: {
    ...mapActions(['getMembersList', 'removeMember', 'changeRoleMember']),

    async getMembersListing () {
      this.membersList = await this.getMembersList()
    },

    async changeRole (roleId, userId) {
      const data = { user_id: userId, role_id: roleId === 2 ? 3 : 2 }
      await this.changeRoleMember(data)
      await this.getMembersListing()
    },

    async removeUser (id) {
      const data = { user_id: id }
      await this.removeMember(data)
      await this.getMembersListing()
    }
  }
}
</script>

<style lang="scss" scoped>
.user-avatar {
  min-width: 30px !important;
}

.user-name {
  line-height: 1;
  color: var(--black-90);
}

.mb-px {
  margin-bottom: 1px;
}

.member-action {
  padding: 12px;
  font-size: 14px;
}

.btn-change-role {
  ::v-deep .dropdown-toggle {
    padding-left: 0px;
  }
}
</style>
