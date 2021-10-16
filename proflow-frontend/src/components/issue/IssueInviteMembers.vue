<template>
  <div class="d-flex flex-column">
    <validation-observer ref="groupForm">
      <b-modal
        v-model="showModal"
        id="group-modal"
        content-class="group-modal-content"
        body-class="d-flex flex-column mx-2 my-1 px-4 py-3"
        hide-header
        hide-footer
        modal-cancel
        centered
        no-close-on-backdrop
      >
        <b-row>
          <b-col>
            <p class="modal-title"> <AppIcon icon="icon-pf-lock" /> Invite people to Issue</p>
          </b-col>
          <b-col cols="auto" @click="onCancel">
            <AppIcon icon="x" class="h4 cursor-pointer" icon-class="m-0" />
          </b-col>
        </b-row>

        <p class="pf-subheading mb-5">Private issues can only been seen by the people that are invited to the issue</p>

        <b-form class="form-no-valid-icons d-inline-flex align-items-center">
          <b-form-group label="Add People" label-for="add_people" class="mr-2 pr-1 invite-input">
            <vue-multiselect
              v-model="form.member_list"
              placeholder="Start typing.."
              label="name"
              track-by="name"
              select-label
              class="vue-multiselect"
              :options="people"
              :multiple="true"
              :hide-selected="true"
              :close-on-select="false"
              :showNoResults = "true"
              @search-change="SearchChange"
              :preserveSearch="true"
              @select="addMember"
              :searchable="true"
            >
            <template slot="option" slot-scope="props">
                <div class="d-flex align-content-center">
                  <b-avatar class="user-avatar" size="2rem" :src="props.option.profile_picture"/>
                  <div class="pl-2 text-truncate">
                    <p class="mb-0 user-name">{{props.option.name}}</p>
                    <small class="text-muted">{{props.option.email}}</small>
                  </div>
                </div>
              </template>
              <template slot="selection" slot-scope="{ values, search, isOpen }">
                <span class="multiselect__single"
                      v-if="values.length &amp;&amp; !isOpen">{{ values.length }} members</span>
              </template>
              <template slot="noResult">
                 No members found
              </template>
            </vue-multiselect>
          </b-form-group>

          <b-button type="button" variant="primary" class="mt-3 btn-invite" @click="onSubmit">Invite</b-button>
        </b-form>

        <div
          class="d-flex flex-column flex-grow-1 members-list thin-scroll"
          :class="{ 'justify-content-center align-items-center': isListEmpty }"
        >
          <div v-if="isListEmpty">Pretty Empty here 🙄</div>
          <div
            v-else
            class="d-flex align-items-center py-2"
            v-for="(item, index) in form.member_list"
            :key="item.id"

          >
            <AppAvatar class="user-avatar" :text="item.email" size="2rem" :src="item.profile_picture" />
            <div class="pl-2 text-truncate">
              <p class="mb-0 user-name">{{item.name}}</p>
              <small class="text-muted">{{item.email}}</small>
            </div>
            <b-icon
              class="icon-remove-member cursor-pointer ml-auto"
              icon="dash-circle-fill"
              aria-hidden="true"
              @click="deleteMember(index)"
            />
          </div>
        </div>
      </b-modal>
    </validation-observer>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import VueMultiselect from 'vue-multiselect'
export default {
  name: 'IssueInviteMembers',
  components: {
    VueMultiselect
  },
  props: {
    value: {
      type: [Array],
      default: () => []
    },
    issueId: {
      type: String,
      default: ''
    },
    people: {
      type: [Array],
      default: () => []
    }
  },
  computed: {
    ...mapGetters(['user']),

    isListEmpty () {
      return this.form.member_list.length === 0
    },

    inviteList () {
      return this.value
    }

  },
  watch: {
    issueId: function (id) {
      this.form.issue_id = id
    }
  },
  data () {
    return {
      allMembers: this.people,
      invitedMembers: this.value,
      showModal: true,
      form: {
        issue_id: this.issueId,
        member_list: this.value
      }
    }
  },
  mounted () {
    this.getMembersListing()
  },
  methods: {
    ...mapActions(['addMembersToIssue', 'getMembersList']),
    // get all members of a company
    async getMembersListing () {
      this.form.member_list = this.value
      const getMembers = this.value
      this.allMembers = getMembers.filter(
        data => data.id !== this.user.user_detail.user_id
      )
    },
    // delete member
    async deleteMember (index) {
      console.log('form', this.form.member_list)
      const member = this.form.member_list[index]
      const isSaved = this.form.member_list.findIndex(m => m === member) > -1
      // delete locally
      this.$delete(this.form.member_list, index)
      // delete from database
      if (!isSaved) return
      this.form.is_invited = 0
      this.form.type = 'invited'
      this.form.member_id = member.id
      await this.addMembersToIssue(this.form)
    },

    async addMember (option) {
      this.form.type = 'invited'
      this.form.is_invited = 1
      this.form.member_id = option.userId
      this.form.member_list = await this.addMembersToIssue(this.form)
    },

    onCancel () {
      this.showModal = false
    },
    async onSubmit () {
      this.form.is_invited = 1
      this.form.type = 'add-invite-email'
      this.form.member_list = await this.addMembersToIssue(this.form)
    },
    SearchChange (option) {
      this.form.invite_email = option
    }
  }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="scss" scopped>
.group-modal-content {
  height: 600px;
  width: 422px;
  max-width: 100%;
}

.invite-input {
  width: 262px;
}

.btn-invite {
  width: 84px;
  height: 36px;
}

.vue-multiselect {
  .multiselect__tags-wrap {
    display: none;
  }
}

.icon-remove-member {
  color: #e0e0e0;

  &:hover {
    color: #e03766;
  }
}

.members-list {
  max-height: 270px;
  overflow: auto;
  width: 100%;
  padding: 0px 8px;
}
</style>
