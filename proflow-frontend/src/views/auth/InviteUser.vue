<template>
  <div class="h-100">
    <BtnArrow class="position-absolute mt-md-4" position="left" @click="goToPersonalDetail" />
    <AppAvatar :text="user.company_detail.name" :show-text="true" class="pt-0 pt-md-4 pb-md-5" />

    <h5 class="text-center font-weight-bold mb-3">Who do you work most closely with?</h5>

    <div class="d-flex flex-column justify-content-center text-center mx-auto form-wrapper">
      <p class="mb-2">ProFlow is built for collaborate on!</p>
      <p>However, if you're just trying it out, you can always skip this step and invite people later 🙂</p>

      <validation-observer ref="observer" v-slot="{ handleSubmit }">
        <b-form class="form-no-valid-icons mt-3" @submit.stop.prevent="handleSubmit(onSubmit)">
          <validation-provider
            v-for="(input, key) in invites"
            :key="key"
            name="email"
            :vid="`email-${key}`"
            :rules="{ required: true, email: true }"
            v-slot="validationContext"
          >
            <b-form-group class="text-left">
              <div class="d-flex flex-column flex-md-row align-items-md-center position-relative">
                <b-form-input
                  :id="`invite-input-${key}`"
                  :name="`invite-input-${key}`"
                  :state="getValidationState(validationContext)"
                  :aria-describedby="`invite-input-${key}-feedback`"
                  v-model="input.email"
                />
                <AppIcon
                  v-if="key > 0"
                  class="cross-icon cursor-pointer px-2 py-2 position-absolute text-black-50"
                  icon="icon-pf-delete-circle"
                  @click.native="remove(key)"
                />
              </div>

              <b-form-invalid-feedback
                :id="`invite-input-${key}-feedback`"
              >{{ validationContext.errors[0] }}</b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <p class="cursor-pointer pb-5 pf-subheading" @click="add">+ Add another</p>

          <div class="d-flex flex-column flex-md-row align-items-center">
            <b-button
              type="submit"
              class="font-weight-bold btn-block mt-md-5"
              variant="primary"
            >Invite</b-button>
            <BtnArrow
              class="mt-5 skip-btn"
              @click="onSkip"
              text="Skip"
              variant="light"
              position="right"
            />
          </div>
        </b-form>
      </validation-observer>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

import BtnArrow from '@/components/buttons/BtnArrow'

export default {
  name: 'InviteUser',
  components: { BtnArrow },

  data () {
    return {
      invites: [
        {
          email: ''
        }
      ]
    }
  },
  computed: {
    ...mapGetters(['user'])
  },

  methods: {
    ...mapActions(['inviteEmail']),

    add () {
      this.invites.push({ email: '' })
    },
    remove (index) {
      this.invites.splice(index, 1)
    },
    async onSubmit () {
      const inviteMap = this.invites
        .filter((data) => data.email !== '')
        .map((data) => data.email)

      if (inviteMap.length > 0) {
        await this.inviteEmail({ invites: inviteMap })
      }

      this.$router.push({ name: 'home' })
    },
    async onSkip () {
      await this.inviteEmail({})
      this.$router.push({ name: 'home' })
    },
    goToPersonalDetail () {
      this.$router.push({
        name: 'personal-detail',
        params: { forceRedirect: true }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.form-wrapper {
  max-width: 320px;
}

.cross-icon {
  right: 0;
}

@media only screen and (min-width: 992px) {
  .skip-btn {
    position: absolute;
    border-color: transparent;
  }
}
</style>
