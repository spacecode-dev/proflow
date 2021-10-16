<template>
  <b-nav vertical class="h-100">
    <label class="list-title font-weight-bold pt-4 px-3">Personal</label>
    <b-nav-item @click.prevent="changeTab('account')" :link-classes="['pf-heading', 'font-weight-medium', {'is-active': modalSettingActiveTab === 'account'}]">
      <AppIcon icon="icon-pf-user-thin"/>
      <span>My Account</span>
    </b-nav-item>
    <!-- <b-nav-item :to="{ name: 'notification-settings' }" link-classes="pf-heading font-weight-medium">
      <AppIcon icon="icon-pf-notification"/>
      <span>Notifications</span>
    </b-nav-item> -->
    <label class="list-title font-weight-bold pt-4 px-3">Workspace</label>
    <b-nav-item @click.prevent="changeTab('workspace')" :link-classes="['pf-heading', 'font-weight-medium', {'is-active': modalSettingActiveTab === 'workspace'}]">
      <AppIcon icon="icon-pf-cog"/>
      <span>Settings</span>
    </b-nav-item>
    <b-nav-item @click.prevent="changeTab('members')" :link-classes="['pf-heading', 'font-weight-medium', {'is-active': modalSettingActiveTab === 'members'}]">
      <AppIcon icon="icon-pf-users"/>
      <span>Members</span>
    </b-nav-item>
    <b-nav-item v-if="user.role_id === 2" @click.prevent="changeTab('plans')" class="pb-3" :link-classes="['pf-heading', 'font-weight-medium', {'is-active': modalSettingActiveTab === 'plans'}]">
      <AppIcon icon="icon-pf-rocket"/>
      <span>Plans</span>
    </b-nav-item>
  </b-nav>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'

export default {
  name: 'Sidebar',

  computed: {
    ...mapGetters(['user', 'modalSettingActiveTab'])
  },

  methods: {
    ...mapActions(['toggleModalSetting']),
    changeTab (tab) {
      this.toggleModalSetting({ status: true, tab: tab })
    }
  }
}
</script>

<style lang="scss" scoped>
  ul.nav {
    background: var(--info);
  }

  a.nav-link:hover {
    background: #E2EAF1;
  }

  a.is-active {
    background: #D0DCE8;
  }
</style>
