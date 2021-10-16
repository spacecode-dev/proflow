<template>
  <b-row class="d-flex flex-column flex-md-row p-2 px-md-4">
    <div class="d-flex flex-row flex-grow-1 align-items-center order-2 order-md-1">
      <h3 class="title">{{ headerTitle | stripTags | capitalize }}</h3>
      <div class="buttons d-flex ml-auto">
        <b-button variant="outline-dark" class="d-flex align-items-center" pill @click="redirectToHelpCenter">
          <AppIcon icon="icon-pf-help" variant="dark" iconClass=""/>
          Help Center
        </b-button>
        <b-button variant="outline-dark" class="d-flex align-items-center" pill @click="showSendFeedbackModal = true">
          <AppIcon icon="icon-pf-filled-notification" variant="dark" iconClass=""/>
          Send Feedback
        </b-button>
      </div>
    </div>
    <div class="d-flex flex-shrink order-1 order-md-2">
      <AppLogo with-title class="mr-auto d-md-none" height="24" width="24" clickable/>
      <!-- notification -->
      <!-- <b-nav-item-dropdown no-caret right>
        <template v-slot:button-content>
          <div class="d-flex position-relative">
            <b-badge
              variant="danger"
              class="notification-count position-absolute rounded-circle"
            >
              2
            </b-badge>
            <b-icon class="mt-1" icon="bell-fill" variant="secondary" aria-hidden="true"/>
          </div>
        </template>
        <b-dropdown-item>Lorium ipsum dollar sit</b-dropdown-item>
        <b-dropdown-item>Lorium ipsum dollar sit</b-dropdown-item>
      </b-nav-item-dropdown> -->
      <!-- profile -->
      <b-nav-item-dropdown no-caret right>
        <template v-slot:button-content>
          <b-avatar :src="user.user_detail.profile_picture" size="1.75rem"/>
        </template>
        <b-dropdown-item @click="toggleModal({status: true, tab: 'account'})">Settings</b-dropdown-item>
        <b-dropdown-item @click="onSubmit" v-if="isLoggedIn">Logout</b-dropdown-item>
      </b-nav-item-dropdown>
    </div>
     <SendFeedback
       v-if="showSendFeedbackModal"
      header-title="Send Feedback"
      @clicked="showSendFeedbackModal = !showSendFeedbackModal"
      @cancel="showSendFeedbackModal = !showSendFeedbackModal"
    />
  </b-row>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import AppLogo from '@/components/general/AppLogo'
import SendFeedback from '@/views/dashboard/SendFeedback'
export default {
  name: 'Header',
  components: { AppLogo, SendFeedback },
  data () {
    return {
      showSendFeedbackModal: false
    }
  },
  computed: {
    ...mapGetters(['user', 'isLoggedIn']),
    headerTitle: function () {
      return this.$route.meta.title || 'My Issues'
    }
  },
  methods: {
    ...mapActions(['logout', 'toggleModalSetting']),
    async onSubmit () {
      await this.logout()
    },
    redirectToHelpCenter () {
      window.open('https://getproflow.com/help-center', '_newtab')
    },
    toggleModal (data) {
      if(!data.status) {
        this.$root.$emit('bv::hide::modal', 'settingsModal')
        setTimeout(function () {
          this.toggleModalSetting(data)
        }.bind(this), 500)
      } else if (data.status) {
        this.toggleModalSetting(data).then(() => {
          this.$root.$emit('bv::show::modal', 'settingsModal')
        })
      }
    },
  }

}
</script>

<style lang="scss" scoped>
  .notification-count {
    top: -8px;
    right: -6px;
    color: var(--pink);
    background-color: var(--pink-light); // #F5BBCB;
  }
  .title {
    font-weight: 600;
    font-size: 18px;
    line-height: 22px;
    margin: 0;
  }
  .buttons {
    button {
      color: #606974 !important;
      padding: 2px 16px;
      font-size: 11px;
      max-height: 22px;
      font-weight: 600;
      border: 1px solid rgba(96, 105, 116, 0.8);
      background: transparent !important;
      &:first-of-type {
        margin-right: 8px;
      }
      span {
        font-size: 15px;
        margin-right: 7px;
      }
    }
  }
</style>
