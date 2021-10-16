<template>
  <div class="switch-input">
    <label class="switch-input-light mb-0">
      <input
        :value="value"
        @change="$emit('change', !value)"
        type="checkbox"
        :class="{ 'is-checked': value }"
      />
      <div class="options font-weight-semibold">
        <span :class="{ 'is-selected': !value }">No</span>
        <span :class="{ 'is-selected': value }">Yes</span>
      </div>
      <a class="btn btn-light"></a>
    </label>
  </div>
</template>

<script>
export default {
  name: 'SwitchInput',
  props: {
    value: {
      type: Boolean,
      default: false
    }
  }
}
</script>

<style lang="scss" scoped>
  .switch-input {
    border: 1px solid var(--gray-outline);
    border-radius: 29px;
    width: 109px;
    height: 35px;
    padding: 2px;

    .switch-input-light {
      position: relative;
      display: block;
      overflow: visible;
      vertical-align: middle;
      line-height: 31px;
      height: 29px;

      input {
        position: absolute;
        opacity: 0;
        z-index: 5;

        &.is-checked {
          ~ {
            a {
              right: 0%;
            }
          }
        }
      }

      .options {
        position: absolute;
        text-align: left;
        left: -100px;
        width: 100%;
        margin: 0px;
        padding-right: 100px;

        span {
          display: block;
          position: absolute;
          text-align: center;
          width: 50%;
          z-index: 5;
          font-size: 12px;
          top: 0px;
          left: -2px;
          margin-left: 100px;

          &:last-child {
            left: calc(50% - 2px);
          }

          &.is-selected {
            color: var(--black);
          }
        }
      }

      a {
        position: absolute;
        display: block;
        z-index: 4;
        transition: all 0.3s ease-out 0s;
        right: 50%;
        width: 50%;
        height: 100%;
        top: 0px;
        padding: 0px;
        border-radius: 30px;
      }
    }

    * {
      box-sizing: border-box;
    }
  }
</style>
