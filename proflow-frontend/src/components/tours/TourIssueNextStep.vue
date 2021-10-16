<template>
  <b-modal
    v-model="show"
    dialog-class="tour-modal-issue-view"
    centered
    hide-header
    hide-footer
    no-close-on-backdrop
  >
    <b-row>
      <b-col cols="auto">
        <img src="@/assets/images/icons/group.svg" />
      </b-col>

      <b-col class="pl-2">
        <div class="font-weight-semibold">Next Steps</div>
        <div class="mt-3 mb-5">
          <p class="text-black-75">
            Clearly stated and assigned next steps are crucial to progressing issues. Use them to maintain transparency and accountability, like an open to-do list for your team.
          </p>
          <p class="text-black-75 pb-3">
            Don't worry if you're not 100% sure what the next steps should be. Think of them like a first draft - they may well change but they'll help everyone get started!
          </p>
          <div class="d-flex border py-2 px-2 rounded mr-3 mb-3">
            <p class="pl-1 text-black-90 mb-0 font-14">Ashley to define templates</p>
            <img src="@/assets/images/default/women.svg" class="ml-auto pr-1"/>
          </div>
          <div class="pt-3 px-2 text-muted">
            <div class="d-flex align-items-center pl-1 mb-3 font-14">
              <AppIcon icon="icon-pf-comment" class="mr-2" />
              <span class="pf-heading">Comment</span>
            </div>
            <div class="d-flex align-items-center pl-1 mb-3 font-14">
              <AppIcon icon="icon-pf-calendar" class="mr-2" />
              <span class="pf-heading">Set Due Dates</span>
            </div>
            <div class="d-flex align-items-center pl-1 mb-3 font-14">
              <AppIcon icon="icon-pf-draft" class="mr-2" />
              <span class="pf-heading">Upload Files</span>
            </div>
            <div class="d-flex align-items-center pl-1 mb-3 font-14">
              <b-avatar-group size="20px" class="mr-2">
                <b-avatar :src="require('@/assets/images/default/women1.svg')"></b-avatar>
                <b-avatar :src="require('@/assets/images/default/women.svg')"></b-avatar>
              </b-avatar-group>
              <span class="pf-heading">Assign Tasks</span>
            </div>
          </div>
        </div>
      </b-col>
    </b-row>
    <b-button variant="primary" class="d-flex ml-auto mb-1 mr-3 px-4 btn-tour-next" @click="onSubmit">
      <span class="mx-2">Next</span>
    </b-button>
  </b-modal>
</template>

<script>
import { mapActions } from 'vuex'
export default {
  name: 'TourIssueNextStep',
  props: {
    show: {
      type: Boolean,
      default: true
    }
  },
  methods: {
    ...mapActions(['saveOnboardingDetail']),
    async onSubmit () {
      await this.saveOnboardingDetail({ on_boarding_step: 5 })
    }
  }
}
</script>

<style lang="scss" scoped>
.font-14 {
  font-size: 14px;
}
</style>
