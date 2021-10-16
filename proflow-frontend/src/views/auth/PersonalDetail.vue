<template>
  <div class="h-100">
    <BtnArrow
      v-if="!user.user_detail.invited_by"
      class="position-absolute mt-md-4"
      position="left"
      @click="goToCompanyDetail"
    />
    <AppAvatar :text="user.company_detail.name" :show-text = "true" class="pt-0 pt-md-4 pb-md-5"/>

    <h4 class="text-center font-weight-bold pt-4 pb-3 mb-0">Personal Details</h4>

    <validation-observer ref="observer" v-slot="{ handleSubmit }">
      <b-form class="form-no-valid-icons mt-3 mt-md-5" @submit.stop.prevent="handleSubmit(onSubmit)">
        <validation-provider
          name="full name"
          :rules="{ required: true, min: 3 }"
          v-slot="validationContext"
        >
          <b-form-group label="Full Name" label-for="fullname">
            <b-form-input
              id="fullname"
              name="fullname"
              v-model="form.name"
              :state="getValidationState(validationContext)"
              aria-describedby="fullname-feedback"
            />
            <b-form-invalid-feedback id="fullname-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
          </b-form-group>
        </validation-provider>

        <validation-provider name="department" :rules="{ required: true }" v-slot="validationContext">
          <b-form-group label="Department" label-for="department">
            <b-form-select
              id="department"
              name="department"
              v-model="form.department_id"
              :options="options"
              :state="getValidationState(validationContext)"
              aria-describedby="department-feedback"
            />

            <b-form-invalid-feedback id="department-feedback">{{ validationContext.errors[0] }}
            </b-form-invalid-feedback>
          </b-form-group>
        </validation-provider>

        <b-form-group>
          <b-row>
            <b-col cols="12" md="8">
              <p class="px-3 mb-2">Are you managing people at your company:</p>
            </b-col>
            <b-col cols="12" md="4" class="d-flex justify-content-md-center pl-md-0">
              <SwitchInput
                v-model="isManagingPeople"
                @change="v => form.managing_people = v"
                class="mx-2 mx-md-0"
              />
            </b-col>
          </b-row>
        </b-form-group>

        <b-button type="submit" class="font-weight-bold btn-block mt-5 mb-1" variant="primary">Next</b-button>
      </b-form>
    </validation-observer>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

import BtnArrow from '@/components/buttons/BtnArrow'
import SwitchInput from '@/components/inputs/SwitchInput'

export default {
  name: 'PersonalDetail',
  components: { BtnArrow, SwitchInput },

  data () {
    return {
      form: {
        name: null,
        department_id: null,
        managing_people: false
      },
      options: [
        { value: 1, text: 'Product' },
        { value: 2, text: 'Engineering' },
        { value: 3, text: 'Operations' },
        { value: 4, text: 'Marketing' },
        { value: 4, text: 'Sales' },
        { value: 6, text: 'Finance' },
        { value: 7, text: 'Account Management' },
        { value: 8, text: 'Customer Success' },
        { value: 9, text: 'Other' }
      ]
    }
  },

  computed: {
    ...mapGetters(['user']),

    isManagingPeople () {
      return !!this.form.managing_people
    }
  },

  created () {
    this.form.name = this.user.name
    this.form.department_id = this.user.user_detail.department_id
    this.form.managing_people = this.user.user_detail.managing_people
  },

  methods: {
    ...mapActions(['saveUserDetail']),
    async onSubmit () {
      await this.saveUserDetail(this.form)
      this.$router.push({ name: 'invite-user' })
    },
    goToCompanyDetail () {
      this.$router.push({ name: 'company-detail', params: { forceRedirect: true } })
    }
  }
}
</script>
