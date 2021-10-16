<template>
  <b-container fluid class="d-flex flex-column flex-grow-1 px-0">
    <b-card no-body class="h-100 card-full-height px-4 py-3 mb-2">
      <slot name="header"></slot>
      <b-card-body class="mx-auto">
        <slot></slot>
      </b-card-body>
    </b-card>
  </b-container>
</template>

<script>
export default {
  name: 'AppCardFullHeight'
}
</script>

<style lang="scss" scoped>
  .card-body {
    padding: 20px 0px;
    width:100%;
    max-width: 1000px;

    @media only screen and (min-width: 992px) {
      padding: 20px 0px 20px 40px;
    }
  }
</style>
