<template>
  <b-button
    class="d-flex align-items-center shadow-none"
    :class="[`${position}`, { 'flex-row-reverse': position === 'right' }]"
    :variant="variant"
    @click="$emit('click')"
  >
    <b-icon icon="arrow-left" class="arrow" :class="{ 'ml-2': position === 'right', 'mr-2': position === 'left' }"
            aria-hidden="true"/>
    <span>{{ text }}</span>
  </b-button>
</template>

<script>
export default {
  name: 'BtnArrow',
  props: {
    variant: {
      type: String,
      default: 'outline-secondary'
    },
    position: {
      type: String,
      validator: type => type === '' || type === 'left' || type === 'right',
      default: ''
    },
    text: {
      type: String,
      default: 'Back'
    }
  }
}
</script>

<style lang="scss" scoped>
  .btn {
    width: 92px;
    height: 34px;
    border: 1px solid var(--gray-darker);
  }

  .left {
    left: 20px;
  }

  .right {
    right: 20px;

    span {
      color: var(--secondary);
    }

    .arrow {
      transform: rotate(180deg);
    }
  }
</style>
