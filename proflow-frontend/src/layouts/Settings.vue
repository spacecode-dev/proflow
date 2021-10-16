<template>
  <b-modal
    id="settingsModal"
    size="xl"
    modal-class="settings-modal p-0"
    content-class="fill-modal-height"
    body-class="d-flex p-0"
    centered
    hide-header
    hide-footer
    @hidden="close"
  >
    <b-container fluid>
      <b-row class="h-100">
        <b-col cols="auto" class="p-0">
          <Sidebar class="sidebar"/>
        </b-col>
        <b-col v-if="modalSettingActiveTab" cols="auto" class="pt-3 pb-2 px-md-4 flex-grow-1">
          <AccountSettings v-if="modalSettingActiveTab === 'account'" @cancel="close"/>
          <Workspace v-else-if="modalSettingActiveTab === 'workspace'" @cancel="close"/>
          <Members v-else-if="modalSettingActiveTab === 'members'" @cancel="close"/>
          <Plan v-else-if="modalSettingActiveTab === 'plans'" @cancel="close"/>
        </b-col>
      </b-row>
    </b-container>
  </b-modal>
</template>

<script>
import Sidebar from '@/components/layouts/settings/Sidebar'
import AccountSettings from '@/views/settings/Account'
import Workspace from '@/views/settings/Workspace'
import Members from '@/views/settings/Members'
import Plan from '@/views/settings/Plan'
import { mapActions, mapGetters } from 'vuex'

export default {
  name: 'Settings',
  components: {
    Sidebar,
    AccountSettings,
    Workspace,
    Members,
    Plan
  },
  computed: {
    ...mapGetters(['modalSettingActiveTab'])
  },
  methods: {
    ...mapActions(['toggleModalSetting']),
    close () {
      this.$root.$emit('bv::hide::modal', 'settingsModal')
      setTimeout(function () {
        this.toggleModalSetting({ status: false })
      }.bind(this), 500)
    }
  }
}
</script>

<style lang="scss">
  .fill-modal-height {
    min-height: calc(100vh - 6.5rem);
    margin: 0 0.5rem;
  }

  @media only screen and (max-width: 575px) {
    .fill-modal-height {
      min-height: calc(100vh - 2.5rem);
      margin: 0.5rem;
    }
  }

  .sidebar {
    width: 180px;
    border-top-left-radius: 6px;
    border-bottom-left-radius: 6px;
  }

  .settings-modal {
    p {
      color: var(--black);
    }

    .btn-outline-secondary {
      color: var(--black-90);
    }

    .btn-change-photo {
      width: 140px;
    }

    .form-text {
      font-size: 11px;
      margin-left: 12px;
    }

    .action-divider {
      margin: 12px -24px;
    }
  }
</style>
