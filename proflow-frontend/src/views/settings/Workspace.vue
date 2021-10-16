<template>
  <div class="d-flex flex-column h-100">
    <p class="modal-title">Workspace Settings</p>

    <validation-observer ref="workspaceForm" class="d-flex flex-column flex-grow-1">
      <b-form class="form-no-valid-icons content-divider">
        <p class="font-weight-bold">Workspace Logo</p>
        <b-row align-v="stretch" class="pb-3">
          <b-col cols="auto">

            <b-avatar v-if="form.logo || imageData" :src="imageData" size="4.375rem"/>
            <b-avatar v-else :text="initial" size="4.375rem"></b-avatar>
            <b-progress v-if="isLoading" :value="uploadPercentage" :max="max" show-progress animated
                        class="mt-2"></b-progress>

          </b-col>
          <b-col align-self="center">
            <b-button variant="outline-secondary btn-change-photo" @click="onBtnClick">Change Photo</b-button>
            <b-form-file accept="image/*" @change="previewImage" v-model="form.logo" ref="fileInput" id="upload_file"
                         class="d-none mt-3" no-drop/>
          </b-col>
        </b-row>
        <hr class="mt-4 mb-4 pb-3"/>
        <validation-provider
          name="workspace name"
          :rules="{ required: true, min: 3 }"
          v-slot="validationContext"
        >
          <b-form-group label="Workspace Name" label-for="name">
            <b-form-input
              :state="getValidationState(validationContext)"
              v-model="form.name"
              aria-describedby="name-feedback"
            />
            <b-form-invalid-feedback id="fullname-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
          </b-form-group>
        </validation-provider>

        <validation-provider
          name="url"
          v-slot="validationContext"
        >
          <b-form-group
            label="Workspace Url"
            label-for="workspace_url"
          >
            <b-form-input
              :state="getValidationState(validationContext)"
              v-model="form.workspace_url"
              aria-describedby="workspace_url-feedback"
              :disabled="workspaceDisabled"
            />
            <b-form-invalid-feedback id="workspace_url-feedback">{{ validationContext.errors[0] }}
            </b-form-invalid-feedback>
          </b-form-group>
        </validation-provider>
        <hr/>
      </b-form>
    </validation-observer>

    <hr class="action-divider"/>
    <div class="d-flex justify-content-end modal-action mb-1">
      <b-button
        class="mr-3"
        type="submit"
        variant="outline-secondary"
        @click="$emit('cancel')"
      >
        Cancel
      </b-button>

      <b-button
        type="submit"
        variant="primary"
        @click="onSubmit()"
      >
        Update
      </b-button>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'

export default {
  name: 'WorkspaceSettings',
  data () {
    return {
      imageData: '',
      initial: '',
      uploadPercentage: 0,
      max: 100,
      isLoading: false,
      workspaceDisabled: true,
      form: {
        name: '',
        workspace_url: '',
        logo: []
      }
    }
  },

  computed: {
    ...mapGetters(['user', 'upload'])

  },
  watch: {
    'form.profile_picture': function (val, oldVal) {
      if (val) {
        this.fileChanged = true
      }
    }
  },
  created () {
    this.initial = this.user.company_detail.name.length > 0 ? this.user.company_detail.name.charAt(0).toUpperCase() : ''
    this.form = Object.assign({}, this.user.company_detail)

    this.imageData = this.form.logo
    this.form.logo = []

    // success
  },

  methods: {
    ...mapActions(['updateCompanyDetail']),

    async updateAccountDetails () {
      const formData = new FormData()
      formData.append('name', this.form.name)
      formData.append('workspace_url', this.form.workspace_url)
      formData.append('logo', this.form.logo)
      formData.append('id', this.form.id)
      formData.append('_method', 'patch')
      this.uploadPercentage = this.upload
      const data = await this.updateCompanyDetail(formData)
      if (data) {
        setTimeout(function () {
          this.isLoading = false
          this.uploadPercentage = 0
          this.fileChanged = false
        }.bind(this), 500)
      }
    },
    previewImage: function (event) {
      this.form.logo = event.target.files[0]
      var input = event.target
      // Ensure that you have a file before attempting to read it
      if (input.files && input.files[0]) {
        // create a new FileReader to read this image and convert to base64 format
        var reader = new FileReader()
        // Define a callback function to run, when FileReader finishes its job
        reader.onload = (e) => {
          // Note: arrow function used here, so that "this.imageData" refers to the imageData of Vue component
          // Read image as base64 and set to imageData
          this.imageData = e.target.result
        }
        // Start the reader job - read file as a data url (base64 format)
        reader.readAsDataURL(input.files[0])
      }
    },
    onBtnClick (event) {
      console.log(this.$refs.fileInput.$el.childNodes[0].click())
    },

    async onSubmit () {
      if (this.fileChanged === true) {
        this.isLoading = true
      }
      const valid = await this.$refs.workspaceForm.validate()
      if (valid) {
        this.updateAccountDetails()
      }
    }

  }
}
</script>
