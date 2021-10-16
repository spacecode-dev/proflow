<template>
  <div class="d-flex flex-column">
    <validation-observer ref="checkform">
      <b-modal
        v-model="showModal"
        id="send-feedback-modal"
        modal-class="settings-modal"
        dialog-class="pf-modal-small"
        body-class="px-4 py-3"
        ok-title="Send"
        cancel-variant="outline-secondary"
        hide-header
        centered
        @ok="onSubmit($event)"
        @cancel="$emit('cancel')"
      >
        <div class="px-2 py-1">
          <b-row>
          <b-col>
            <p class="modal-title">{{headerTitle}}</p>
          </b-col>
        </b-row>

       <b-form class="form-no-valid-icons">
         <validation-provider
            name="feedback type"
            :rules="{ required: true }"
            v-slot="validationContext"
          >
            <b-form-group
              label="Feedback Type"
              label-for="type"
            >

              <b-form-select
                id="type"
                name="type"
                v-model="form.type"
                :options="options"
                :class="{ 'feedback-default' : form.type == null }"
                class="feedback-select"
                :state="getValidationState(validationContext)"
                aria-describedby="type-feedback"
              />
              <b-form-invalid-feedback id="type-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
            </b-form-group>

          </validation-provider>
            <validation-provider name="text" rules="required" v-slot="validationContext">
              <b-form-group  label-for="feedbacktext">
              <b-form-textarea
              id="textarea-default"
              v-model="form.text"
              :state="getValidationState(validationContext)"
              placeholder="Type here.."
              aria-describedby="feedbacktext-feedback"
              rows="8"
              ></b-form-textarea>

                <b-form-invalid-feedback id="feedbacktext-feedback">{{ validationContext.errors[0] }}
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
  name: 'SendFeedback',
  props: {
    headerTitle: {
      type: String,
      default: ''
    }
  },
  data () {
    return {
      showModal: true,
      form: {
        type: null,
        text: ''
      },
      options: [
        { value: null, text: 'Feedback Type', disabled: true },
        { value: 1, text: 'Bug' },
        { value: 2, text: 'UX Issue' },
        { value: 3, text: 'Feature Request' },
        { value: 4, text: 'Other' }
      ]
    }
  },
  methods: {
    ...mapActions(['sendFeedback']),
    async onSubmit (bvModalEvt) {
      bvModalEvt.preventDefault()
      const valid = await this.$refs.checkform.validate()
      if (valid) {
        const update = await this.sendFeedback(this.form)
        if (update) {
          this.$emit('clicked', update)
        }
      }
    }
  }
}
</script>
<style lang='scss' scoped>

 ::v-deep .feedback-select {

    &.feedback-default {
      color: var(--secondary);
    }
    option {
      &:first-child {
        display:none;
      }
        &:not(:first-child) {
        color: var(--black-90) !important;
      }
    }
  }

</style>
