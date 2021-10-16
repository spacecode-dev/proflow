<template>
  <div>
    <img
      src="@/assets/images/icons/interface.svg"
      class="img-fluid mx-auto d-flex"
      alt="send"
    >
    <h4 class="text-center font-weight-bold my-4">Verify Email</h4>

    <b-row class="text-center" align-h="around">
      <b-col cols="12" md="6">
        <p class="mt-2">You’re almost there! We sent an email to <b>{{email}}</b></p>
      </b-col>
    </b-row>

    <b-row class="text-center py-4" align-h="around">
      <b-col cols="12" md="7">
        <p>
          Just click on the link in that email to complete your signup. If you don’t see it you may need to check your
          <b>spam folder.</b>
        </p>
      </b-col>
    </b-row>

    <b-row align-h="around" class="pt-4">
      <b-col cols="12" md="4">
        <p class="text-center">Still can't find the email?</p>
        <b-button @click="onSubmit" class="font-weight-bold btn-block mt-3" variant="primary">Resend Email</b-button>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import { mapActions } from 'vuex'

export default {
  name: 'CompanyDetail',
  props: ['email'],

  methods: {
    ...mapActions(['resendEmail']),

    onSubmit () {
      this.resendEmail({ email: this.email })
    }
  }
}
</script>
