<template>
  <div class="d-md-inline-flex align-items-center" :class="{ 'preview-only': moreTags === 0 }">
    <template v-for="(tag, index) in selectedTags">
      <b-form-tag
        v-if="moreTags > 0 ? index < 2 : index <= 2"
        :key="`selected-tag-${tag.id}`"
        :title="tag.title"
        tag-pills
        variant="info"
        class="tag mr-2 px-3"
        @remove="removeTag(tag.id)"
      />
    </template>

    <b-dropdown
      :class="{ show: isOpen }"
      variant="transparent"
      menu-class="select-tag-dropdown"
      no-caret
    >
      <template v-if="moreTags !== 0" v-slot:button-content>
        <b-form-tag
          v-if="moreTags > 0"
          :title="`+${moreTags} more`"
          tag-pills variant="info"
          class="tag mr-2 px-3"
          @remove="removeTag(tag.id)"
        />
        <div v-else-if="moreTags < 0"
             class="rounded-pill d-flex align-items-center justify-content-center tag dashed-border">
          Add Tag
        </div>
      </template>

      <b-form-tags no-outer-focus>
        <template v-slot>
          <b-form-tag
            v-for="tag in selectedTags"
            :key="`all-tag-${tag.id}`"
            :title="tag.title"
            tag-pills
            variant="info"
            class="tag mr-2 px-3 text-left"
            @remove="removeTag(tag.id)"
          />

          <b-form-input
            v-model="search"
            class="mt-2"
            type="search"
            size="sm"
            autocomplete="on"
          />

          <b-dropdown-divider class="divider"></b-dropdown-divider>
          <div v-if="availableTags.length > 0" class="d-flex flex-column">
            <b-form-tag
              v-for="tag in availableTags"
              :key="`available-tag-${tag.id}`"
              :title="tag.title"
              tag-pills
              variant="info"
              class="cursor-pointer available-tag mr-auto px-3 tag my-1 text-left"
              @click.native="addTag(tag)"

            />
          </div>
          <p v-else>{{ searchDesc }}</p>
          <b-form-invalid-feedback>
            Duplicate tag value cannot be added again!
          </b-form-invalid-feedback>
          <b-input-group-append v-if="search" class="my-2">
            <span @click="createTag(search)" class="pf-subheading cursor-pointer font-weight-semibold  mt-1 mr-3"
                  variant="primary">Create</span>
            <span
              class="badge b-form-tag d-inline-flex align-items-center font-weight-semibold  overflow-hidden mw-100 tag mt-1 px-2 badge-info">
              <span class="mx-auto">{{ search }}</span>
            </span>
          </b-input-group-append>
        </template>
      </b-form-tags>
    </b-dropdown>
  </div>
</template>

<script>
export default {
  name: 'TagInput',
  props: {
    value: {
      type: Array,
      default: () => ([])
    },
    tags: {
      type: Array,
      default: () => ([])
    }
  },
  data () {
    return {
      isOpen: true,
      unsavedTags: [],
      search: '',
      form: {}
    }
  },
  computed: {
    getAllTags () {
      return this.tags
    },
    selectedTags () {
      return this.value
    },
    moreTags () {
      return this.selectedTags.length - 3
    },
    criteria () {
      // Compute the search criteria
      return this.search.trim().toLowerCase()
    },
    availableTags () {
      const criteria = this.criteria

      // get already selected id from selected array
      const getValueId = this.value.map(x => x.id)

      // Filter out already selected options
      const options = this.tags.filter(opt => getValueId.indexOf(opt.id) === -1)

      if (criteria) {
        // Show only options that match criteria
        return options.filter(opt => opt.title.toLowerCase().indexOf(criteria) > -1)
      }
      // Show all options available
      return options
    },
    searchDesc () {
      if (this.criteria && this.availableTags.length === 0) {
        return 'There are no tags matching your search criteria'
      }
      return ''
    }
  },
  methods: {
    createTag (tag) {
      const mergedArray = this.getAllTags.concat.apply([], this.value)
      const tagFilter = mergedArray.filter(opt => opt.title.toLowerCase().indexOf(tag.toLowerCase().trim()) > -1)
      if (tagFilter.length === 0) {
        this.addTag(tag)
      }
    },
    addTag (tag) {
      this.$emit('add-tag', tag)
    },
    removeTag (tag) {
      this.$emit('remove-tag', tag)
    },
    onTagState (valid, invalid, duplicate) {
      this.duplicateTags = duplicate
    }

  }
}
</script>

<style lang="scss" scoped>
  .preview-only {
    padding-top: 10px;
  }

  .tag {
    min-width: 92px;
    height: 20px;
    color: var(--black-75);
    font-size: 11px;
    border-radius: 20px;
    align-items: center !important;
  }

  .dashed-border {
    border: 1px dashed var(--black-75);
  }

  .divider {
    margin-left: -15px;
    margin-right: -15px;
  }

  ::v-deep .available-tag {
    .b-form-tag-remove {
      display: none;
    }
  }

  ::v-deep .dropdown-toggle {
    padding-left: 0px;
  }

  ::v-deep .b-form-tag-remove {
    outline: none;
    align-self: center;
  }

  ::v-deep .select-tag-dropdown {
    width: 371px;
  }
</style>
