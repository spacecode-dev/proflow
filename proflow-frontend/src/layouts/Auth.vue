<template>
  <b-container fluid class="h-100 auth-layout">
    <b-row class="h-100">
      <b-col cols="12" md="4"
             class="bg-primary text-white d-none d-md-flex flex-column justify-content-center align-items-center position-relative overflow-hidden p-3">
        <AppLogo :with-title="false"/>
        <div class="welcome-text text-center mt-2 mb-0">Welcome to ProFlow</div>

        <img
          src="@/assets/images/illustrations/dot-square.svg"
          class="img-fluid position-absolute dot-square"
          alt="dot-square"
        >
        <img
          src="@/assets/images/illustrations/dot-rect.svg"
          class="img-fluid position-absolute dot-rect"
          alt="dot-rect"
        >
      </b-col>
      <b-col cols="12" md="8"
             class="d-flex flex-column justify-content-start justify-content-md-center align-items-center py-md-3">
        <AppLogo class="d-block d-md-none logo mx-auto my-5 py-2" with-title height="30" width="30" clickable/>
        <router-view/>
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
import AppLogo from '@/components/general/AppLogo'

export default {
  name: 'Auth',
  components: { AppLogo }
}
</script>

<style lang="scss">
  /* auth specific styles */
  $shadow-sm: 0px 2px 2px var(--black-05);
  $shadow: 0px 2px 6px var(--black-05);

  .auth-layout {
    h2 {
      padding-bottom: 30px;
    }

    a {
      text-decoration: underline;
    }

    .btn-light {
      box-shadow: $shadow-sm !important;
    }

    .btn-primary {
      box-shadow: $shadow !important;
    }

    label {
      color: var(--secondary);
      text-transform: uppercase;
      font-weight: 800;
      padding: 0 12px;
    }

    .form-group {
      min-width: 320px;
      max-width: 320px;

      .form-control, .custom-select {
        box-sizing: border-box;
        box-shadow: $shadow;
        color: var(--black);

        &:not(:focus):not(.is-invalid) {
          border-color: transparent;
        }
      }
    }
  }
</style>

<style lang="scss" scoped>
  .welcome-text {
    font-weight: 800;
    font-size: 26px;
    line-height: 36px;
  }

  .dot-square {
    top: 22px;
    left: -10px;
  }

  .dot-rect {
    bottom: -22px;
    right: -24px;
  }
</style>
