<template>
  <b-container v-if="!isMobile" fluid class="h-100">
    <div class="d-flex row">
      <Sidebar class="d-none d-lg-flex"/>
      <transition name="block">
        <div v-if="dashboardShow" class="col d-flex flex-column flex-grow-1 main-content thin-scroll" data-action="scroll">
          <Header/>
          <router-view class="px-1"/>
        </div>
      </transition>
    </div>
    <ModalSettings/>
    <ModalGroup/>
    <TourWelcome v-if="user.user_detail.on_boarding_step === 0"/>
    <TourNavigation  v-if="user.user_detail.on_boarding_step === 1"/>
    <TourIssueView v-if="user.user_detail.on_boarding_step === 3 && $route.name === 'issue-view' " />
    <TourIssueNextStep v-if="user.user_detail.on_boarding_step === 4 && $route.name === 'issue-view' "/>
    <TourIssueSharing  v-if="user.user_detail.on_boarding_step === 5 && $route.name ==='issue-view' "/>
  </b-container>
  <b-container v-else-if="isMobile && dashboardShow">
    <div class="row">
      <b-col cols="12" class="mobile-content" data-action="scroll">
        <div class="logo d-flex align-items-center">
          <img
            src="@/assets/images/logo/logo-blue.svg"
            alt="log-blue"
            class="img-fluid"
          />
          <span>{{ appTitle }}</span>
        </div>
        <div class="desktop d-flex flex-column align-items-center">
          <AppIcon icon="icon-pf-desktop" variant="dark" iconClass=""/>
          <p>ProFlow is currently desktop only <br> Mobile view coming soon</p>
        </div>
        <div class="user d-flex flex-column align-items-center">
          <p v-if="isLoggedIn">{{ user.name | capitalize }}</p>
          <a href="#" @click.prevent="onSubmit">Log Out</a>
        </div>
      </b-col>
    </div>
  </b-container>
</template>

<script>
import { isMobile } from 'mobile-device-detect'
import { mapGetters, mapActions } from 'vuex'

import Header from '@/components/layouts/dashboard/Header'
import Sidebar from '@/components/layouts/dashboard/Sidebar'
import ModalSettings from '@/layouts/Settings.vue'
import ModalGroup from '@/views/dashboard/Group'
import TourWelcome from '@/components/tours/TourWelcome'
import TourNavigation from '@/components/tours/TourNavigation'
import TourIssueView from '@/components/tours/TourIssueView'
import TourIssueNextStep from '@/components/tours/TourIssueNextStep'
import TourIssueSharing from '@/components/tours/TourIssueSharing'

export default {
  name: 'Dashboard',
  components: {
    Header,
    Sidebar,
    TourWelcome,
    TourNavigation,
    TourIssueView,
    TourIssueNextStep,
    TourIssueSharing,
    ModalSettings,
    ModalGroup
  },
  data () {
    return {
      // screenWidth: 0
    }
  },
  beforeMount() {
    this.toggleShow(false)
  },
  mounted () {
    this.toggleShow(true)
  //   this.$nextTick(function () {
  //     this.onResize()
  //   })
  //   window.addEventListener('resize', this.onResize)
  // },
  // beforeDestroy () {
  //   window.removeEventListener('resize', this.onResize)
  },
  computed: {
    ...mapGetters(['user', 'isLoggedIn', 'issues', 'dashboardShow']),
    appTitle () {
      return process.env.VUE_APP_TITLE
    },
    isMobile () {
      // return isMobile || this.screenWidth <= 1199
      // return isMobile || this.screenWidth <= 992
      return isMobile
    }
  },
  methods: {
    ...mapActions(['logout', 'toggleShow']),
    async onSubmit () {
      await this.logout()
      // await this.$router.push({ name: 'login' })
    }
    // onResize () {
    //   this.screenWidth = document.documentElement.clientWidth
    // }
  }
}
</script>

<style lang="scss">
.block-enter {
  opacity: 0;
}
.block-enter-active {
  transition : opacity .15s;
}
.block-leave-active {
  transition: opacity .1s;
  opacity: 0;
}
.tour-modal-shadow {
  border: none;
  box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
}

.tour-modal-no-back-drop {
  width: 340px;
}

.tour-modal-issue-view {
  min-width: 530px;

  p {
    font-size: 13px;
  }
}

.btn-tour-next {
  background-color: #3860FF;
}

.mobile-content {
  display: flex;
  flex-direction: column;
  justify-content: space-around;
  align-items: center;
  height: 100vh;

  .logo {
    img {
      width: 35px;
      margin-right: 12px;
    }

    span {
      font-size: 21.8614px;
      line-height: 26px;
      font-weight: 600;
      color: black;
    }
  }

  .desktop {
    [class^="icon-"] {
      font-size: 32px;
      margin-bottom: 25px;
      opacity: .15;
    }

    p {
      margin: 0;
      text-align: center;
      font-size: 14px;
      line-height: 1.8;
      color: rgba(0, 0, 0, 0.7);
    }
  }

  .user {
    p {
      color: rgba(0, 0, 0, 0.9);
      font-weight: bold;
      font-size: 16px;
    }

    a {
      color: #3860FF;
    }
  }
}

.main-content {
  height: 100vh;
  overflow: auto;
}

.pf-modal-small {
  max-width: 404px;
}

p.modal-title {
  font-weight: 600;
  font-size: 18px;
  line-height: 22px;
  padding-bottom: 32px;
}

.modal-footer, .modal-action {
  .btn {
    min-width: 96px;
  }
}

.vue-multiselect {
  .multiselect__tags {
    padding: 8px 0 0 12px;
  }

  .multiselect__option--highlight {
    background-color: var(--info);
  }

  .multiselect__content-wrapper {
    border: 1px solid var(--gray-outline);
    box-sizing: border-box;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    border-radius: 6px;
    margin-top: 4px;
  }
}
</style>
