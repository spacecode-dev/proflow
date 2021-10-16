<template>
  <div class="d-flex align-items-start section-info mr-1">
    <label class="font-weight-semibold text-capitalize typo__label mb-0 mt-2">
      <AppIcon v-if="icon" :icon="icon" />
      <span v-if="label" class="text-nowrap">{{ label }}</span>
    </label>

    <slot></slot>
  </div>
</template>

<script>
export default {
  name: 'IssueInfoSection',
  props: {
    icon: {
      type: String,
      default: ''
    },
    label: {
      type: String,
      default: ''
    }
  },

  methods: {
    addData () {
      console.log('Dsfsdf')
    }
  }
}
</script>

<style lang="scss" scoped>
.section-info {
  min-width: 255px;
  max-width: 255px;
  margin-bottom: 26px;

  ::v-deep .members-list-dropdown {
    .dropdown-toggle {
      padding: 4px 0px;
      margin-left: -4px;
    }
  }

  ::v-deep .b-avatar {
    min-width: 20px;
  }
}
</style>
