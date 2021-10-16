<template>
  <div class="text-center" :class="{ 'grayscale': grayscale }">
    <b-avatar :src="src" :text="initial" :size="size"  :style="style" variant="light"/>
    <p v-if="text && showText" class="pt-3 font-weight-bold avatar-name">{{ text }}</p>
  </div>
</template>

<script>
export default {
  name: 'AppAvatar',
  props: {
    size: {
      type: String,
      default: '3.5rem'
    },
    bgColor: {
      type: String,
      default: 'avatar-gray'
    },
    src: {
      type: String,
      default: ''
    },
    text: {
      type: String,
      default: ''
    },
    grayscale: {
      type: Boolean,
      default: false
    },
    showText: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    initial () {
      return this.text.length > 0 ? this.text.charAt(0).toUpperCase() : ''
    },
    style () {
      return {
        'background-color': `var(--${this.bgColor})`
      }
    }
  }
}
</script>

<style lang="scss" scoped>
  ::v-deep {
    &.grayscale {
      img {
        -webkit-filter: grayscale(100%);
        filter: grayscale(100%);
      }
    }
    .b-avatar .b-avatar-img {
      img {
        border-radius: 50rem;
      }
    }
    .avatar-name {
      font-size: 16px;
      line-height: 19px;
      color: var(--black);
    }
  }
</style>
