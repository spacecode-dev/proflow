<template>
  <b-card class="card-notification mx-auto" :style="style" border-variant="info">
    <b-card-text>
      <div class="d-inline-flex align-items-start pf-heading font-weight-semibold mb-2">
        <div v-if="icon">
          <AppIcon :icon="icon"/>
        </div>
        <div>{{ heading }}</div>
      </div>
      <div class="font-weight-normal pf-subheading" v-html="text"></div>
    </b-card-text>
  </b-card>
</template>

<script>
export default {
  name: 'AppCardNotification',
  props: {
    bgColor: {
      type: String,
      default: 'info'
    },
    heading: {
      type: String,
      default: ''
    },
    text: {
      type: String,
      default: ''
    },
    icon: {
      type: String,
      default: ''
    }
  },
  computed: {
    style () {
      return {
        'background-color': `var(--${this.bgColor})`
      }
    }
  }
}
</script>

<style lang="scss" scoped>
  .card-notification {
    color: var(--black-75);
    width: 100%;
    max-width: 1080px;

    ::v-deep .card-body {
      padding: 15px;
    }
  }
</style>
