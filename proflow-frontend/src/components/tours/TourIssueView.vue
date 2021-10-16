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

      <b-col class="pl-1">
        <div class="font-weight-semibold">Issue View</div>
        <div class="mt-3 mb-5">
          <p class="text-black-75">
            The key to raising issues is to clearly communicate any problems, why resolving it is important and what the ideal output looks like.
          </p>
          <p class="text-black-75">
            ProFlow gives you the structure to do just this. Ensure you include the key information then share it with your team.
          </p>
        </div>
      </b-col>
    </b-row>
    <b-button variant="primary" class="d-flex ml-auto mb-1 mr-3 px-4 btn-tour-next"  @click="onSubmit">
      <span class="mx-2">Next</span>
    </b-button>
  </b-modal>
</template>

<script>
import { mapActions } from 'vuex'
export default {
  name: 'TourIssueView',
  props: {
    show: {
      type: Boolean,
      default: true
    }
  },
  methods: {
    ...mapActions(['saveOnboardingDetail']),
    async onSubmit () {
      await this.saveOnboardingDetail({ on_boarding_step: 4 })
    }
  }

}
</script>
