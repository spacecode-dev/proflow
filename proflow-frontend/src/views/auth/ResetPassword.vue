<template>
  <div>
    <h2 class="text-center font-weight-bold">Reset Password</h2>

    <validation-observer ref="observer" v-slot="{ handleSubmit }">
      <b-form class="form-no-valid-icons" @submit.stop.prevent="handleSubmit(onSubmit)">
        <validation-provider
          name="password"
          rules="required|min:6"
          v-slot="validationContext"
        >
          <b-form-group label="Password" label-for="password">
            <b-form-input
              id="password"
              name="password"
              type="password"
              v-model="form.password"
              :state="getValidationState(validationContext)"
              aria-describedby="password-feedback"
            />

            <b-form-invalid-feedback id="password-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
          </b-form-group>
        </validation-provider>

        <validation-provider
          name="confirm password"
          rules="required|confirmed:password"
          v-slot="validationContext"
        >
          <b-form-group label="Confirm Password" label-for="confirm_password">
            <b-form-input
              id="confirm_password"
              name="confirm_password"
              type="password"
              v-model="form.confirm_password"
              :state="getValidationState(validationContext)"
              aria-describedby="confirm-password-feedback"
            />

            <b-form-invalid-feedback id="confirm-password-feedback">{{ validationContext.errors[0] }}
            </b-form-invalid-feedback>
          </b-form-group>
        </validation-provider>

        <b-button type="submit" class="font-weight-bold btn-block mt-md-5 mb-1" variant="primary">Submit</b-button>
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
        password: ''

      }
    }
  },
  mounted () {
    const isExpired = this.$route.query.is_expired
    this.form.token = this.$route.query.token
    this.form.email = this.$route.query.email
    if (isExpired) {
      this.$bus.$emit('error', 'Sorry Your Password reset link has been expired. You can always request a new one.')
      // if link is expired then redirect to forgot password page again
      this.$router.push({ name: 'forgot-password' })
    }
  },

  methods: {
    ...mapActions(['resetPassword']),

    async onSubmit () {
      await this.resetPassword(this.form)
      this.$router.push({ name: 'login' })
    }
  }
}
</script>
