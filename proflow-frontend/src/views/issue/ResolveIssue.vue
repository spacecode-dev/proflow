<template>
  <div class="d-flex flex-column">
    <validation-observer ref="checkform">
      <b-modal

        v-model="showModal"
        id="resolve-modal"
        modal-class="resolve-modal"
        dialog-class="pf-modal-small"
        body-class="px-4 py-3"
        ok-title="Resolve"
        cancel-variant="outline-secondary"
        hide-header
        centered
        @ok="onSubmit($event)"
        @cancel="$emit('cancel')"
      >
        <div class="px-2 py-1">
          <b-row>
          <b-col>
            <p class="modal-title"> <AppIcon icon="icon-pf-check-round" /> {{headerTitle}}</p>
          </b-col>
        </b-row>

        <p class="pf-subheading mb-5">To resolve the issue, please write the short summary of acheived result.</p>

          <b-form class="form-no-valid-icons">
            <validation-provider name="resolve text" rules="required" v-slot="validationContext">
              <b-form-group  label-for="resolveissue">
              <b-form-textarea
              id="textarea-default"
              v-model="form.resolve_text"
              :state="getValidationState(validationContext)"
               placeholder="Type here.."
               aria-describedby="resolveissue-feedback"
               rows="8"

              ></b-form-textarea>

                <b-form-invalid-feedback id="resolveissue-feedback">{{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </b-form>
        </div>
      </b-modal>
    </validation-observer>

  </div>
</template>

<script>
import { mapActions } from 'vuex'

export default {
  name: 'ResolveIssue',
  props: {
    headerTitle: {
      type: String,
      default: ''
    },
    issueId: {
      type: String,
      default: ''
    }

  },
  data () {
    return {
      showModal: true,
      form: {
        issue_id: this.issueId,
        is_resolved: 1
      }
    }
  },
  methods: {
    ...mapActions(['updateIssueStatus']),
    async onSubmit (bvModalEvt) {
      bvModalEvt.preventDefault()
      const valid = await this.$refs.checkform.validate()
      if (valid) {
        const update = await this.updateIssueStatus(this.form)
        if (update) {
          this.$emit('clicked', update)
        }
      }
    }
  }
}
</script>

<style lang="scss">
   #textarea-default{
    height: 243px !important;
  }
</style>
