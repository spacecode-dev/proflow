<template>
  <div class="d-flex flex-column">
    <b-button class="btn-add-user" variant="primary" v-b-modal.add-user-modal>Add User</b-button>

    <validation-observer ref="form">
      <b-modal
        id="add-user-modal"
        modal-class="settings-modal"
        body-class="px-4 py-3"
        ok-title="Send"
        cancel-variant="outline-secondary"
        hide-header
        centered
        @ok="onSubmit($event)"
      >
        <div class="px-2 py-1">
          <p class="modal-title">Add User</p>

          <b-form class="form-no-valid-icons">
            <validation-provider
              name="email"
              :rules="{ required: true, email: true }"
              v-slot="validationContext"
            >
              <b-form-group label="EMAIL" label-for="email">
                <b-form-input
                  id="email"
                  name="email"
                  v-model="form.email"
                  :state="getValidationState(validationContext)"
                  aria-describedby="email-feedback"
                />

                <b-form-invalid-feedback id="email-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
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
  name: 'AddUser',
  data () {
    return {
      form: {
        email: ''
      }
    }
  },
  methods: {
    ...mapActions(['addMember']),

    async onSubmit (bvModalEvt) {
      bvModalEvt.preventDefault()
      const validate = await this.$refs.form.validate()
      if (validate) {
        const data = await this.addMember(this.form)
        if (data) {
          this.$emit('output-data')
          this.$bvModal.hide('add-user-modal')
        }
      }
    }

  }
}
</script>

<style lang="scss" scoped>
  .btn-add-user {
    width: 138px;
  }
</style>
