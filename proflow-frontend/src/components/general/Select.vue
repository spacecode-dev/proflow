<template>
  <div>
    <multiselect
      :class="{'__empty': !selected || selected.length === 0, 'with-search': search}"
      v-model="selected"
      @input="$emit('change', selected)"
      @remove="$emit('remove', $event)"
      :options="options"
      :searchable="search"
      :show-labels="false"
      :close-on-select="!multi"
      :clear-on-select="!multi"
      :multiple="multi"
      :placeholder="placeholder"
      :track-by="type === 'simple' ? '' : 'title'"
      :label="type === 'simple' ? '' : 'title'"
    >
      <template v-if="type === 'icons'" slot="option" slot-scope="props">
        <AppIcon :icon="'icon-pf-' + props.option.icon" :variant="props.option.type" iconClass="option__image"/>
        <div class="option__desc">
          <span class="option__title">{{ props.option.title }}</span>
        </div>
      </template>
      <template v-else-if="type === 'tags'" slot="option" slot-scope="props">
        <span>{{ props.option.title }}</span>
      </template>
    </multiselect>
  </div>
</template>

<script>
import Multiselect from 'vue-multiselect'
import '@/../node_modules/vue-multiselect/dist/vue-multiselect.min.css'

export default {
  name: 'Select',
  props: {
    title: {
      type: String,
      require: true,
      default: ''
    },
    options: {
      type: Array,
      require: true,
      default: () => []
    },
    multi: {
      type: Boolean,
      default: false
    },
    search: {
      type: Boolean,
      default: false
    },
    type: {
      type: String,
      validator: type => type === 'simple' || type === 'icons' || type === 'tags',
      default: 'simple'
    },
    selectedString: {
      type: String,
      default: ''
    },
    placeholder: {
      type: String,
      require: true,
      default: ''
    },
    selectedArray: {
      type: Array,
      default: () => []
    }
  },
  data () {
    return {
      selected: this.multi ? this.selectedArray : this.selectedString
    }
  },
  mounted () {
    this.$bus.$on('clearSelected', (value) => {
      if (value.includes(this.title)) {
        this.selected = this.multi ? [] : ''
      }
    })
  },
  components: { Multiselect }
}
</script>

<style lang="scss" scoped>
  ::v-deep {
    .multiselect {
      .multiselect__content-wrapper {
        right: 0;
      }

      &.__empty {
        &.multiselect--active {
          .multiselect__placeholder {
            display: inline-block;
          }
        }
      }

      &.with-search {
        .multiselect__content-wrapper {
          top: 40px;
          padding-top: 52px;
        }

        .multiselect__tags {
          .multiselect__input {
            display: none;
            position: absolute;
            right: 0;
            top: 40px;
            background: white;
            margin: 12px 10px;
            z-index: 99;
            font-size: 12px;
            padding: 7px 10px !important;
            border-radius: 6px;
            border: 1px solid #D6D6D6;
            line-height: 1;
            height: 30px;
          }
        }

        &.multiselect--active {
          .multiselect__tags .multiselect__input {
            display: inline-block;
          }
        }
      }

      &.multiselect--active {
        .multiselect__tags .multiselect__placeholder {
          color: #3C3D3E;
        }
      }

      .multiselect__select {
        display: none;
      }

      .multiselect__tags {
        border: 0;
        padding: 9px 0 0 0;
        background: transparent;
        font-weight: 500;
        font-size: 12px;

        .multiselect__tags-wrap {
          & > span {
            background: transparent;
            padding: 0;
            color: #35495e;
            border-radius: 0;
            line-height: inherit;
            white-space: inherit;
            text-overflow: unset;
            top: 2px;

            &:not(:last-of-type) {
              margin: 0 5px 0 0;

              &:after {
                content: ',';
                position: relative;
                left: -2px;
              }
            }

            &:last-of-type {
              margin: 0;
            }

            .multiselect__tag-icon {
              display: none;
            }
          }
        }

        .multiselect__placeholder {
          color: #606974;
          margin: 0;
        }

        .multiselect__single {
          background: transparent;
          margin: 0;
          font-weight: 500;
          font-size: 12px;
          padding: 0;
          vertical-align: unset;
        }
      }

      .multiselect__content-wrapper {
        min-width: 200px;
        background: #FFFFFF;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        border-radius: 6px;

        .multiselect__content {
          li {
            .multiselect__option {
              font-size: 12px;
              padding: 5px 12px;
              min-height: auto;
              color: rgba(0, 0, 0, 0.9);
              white-space: unset;
            }

            .multiselect__option--selected {
              background: rgba(240, 243, 246, 1) !important;
              font-weight: 500;
            }

            .multiselect__option--highlight {
              background: rgba(240, 243, 246, .5);
              color: rgba(0, 0, 0, 0.9);
            }
          }
        }
      }
    }
  }
</style>
