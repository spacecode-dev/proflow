<template>
  <div class="bg-info rounded position-relative file-input-wrapper px-3">

    <div
      v-if="!file.name && !uploadedFile"
      class="d-flex align-items-center position-absolute upload-file cursor-pointer"
    >
      <AppIcon icon="icon-pf-upload" class="icon" />
      <div class="w-75">
        <p class="pf-heading font-weight-semibold mb-2">Upload file</p>
        <p class="pf-subheading mb-0">Or Drag and Drop</p>
      </div>
    </div>
    <div v-if="!file.name" class="d-flex align-items-center">
      <AppIcon v-if="uploadedFile" icon="icon-pf-upload" class="icon" />
      <b-form-file
        ref="fileInput"
        class="h-100"
        :class="{ 'has-file': !!uploadedFile }"
        :value="uploadedFile"
        @input="onFileChange"
      />
    </div>
    <div v-else class="d-flex align-items-center h-100">
      <AppIcon icon="icon-pf-draft" class="icon" />
      <div class="w-75">
        <p class="pf-heading text-truncate font-weight-semibold mb-1">
          <a :href="file.url" target="_blank" download>{{ file.name }}</a>
        </p>
        <p class="pf-subheading mb-0">Uploaded by {{ file.uploaded_by }}</p>
      </div>
      <b-dropdown
        class="position-absolute file-actions"
        menu-class="file-actions-menu"
        variant="text"
        right
        no-caret
      >
        <template v-slot:button-content>
          <AppIcon icon="icon-pf-dots-horizontal" />
        </template>
        <b-dropdown-item @click="renameFile">
          <span class="pf-heading">Rename</span>
        </b-dropdown-item>
        <b-dropdown-item @click="removeFile(file.id)">
          <span>
            <span class="pf-heading">Delete</span>
          </span>
        </b-dropdown-item>
      </b-dropdown>
    </div>
    <RenameFile
      v-if="showRenameModal"
      :file-name="file.name"
      :file-id="file.id"
      header-title="Rename File"
      @clicked="getIssueFileData"
      @cancel="showRenameModal = false"
    />
  </div>
</template>

<script>
import RenameFile from '@/views/issue/RenameFile'
export default {
  name: 'AppFileInput',
  components: { RenameFile },
  props: {
    file: {
      type: Object,
      default: () => ({})
    }
  },
  data () {
    return {
      showRenameModal: false,
      uploadedFile: this.value
    }
  },
  methods: {
    onFileChange (file) {
      this.uploadedFile = file
      this.$emit('upload', this.uploadedFile)
    },
    renameFile () {
      this.showRenameModal = true
    },
    removeFile (id) {
      this.$emit('remove', id)
    },
    getIssueFileData (data) {
      this.showRenameModal = false
      this.$emit('getData', data)
    }
  }
}
</script>

<style lang="scss" scoped>
.file-input-wrapper {
  height: 58px;
}

.upload-file {
  z-index: 1;
  width: auto;
  height: 100%;
  left: 25px;
}

.has-file {
  ::v-deep .custom-file-label {
    visibility: visible;
    padding-top: 20px;
  }
}

.file-actions {
  top: 0px;
  right: 0px;
}

.w-75 {
  margin-left: 5px;
}

::v-deep .icon {
  font-size: 26px;
}

::v-deep .file-actions-menu {
  top: -12px !important;
  left: -15px !important;
}

::v-deep .custom-file-input {
  cursor: pointer;
  min-width: 100%;
  height: 58px;
}

::v-deep .custom-file-label {
  visibility: hidden;
  background-color: transparent;
  border: none;

  &::after {
    display: none;
  }

  &:focus {
    outline: none;
  }
}
</style>
