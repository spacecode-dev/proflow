<template>
  <div>
    <h2 class="text-center font-weight-bold">Reset Password</h2>

    <validation-observer ref="observer" v-slot="{ handleSubmit }">
      <b-form class="form-no-valid-icons" @submit.stop.prevent="handleSubmit(onSubmit)">
        <validation-provider
          name="email"
          :rules="{ required: true, email: true }"
          v-slot="validationContext"
        >
          <b-form-group label="We'll send a recovery link to" label-for="email">
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

        <b-button type="submit" class="font-weight-bold btn-block mt-md-5 mb-1" variant="primary">Send Password Reset
          Link
        </b-button>
        <p class="mt-2 text-center">
          <router-link :to="{ name: 'login' }">Return to Log in</router-link>
        </p>
      </b-form>
    </validation-observer>

  </div>
</template>

<script>
import { mapActions } from 'vuex'

export default {
  name: 'ForgotPassword',
  data () {
    return {
      form: {
        email: ''
      }
    }
  },

  methods: {
    ...mapActions(['forgotPassword']),

    async onSubmit () {
      await this.forgotPassword(this.form)
    }
  }
}
</script>
