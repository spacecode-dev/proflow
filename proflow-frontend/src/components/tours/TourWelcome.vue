<template>
  <b-modal
    v-model="show"
    content-class="tour-modal-shadow"
    dialog-class="tour-modal-no-back-drop"
    body-class="p-0"
    hide-backdrop
    centered
    hide-header
    hide-footer
    no-close-on-backdrop
  >
    <img src="@/assets/images/illustrations/BannerImage.svg" class="mx-auto mb-5"/>
    <div class="mt-3 p-4 text-center mb-5">
      <p class="font-weight-semibold heading">Welcome to ProFlow!</p>
      <p class="pf-subheading pt-3">
        Thank you for signing up to our Beta!
      </p>
      <p class="pf-subheading">
        ProFlow is a tool for teams to organise issues and resolve blockers in the workplace.
      </p>
      <p class="pf-subheading">
        Say goodbye to never-ending email threads, Slack channels and Google Docs.
      </p>
    </div>
    <b-button variant="primary" class="d-flex mx-auto mb-4 px-4 btn-tour-next" @click="onSubmit">
      <span class="mx-2">Next</span>
    </b-button>
  </b-modal>
</template>

<script>
import { mapActions } from 'vuex'

export default {
  name: 'TourWelcome',
  props: {
    show: {
      type: Boolean,
      default: true
    }
  },
  methods: {
    ...mapActions(['saveOnboardingDetail']),
    async onSubmit () {
      await this.saveOnboardingDetail({ on_boarding_step: 1 })
    }
  }
}
</script>

<style lang="scss" scoped>
  .heading {
    font-size: 22px;
  }
</style>
