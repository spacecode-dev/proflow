<template>
  <div>
    <h2 class="text-center font-weight-bold">Sign Up</h2>

    <ConnectWithGoogle/>

    <Separator/>

    <validation-observer ref="observer" v-slot="{ handleSubmit }">
      <b-form class="form-no-valid-icons" @submit.stop.prevent="handleSubmit(onSubmit)">
        <validation-provider
          name="email"
          :rules="{ required: true, email: true }"
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

        <validation-provider name="password" :rules="{ required: true, min:6 }" v-slot="validationContext">
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

        <b-button type="submit" class="font-weight-bold btn-block mt-md-5 mb-1" variant="primary">Sign Up</b-button>
        <p class="mt-2 text-center mb-5">
          <span class="mr-2">Already have an account?</span>
          <router-link :to="{ name: 'login' }">Log In</router-link>
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
  name: 'SignUp',
  components: { ConnectWithGoogle, Separator },

  data () {
    return {
      form: {
        email: '',
        password: ''
      }
    }
  },

  methods: {
    ...mapActions(['register']),

    async onSubmit () {
     await this.register(this.form)
//             var USER_ID = "12148";
// var USER_SIGNUP_DATE = "2020-01-02T21:07:03Z";

// this.$mixpanel.track(
//     "Played song",
//     {"genre": "hip-hop"}
// );
      
     const email = this.form.email
      this.$router.push({ name: 'verify-email', params: { email } })
    }
  }
}
</script>
