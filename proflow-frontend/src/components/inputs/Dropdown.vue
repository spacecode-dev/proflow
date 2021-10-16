<template>
  <div class="d-inline-flex align-items-center">

    <label v-if="label" class="font-weight-semibold text-capitalize mb-0 typo__label">{{ label }}:</label>

    <b-dropdown
      variant="transparent"
      menu-class="dropdown-menu"
      no-caret
      right
    >
      <template v-slot:button-content>
        <div v-if="!selectedOption" class="pf-subheading d-flex align-items-center" :class="[btnClasses, { 'border px-3 py-1': btnBorder }]">
          <AppIcon  :icon="options[0].icon" :variant="options[0].variant"/>
          <span>{{ options[0].title }}</span>
        </div>
        <div v-else class="pf-subheading d-flex align-items-center" :class="[btnClasses, { 'border px-3 py-1': btnBorder }]">
          <AppIcon v-if="selectedOption.icon" :icon="selectedOption.icon" :variant="selectedOption.variant"/>
          <span>{{ selectedOption.title }}</span>
        </div>
      </template>

      <b-dropdown-item-button
        v-for="option in options"
        :key="option.value"
        @click="onClickButton(option)"
      >
        <b-row class="p-2">
          <b-col v-if="option.icon" cols="auto" class="px-0">
            <AppIcon :icon="option.icon" :variant="option.variant"/>
          </b-col>
          <b-col class="pl-2">
            <p class="mb-0 pf-heading">{{ option.title }}</p>
            <span class="text-secondary pf-subheading">{{ option.subtitle }}</span>
          </b-col>
        </b-row>
      </b-dropdown-item-button>
    </b-dropdown>
  </div>
</template>

<script>
export default {
  name: 'Dropdown',
  props: {
    value: {
      type: [String, Number],
      default: ''
    },
    options: {
      type: Array,
      default: () => []
    },
    label: {
      type: String,
      default: ''
    },
    btnBorder: {
      type: Boolean,
      default: false
    },
    btnClasses: {
      type: [String, Array],
      default: ''
    }
  },
  computed: {
    btnStyle () {
      return this.buttonBorderColor ? `border: 1px solid var(--${this.btnBorderColor})` : ''
    },

    selectedOption () {
      return this.options.find(data => data.value === this.value)
    }

  },
  methods: {
    onClickButton (option) {
      this.$emit('click', option.value)
    }
  }

}
</script>

<style lang="scss" scoped>
  .border {
    border-radius: 4px;
  }

  ::v-deep .btn {
    padding: 4px;
  }
</style>
