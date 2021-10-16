<template>
  <div class="d-flex flex-column">
    <validation-observer ref="checkform">
      <b-modal
        v-model="showModal"
        id="check-password-modal"
        modal-class="settings-modal"
        dialog-class="pf-modal-small"
        body-class="px-4 py-3"
        ok-title="Confirm"
        cancel-variant="outline-secondary"
        hide-header
        centered
        @ok="onSubmit($event)"
        @cancel="$emit('cancel')"
      >
        <div class="px-2 py-1">
          <p class="modal-title">{{headerTitle}}</p>

          <b-form class="form-no-valid-icons">
            <validation-provider name="filename" rules="required" v-slot="validationContext">
              <b-form-group label="File Name" label-for="filename">
                <b-form-input
                  type="text"
                  v-model="form.name"
                  :state="getValidationState(validationContext)"
                  aria-describedby="filename-feedback"
                />
                <b-form-invalid-feedback id="filename-feedback">{{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
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
  name: 'RenameFile',
  props: ['headerTitle', 'fileName', 'fileId'],

  data () {
    return {
      showModal: true,
      form: {
        name: this.fileName,
        id: this.fileId
      }
    }
  },
  methods: {
    ...mapActions(['updateUploadedFile']),
    async onSubmit (bvModalEvt) {
      bvModalEvt.preventDefault()
      const valid = await this.$refs.checkform.validate()
      if (valid) {
        const formData = new FormData()
        formData.append('id', this.form.id)
        formData.append('name', this.form.name)
        const update = await this.updateUploadedFile(formData)
        if (update) {
          this.$emit('clicked', update)
        }
      }
    }
  }
}
</script>
