<template>
  <div>
    <h2 class="text-center font-weight-bold">Log In</h2>

    <ConnectWithGoogle @authorize="status => isSignIn = status"/>

    <Separator/>

    <validation-observer ref="observer" v-slot="{ handleSubmit }">
      <b-form class="form-no-valid-icons" @submit.stop.prevent="handleSubmit(onSubmit)">
        <validation-provider
          name="email"
          :rules="{ required: true, email:true }"
          v-slot="validationContext"
        >
          <b-form-group label="WORK EMAIL" label-for="email">
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

        <validation-provider name="password" :rules="{ required: true }" v-slot="validationContext">
          <b-form-group label="PASSWORD" label-for="password">
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

        <b-button type="submit" class="font-weight-bold btn-block mt-md-5 mb-1" variant="primary">Log In</b-button>
        <p class="mt-2 text-center">
          <span class="mr-2">Don’t have an account?</span>
          <router-link :to="{ name: 'sign-up' }">Sign Up</router-link>
        </p>
        <p class="mt-2 text-center">
          <router-link :to="{ name: 'forgot-password' }">Forgot Your Password?</router-link>
        </p>
      </b-form>
    </validation-observer>

  </div>
</template>

<script>

import { mapActions } from 'vuex'
import ConnectWithGoogle from '@/components/buttons/ConnectWithGoogle'
import Separator from '@/components/misc/Separator'

export default {
  name: 'Login',
  components: { ConnectWithGoogle, Separator },

  data () {
    return {

      form: {
        email: '',
        password: ''
      }
    }
  },

  mounted () {
    const isVerified = this.$route.query.is_verified
    const isExpired = this.$route.query.is_expired
    const email = this.$route.query.email
    const isAlreadyVerified = this.$route.query.is_already_verified

    if (isAlreadyVerified) {
      this.$bus.$emit('success', 'You have already verified your email')
      return
    }
    // direct visit - not redirected from backend
    if (!isVerified && !isExpired && !isAlreadyVerified) return

    // show toaster if redirected from backend - after email verification success or link expire
    const toastType = isVerified ? 'success' : 'error'
    const toastMessage = isVerified ? 'Email verified successfully' : 'Email verification link is expired. Please click Resend Email button to get verification link again.'
    this.$bus.$emit(toastType, toastMessage)

    // if link is expired then redirect to verify page again
    if (isExpired && email) {
      this.$router.push({ name: 'verify-email', params: { email } })
    }
  },

  methods: {
    ...mapActions(['login', 'googleAuth']),

    async onSubmit () {
      await this.login(this.form)
      this.$router.push({ name: 'company-detail' })
    }

  }
}
</script>
