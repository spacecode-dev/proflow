<template>
  <i class="icon" v-html="svg()"/>
</template>

<script>
export default {
  name: 'Icon',
  props: {
    variant: {
      type: String,
      validator: variant =>
        variant === 'primary' ||
          variant === 'secondary' ||
          variant === 'light-secondary' ||
          variant === 'success' ||
          variant === 'danger' ||
          variant === 'warning' ||
          variant === 'info' ||
          variant === 'light' ||
          variant === 'dark' ||
          variant === 'black' ||
          variant === 'white' ||
          variant === 'red' ||
          variant === 'yellow' ||
          variant === 'green' ||
          variant === 'blue' ||
          variant === 'nav-links',
      default: 'dark'
    },
    name: {
      type: String,
      require: true
    },
    opacity: {
      type: String,
      default: '0.5'
    },
    width: {
      type: String,
      default: '11'
    }
  },
  methods: {
    svg () {
      let svg
      switch (this.name) {
        case 'flag-outline':
          svg = `<svg viewBox="0 0 11 13" fill="none" xmlns="http://www.w3.org/2000/svg" style="${this.getWidth()}">
  <path fill-rule="evenodd" clip-rule="evenodd" d="M10.353 1.29999H1.94116V0H0.647034V11.7H0V13H2.58826V11.7H1.94116V8.45001H10.353C10.5245 8.45001 10.6891 8.38153 10.8105 8.25961C10.9318 8.13773 11 7.97238 11 7.79999V1.95001C11 1.77762 10.9318 1.61227 10.8105 1.49039C10.6891 1.36847 10.5245 1.29999 10.353 1.29999ZM9.5 2.5H2V7.2H9.5V2.5Z" class="fill-${this.variant}"/>
</svg>`
          break
      }
      return svg
    },
    getWidth () {
      return 'width: ' + this.width + 'px'
    },
    getOpacity () {
      return 'fill-opacity: ' + this.opacity
    }
  }
}
</script>

<style lang="scss" scoped>
  ::v-deep {
    &.icon {
      display: inline-block;
    }

    .fill-black {
      fill: black;
    }

    .fill-white {
      fill: white;
    }

    .fill-red {
      fill: #DC1B50;
    }

    .fill-yellow {
      fill: #FF881B;
    }

    .fill-green {
      fill: #00A811;
    }

    .fill-blue {
      fill: #3860FF;
    }

    .fill-primary {
      fill: var(--primary);
    }

    .fill-secondary {
      fill: var(--secondary);
    }

    .fill-light-secondary {
      fill: #8D99A8;;
    }

    .fill-success {
      fill: var(--success);
    }

    .fill-danger {
      fill: var(--danger);
    }

    .fill-warning {
      fill: var(--warning);
    }

    .fill-info {
      fill: var(--info);
    }

    .fill-light {
      fill: var(--light);
    }

    .fill-dark {
      fill: var(--dark);
    }

    .fill-nav-links {
      fill: #606974;;
    }

    .stroke-black {
      stroke: black;
    }

    .stroke-white {
      stroke: white;
    }

    .stroke-red {
      stroke: #DC1B50;
    }

    .stroke-yellow {
      stroke: #FF881B;
    }

    .stroke-green {
      stroke: #00A811;
    }

    .stroke-blue {
      stroke: #3860FF;
    }

    .stroke-primary {
      stroke: var(--primary);
    }

    .stroke-secondary {
      stroke: var(--secondary);
    }

    .stroke-light-secondary {
      stroke: #8D99A8;
    }

    .stroke-success {
      stroke: var(--success);
    }

    .stroke-danger {
      stroke: var(--danger);
    }

    .stroke-warning {
      stroke: var(--warning);
    }

    .stroke-info {
      stroke: var(--info);
    }

    .stroke-light {
      stroke: var(--light);
    }

    .stroke-dark {
      stroke: var(--dark);
    }

    .stroke-nav-links {
      stroke: #606974;
    }
  }
</style>
