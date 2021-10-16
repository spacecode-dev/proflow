<template>
  <div class="d-flex flex-column">
    <label>Password</label>
    <b-button variant="outline-secondary" v-b-modal.change-password-modal>Change Password</b-button>

    <validation-observer ref="form">
      <b-modal
        id="change-password-modal"
        modal-class="settings-modal"
        dialog-class="pf-modal-small"
        body-class="px-4 py-3"
        ok-title="Done"
        cancel-variant="outline-secondary"
        hide-header
        centered
        @ok="onSubmit($event)"
        @show="onShow($event)"
      >
        <div class="px-2 py-1">
          <p class="modal-title">Change Password</p>

          <b-form class="form-no-valid-icons">
            <validation-provider name="old password" rules="required|min:6" v-slot="validationContext">
              <b-form-group label="OLD PASSWORD" label-for="current_password">
                <b-form-input
                  id="current_password"
                  name="current_password"
                  type="password"
                  v-model="form.current_password"
                  :state="getValidationState(validationContext)"
                  aria-describedby="current_password-feedback"
                />

                <b-form-invalid-feedback id="current_password-feedback">{{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>

            <validation-provider name="password" rules="required|min:6"
                                 v-slot="validationContext">
              <b-form-group label="NEW PASSWORD" label-for="new_password">
                <b-form-input
                  id="new_password"
                  name="new_password"
                  type="password"
                  ref="new_password"
                  :state="getValidationState(validationContext)"
                  v-model="form.new_password"
                  aria-describedby="password-feedback"
                />

                <b-form-invalid-feedback id="password-feedback">{{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>

            <validation-provider name="confirm password" rules="required|password:@password" v-slot="validationContext">
              <b-form-group label="CONFIRM PASSWORD" label-for="confirm_password">
                <b-form-input
                  :state="getValidationState(validationContext)"
                  type="password"
                  v-model="confirm_password"
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
  name: 'ChangePassword',
  data () {
    return {
      confirm_password: '',
      form: {
        current_password: '',
        new_password: ''
      }
    }
  },
  methods: {
    ...mapActions(['updatePassword']),

    async onSubmit (bvModalEvt) {
      bvModalEvt.preventDefault()
      const valid = await this.$refs.form.validate()
      if (valid) {
        await this.updatePassword(this.form)
        this.$bvModal.hide('change-password-modal')
      }
    },
    onShow (bvModalEvt) {
      this.form = {}
      this.confirm_password = ''
    }
  }
}
</script>
