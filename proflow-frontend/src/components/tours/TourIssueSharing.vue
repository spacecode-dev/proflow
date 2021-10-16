<template>
  <b-modal
    v-model="show"
    dialog-class="tour-modal-issue-view-sharing"
    centered
    hide-header
    hide-footer
    no-close-on-backdrop
  >
    <b-row>
      <b-col cols="auto">
        <img src="@/assets/images/icons/sharing.svg" />
      </b-col>

      <b-col class="pl-1">
        <div class="font-weight-semibold">Sharing</div>
        <div class="mt-3 mb-5">
          <p class="text-black-75">
            When you are ready to share with your teammates, simply select an option from the "Visibility" dropdown in the top right corner.
          </p>
          <p class="text-black-75">
            You can choose to share with the entire company or only to specific people.
          </p>
        </div>
        <div class="mt-3 border rounded mb-4 mr-3">
          <b-row class="mx-1 mt-3">
            <b-col cols="auto" class="pr-0">
              <AppIcon icon="icon-pf-company" />
            </b-col>
            <b-col class="pl-1">
              <p class="mb-0 pf-heading">Company</p>
              <span class="text-secondary pf-subheading">Visible to the entire company.</span>
            </b-col>
          </b-row>
          <b-row class="mx-1 my-3">
            <b-col cols="auto" class="pr-0">
              <AppIcon icon="icon-pf-lock" />
            </b-col>
            <b-col class="pl-1">
              <p class="mb-0 pf-heading">Private Issue</p>
              <span class="text-secondary pf-subheading">Visible only to people you choose to share with</span>
            </b-col>
          </b-row>
        </div>
      </b-col>
    </b-row>
    <b-button variant="primary" class="d-flex ml-auto mb-1 mr-3 px-4 btn-tour-next" @click="onSubmit">
      <span class="mx-2">Finish</span>
    </b-button>
  </b-modal>
</template>

<script>
import { mapActions } from 'vuex'
export default {
  name: 'TourIssueSharing',
  props: {
    show: {
      type: Boolean,
      default: true
    }
  },
  methods: {
    ...mapActions(['saveOnboardingDetail']),
    async onSubmit () {
      await this.saveOnboardingDetail({ on_boarding_step: 6 })
    }
  }
}
</script>

<style lang="scss" scoped>
.tour-modal-issue-view-sharing {
  width: 423px;

  p {
    font-size: 13px;
  }
}
</style>
