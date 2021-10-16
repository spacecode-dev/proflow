<template>
  <b-dropdown
    ref="dropdown"
    variant="transparent"
    no-caret
    right
    menu-class="members-dropdown-menu"
    class="members-list-dropdown"
  >
    <template v-slot:button-content>
      <slot v-if="$slots.default">
      </slot>
      <AppIcon v-else icon="plus" class="icon-plus rounded-circle" icon-class="ml-2 mr-1" />
    </template>
    <div class="w-100">
      <div class="d-flex align-items-center justify-content-between m-3">
        <h5>{{ title }}</h5>
        <AppIcon icon="x" class="h4 cursor-pointer" icon-class="mr-0" @click.native="$refs.dropdown.hide()" />
      </div>

      <b-form-input
        v-model="search"
        class="w-100 mx-3 mb-3"
        type="search"
        size="md"
        autocomplete="on"
        :placeholder="searchPlaceholder"
      />

      <slot name="after_search"></slot>

      <b-dropdown-divider />

      <div v-if="availableUsers.length > 0" class="thin-scroll option-list pb-2">
        <b-dropdown-item
          v-for="user in availableUsers"
          :key="user.id"
        >
          <div class="d-flex align-items-center border-0 py-1" @click.stop="toggleUser(user.userId)">
          <AppAvatar :text="(user.name !== '')?user.name:user.email" bg-color="info" :src="user.profile_picture" size="30px" class="mr-3"  />
            <p class="font-weight-semibold mb-0 mr-auto">{{ user.name }}</p>
            <AppIcon
              v-if="selected.some(item => item.id === user.userId) || selected.includes(user.userId)"
              icon="icon-pf-check text-muted"
              icon-class="mr-0"
            />
          </div>
        </b-dropdown-item>
      </div>
      <span v-else class="px-3">{{ searchDesc }}</span>
    </div>
  </b-dropdown>
</template>

<script>
export default {
  name: 'MembersMultiSelectDropdown',
  props: {
    title: {
      type: String,
      default: 'Members'
    },
    searchPlaceholder: {
      type: String,
      default: 'Search Members'
    },
    noResultText: {
      type: String,
      default: 'No member found'
    },
    users: {
      type: Array,
      default: () => []
    },
    selectedUsers: {
      type: Array,
      default: () => []
    },
    stepId: {
      type: [Number, String],
      default: ''
    }
  },
  data () {
    return {
      search: '',
      selected: this.selectedUsers
    }
  },

  computed: {
    criteria () {
      return this.search.trim().toLowerCase()
    },
    availableUsers () {
      if (this.criteria) {
        // Show only options that match criteria
        return this.users.filter(
          user => user.name.toLowerCase().indexOf(this.criteria) > -1
        )
      }
      // Show all options available
      return this.users
    },
    searchDesc () {
      let msg = this.noResultText
      if (this.criteria && this.availableUsers.length === 0) {
        msg += ` with name "${this.search}"`
      }
      return msg
    }
  },
  watch: {
    selectedUsers: function (data) {
      this.selected = data
    }
  },
  methods: {
    toggleUser (id) {
      if (!this.stepId) {
        const index = this.selectedUsers.findIndex(data => data.id === id)
        if (index > -1) {
          this.selected.splice(index, 1)
          this.$emit('remove', id)
        } else {
          this.selected.push(id)
          this.$emit('add', id)
        }
      } else {
        const index = this.selectedUsers.findIndex(data => data.id === id)
        if (index > -1) {
          this.selected.splice(index, 1)
          this.$emit('remove', id, this.stepId)
        } else {
          this.selected.push(id)
          this.$emit('add', id, this.stepId)
        }
      }
    },
    cancleMembers () {

    }
  }
}
</script>

<style lang="scss" scoped>
.icon-plus {
  border: 1px dashed var(--black-90);
}

.option-list {
  max-height: 150px;
  overflow-x: hidden;
  overflow-y: scroll;
}

::v-deep ul.members-dropdown-menu {
  top: 5px !important;
  width: 370px;
  border-radius: 6px;
  cursor: default;
}

::v-deep .dropdown-item {
  padding-left: 16px;
  padding-right: 16px;
}
</style>
