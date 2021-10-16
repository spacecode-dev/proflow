<template>
  <div>
    <h4 class="text-center font-weight-bold pb-3">Company Details</h4>

    <validation-observer ref="observer" v-slot="{ handleSubmit }">
      <b-form class="pt-4" @submit.stop.prevent="handleSubmit(onSubmit)">
        <validation-provider
          name="company name"
          :rules="{ required: true, min: 3 }"
          v-slot="validationContext"
        >
          <b-form-group label="Company Name" label-for="company-name">
            <b-form-input
              id="company-name"
              name="company_name"
              v-model="form.name"
              aria-describedby="company-name-feedback"
            />

            <b-form-invalid-feedback id="company-name-feedback">{{ validationContext.errors[0] }}
            </b-form-invalid-feedback>
          </b-form-group>
        </validation-provider>

        <validation-provider
          name="workspace url"
          :rules="{ required: true, regex: /^[A-Za-z0-9]+$/ }"
          v-slot="validationContext"
        >
          <b-form-group label="WORKSPACE URL" label-for="workspace-url">
            <div class="d-flex flex-column flex-md-row align-items-md-center">
              <b-form-input
                id="workspace-url"
                name="workspace_url"
                v-model="form.workspace_url"
                :state="getValidationState(validationContext)"
                aria-describedby="workspace-url-feedback"
              />
              <p class="mt-2 mt-md-0 mb-0 ml-2 input-group-append">.{{ domain }}</p>
            </div>

            <b-form-invalid-feedback id="workspace-url-feedback">{{ validationContext.errors[0] }}
            </b-form-invalid-feedback>
          </b-form-group>
        </validation-provider>

        <b-button type="submit" class="font-weight-bold btn-block mt-md-5 mb-1" variant="primary">Next</b-button>
      </b-form>
    </validation-observer>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
  name: 'CompanyDetail',
  data () {
    return {
      form: {
        name: '',
        workspace_url: ''
      },
      domain: process.env.VUE_APP_DOMAIN_NAME
    }
  },

  created () {
    this.form = Object.assign({}, this.user.company_detail)
  },

  computed: {
    ...mapGetters(['user'])
  },

  methods: {
    ...mapActions(['saveCompanyDetail']),

    async onSubmit () {
      await this.saveCompanyDetail(this.form)
      this.$router.push({ name: 'personal-detail', params: { forceRedirect: true } })
    }
  }
}
</script>
