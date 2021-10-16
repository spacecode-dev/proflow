<template>
  <b-modal
    v-model="show"
    content-class="tour-modal-shadow"
    dialog-class="tour-modal-no-back-drop nav-tour-modal"
    body-class="p-0"
    hide-backdrop
    hide-header
    hide-footer
    no-close-on-backdrop
  >
    <div class="mt-3 px-4 py-1 text-center mb-5">
      <div class="d-flex mb-3">
        <img src="@/assets/images/icons/navigation.svg" class="mr-3" />
        <div class="font-weight-semibold">Navigation Overview</div>
      </div>
      <div class="d-flex pt-3 mb-2">
        <AppIcon icon="icon-pf-user-single" class="ml-1 mr-3 icon"/>
        <p class="ml-1 text-black-75">Shows issues you are involved in</p>
      </div>
      <div class="d-flex pt-1 mb-2">
        <AppIcon icon="icon-pf-lock" class="ml-1 mr-3 icon"/>
        <p class="ml-1 text-black-75">Invite only issues</p>
      </div>
      <div class="d-flex pt-1 mb-4">
        <AppIcon icon="icon-pf-user-group" class="ml-1 mr-3 icon"/>
        <p class="ml-1 text-black-75">Create or join groups</p>
      </div>
      <div class="d-flex pt-3 mb-2">
        <AppIcon icon="icon-pf-company" class="ml-1 mr-3 icon"/>
        <p class="ml-1 text-black-75">All Issues in your company</p>
      </div>
      <div class="d-flex pt-2 mb-2">
        <AppIcon icon="icon-pf-help1" class="ml-1 mr-3 icon"/>
        <p class="ml-1 text-black-75">Issues with unnasigned next steps</p>
      </div>
    </div>
    <b-button variant="primary" class="d-flex mx-auto mb-4 px-4 btn-tour-next"  @click="onSubmit">
      <span class="mx-2">Next</span>
    </b-button>
  </b-modal>
</template>

<script>
import { mapActions } from 'vuex'
export default {
  name: 'NavigationTour',
  props: {
    show: {
      type: Boolean,
      default: true
    }
  },

  methods: {
    ...mapActions(['saveOnboardingDetail']),
    async onSubmit () {
      await this.saveOnboardingDetail({ on_boarding_step: 2 })
    }
  }

}
</script>

<style lang="scss" scoped>
::v-deep .nav-tour-modal {
  margin-top: 6px;
  margin-left: 228px;
}

.icon {
  color: #606974;
}
</style>
