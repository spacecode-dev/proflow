<template>
  <div class="d-flex flex-column">
    <validation-observer ref="checkform">
      <b-modal
          v-model="showModal"
          id="check-password-modal"
          modal-class="settings-modal"
          dialog-class="pf-modal-small"
          body-class="px-4 py-3"
          ok-title="Confirm"
          cancel-variant="outline-secondary"
          hide-header
          centered
          @ok="onSubmit($event)"
          @cancel="$emit('cancel')"
      >
        <div class="px-2 py-1">
          <p class="modal-title">{{ headerTitle }}</p>

          <b-form class="form-no-valid-icons">
            <validation-provider name="password" rules="required" v-slot="validationContext">
              <b-form-group label="PASSWORD" label-for="password">
                <b-form-input
                    type="password"
                    v-model="form.current_password"
                    :state="getValidationState(validationContext)"
                    aria-describedby="password-feedback"
                />
                <b-form-invalid-feedback id="password-feedback">{{ validationContext.errors[0] }}
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
  name: 'CheckCurrentPassword',
  props: ['headerTitle'],
  data () {
    return {
      showModal: true,
      form: {
        current_password: ''
      }
    }
  },
  methods: {
    ...mapActions(['updatePassword']),
    async onSubmit (bvModalEvt) {
      bvModalEvt.preventDefault()
      const valid = await this.$refs.checkform.validate()
      if (valid) {
        const update = await this.updatePassword(this.form)
        if (update) {
          this.showModal = false
          this.$emit('clicked')
        }
      }
    }
  }
}
</script>
