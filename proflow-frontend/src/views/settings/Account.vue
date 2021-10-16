<template>
  <div class="d-flex flex-column h-100">
    <p class="modal-title">My Account</p>

    <div class="d-flex flex-column flex-grow-1">
      <validation-observer ref="accountForm">
        <b-form class="form-no-valid-icons">
          <p class="font-weight-bold">Profile Photo</p>
          <b-row align-v="stretch" class="pb-5">
            <b-col cols="auto">

              <b-avatar :src="imageData" size="4.375rem" />
              <b-progress  v-if="isLoading" :value="uploadPercentage" :max="max" show-progress animated class="mt-2"></b-progress>
            </b-col>
            <b-col align-self="center">
              <b-button variant="outline-secondary btn-change-photo" :class="{ 'mb-4' : isLoading }" @click="onBtnClick">Change Photo</b-button>
              <b-form-file accept="image/*" @change="previewImage" v-model="form.profile_picture" ref="fileInput"
                           id="upload_file" class="d-none mt-3" no-drop/>
            </b-col>
          </b-row>

          <validation-provider
            name="full name"
            :rules="{ required: true, min: 3 }"
            v-slot="validationContext"
          >
            <b-form-group label="Full Name" label-for="fullname">
              <b-form-input
                :state="getValidationState(validationContext)"
                id="fullname"
                name="fullname"
                v-model="form.name"
                aria-describedby="fullname-feedback"
              />
              <b-form-invalid-feedback id="fullname-feedback">{{ validationContext.errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <validation-provider
            name="email"
            :rules="{ required: true, email: true }"
            v-slot="validationContext"
          >
            <b-form-group
              label="Email Address"
              label-for="email"
            >
              <b-form-input
                :state="getValidationState(validationContext)"
                id="email"
                name="email"
                v-model="form.email"
                aria-describedby="email-feedback"
                :disabled="loginFromGoogle"
              />
              <small class="form-text">
                You can only change email address if the new one is under the same domain name.
              </small>
              <b-form-invalid-feedback id="email-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
            </b-form-group>

          </validation-provider>

          <validation-provider
            name="timezone"
            v-slot="validationContext"
          >
            <b-form-group
              label="Timezone"
              label-for="timezone"
            >
              <b-form-select
                id="department"
                name="department"
                v-model="form.timezone"

                :options="options"
                :state="getValidationState(validationContext)"
                aria-describedby="department-feedback"
              />
              <b-form-invalid-feedback id="email-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>

            </b-form-group>

          </validation-provider>
        </b-form>
        <hr/>
      </validation-observer>

      <ChangePassword v-if="!loginFromGoogle" class="mt-4 pb-5"/>
      <CheckCurrentPassword
        v-if="showPasswordModal"
        :header-title="headerTitle"
        @clicked="updateAccountDetails"
        @cancel="showPasswordModal = false"
      />
    </div>
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
        @click="form.email !== user.email ? confirmPassword() : onSubmit()"
      >
        Update
      </b-button>
    </div>
  </div>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'

import ChangePassword from '@/views/settings/ChangePassword'
import CheckCurrentPassword from '@/views/settings/CheckCurrentPassword'

export default {
  name: 'AccountSettings',
  components: { ChangePassword, CheckCurrentPassword },

  data () {
    return {
      fileChanged: false,
      imageData: '',
      uploadPercentage: 0,
      max: 100,
      isLoading: false,
      options: [],
      form: {
        email: '',
        name: '',
        profile_picture: []
      },
      headerTitle: 'Update Email',
      loginFromGoogle: false,
      showPasswordModal: false
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

  mounted () {
    this.getTimezoneListOptions()
  },

  created () {
    this.form.name = this.user.name
    this.form.email = this.user.email
    this.form.timezone = this.user.user_detail.timezone
    this.imageData = this.user.user_detail.profile_picture
    this.loginFromGoogle = (this.user.user_detail.google_token !== null)
  },

  methods: {
    ...mapActions(['updateProfile', 'getTimezoneList']),

    async updateAccountDetails () {
      const formData = new FormData()
      formData.append('profile_picture', this.form.profile_picture)
      formData.append('name', this.form.name)
      formData.append('email', this.form.email)
      formData.append('timezone', this.form.timezone)
      this.uploadPercentage = this.upload
      const data = await this.updateProfile(formData)
      this.uploadPercentage = 100
      if (data) {
        setTimeout(function () {
          this.isLoading = false
          this.uploadPercentage = 0
          this.fileChanged = false
        }.bind(this), 500)
      }
    },
    previewImage: function (event) {
      this.form.profile_picture = event.target.files[0]
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
      this.$refs.fileInput.$el.childNodes[0].click()
    },

    async confirmPassword () {
      const valid = await this.$refs.accountForm.validate()
      if (valid) {
        this.showPasswordModal = true
      }
    },

    async onSubmit () {
      /* eslint-disable */
      if (this.fileChanged) {
        this.isLoading = true
      }
      const valid = await this.$refs.accountForm.validate()
      if (valid) {
         this.updateAccountDetails()
      }
    },

    async getTimezoneListOptions () {
      this.options = await this.getTimezoneList()
    }
  }
}
</script>
