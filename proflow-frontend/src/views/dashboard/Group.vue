<template>
  <b-modal
    id="groupsModal"
    dialog-class="pf-modal-small"
    content-class="group-modal-content"
    body-class="d-flex flex-column mx-2 my-1 px-4 py-3"
    ok-title="Save"
    cancel-variant="outline-secondary"
    hide-header
    centered
    no-close-on-backdrop
    @show="init"
    @cancel="onCancel"
    @ok="onSubmit($event)"
  >
    <validation-observer ref="groupForm">
      <p class="modal-title">{{ title }}</p>
      <b-form class="form-no-valid-icons">
        <validation-provider name="group name" rules="required|max:50" v-slot="validationContext">
          <b-form-group label="Group Name" label-for="group_name">
            <b-form-input
              v-model="form.name"
              :state="getValidationState(validationContext)"
              aria-describedby="name-feedback"
            />
            <b-form-invalid-feedback id="name-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
          </b-form-group>
        </validation-provider>

        <b-form-group label="Add People" label-for="add_people">
          <vue-multiselect
            v-model="form.member_list"
            placeholder="Start typing.."
           :custom-label="nameWithEmail"
            track-by="name"
            select-label=""
            class="vue-multiselect"
            :options="allMembers"
            :multiple="true"
            :hide-selected="true"
            :close-on-select="false"
            :internal-search="true"
          >
            <template slot="option" slot-scope="props">
              <div class="d-flex align-content-center">
                <b-avatar class="user-avatar" size="2rem" :src="props.option.profile_picture"/>
                <div class="pl-2 text-truncate">
                  <p class="mb-0 user-name">{{ props.option.name }}</p>
                  <small class="text-muted">{{ props.option.email }}</small>
                </div>
              </div>
            </template>
            <template slot="selection" slot-scope="{ values, search, isOpen }">
                <span class="multiselect__single"
                      v-if="values.length &amp;&amp; !isOpen">{{ values.length }} members</span>
            </template>
            <template slot="noResult">
              No member found
            </template>
          </vue-multiselect>
        </b-form-group>
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
          <b-avatar class="user-avatar" size="2rem" :src="item.profile_picture"/>
          <div class="pl-2 text-truncate">
            <p class="mb-0 user-name">{{ item.name }}</p>
            <small class="text-muted">{{ item.email }}</small>
          </div>
          <b-icon
            class="icon-remove-member cursor-pointer ml-auto"
            icon="dash-circle-fill"
            aria-hidden="true"
            @click="deleteMember(index)"
          />
        </div>
      </div>
    </validation-observer>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import VueMultiselect from 'vue-multiselect'

export default {
  name: 'Group',
  components: {
    VueMultiselect
  },
  data () {
    return {
      allMembers: [],
      companyMembers: [],
      form: {
        name: '',
        member_id: [],
        member_list: []
      }
    }
  },
  computed: {
    ...mapGetters(['user', 'modalGroupAction', 'modalGroupId', 'issueTakeCount']),
    title () {
      return (this.modalGroupAction === 'edit' && this.modalGroupId ? 'Edit Group' : 'Add Group')
    },
    isListEmpty () {
      return this.form.member_list.length === 0
    }
  },
  methods: {
    ...mapActions(['editGroupDetail', 'getMembersList', 'getGroupDetail', 'toggleModalGroup', 'getIssues', 'getIssuesCount']),
    nameWithEmail ({ name, email }) {
      return `${name} — [${email}]`
    },
    
    // get group detail
    async getGroupData () {
      const data = await this.getGroupDetail(this.modalGroupId)
      this.companyMembers = data.member_list
      this.form = Object.assign({}, data)
    },
    // get all members of a company
    async getMembersListing () {
      const getMembers = await this.getMembersList()
      this.allMembers = getMembers.filter(data => data.id !== this.user.user_detail.user_id)
    },
    // delete member
    async deleteMember (index) {
      const member = this.form.member_list[index]
      const isSaved = this.companyMembers.findIndex(m => m === member) > -1
      // delete locally
      this.$delete(this.form.member_list, index)
      // delete from database
      if (!isSaved) return
      this.form.member_id = this.companyMembers.map((data) => data.id).toString()
      await this.editGroupDetail(this.form)
    },
    async onSubmit (bvModalEvt) {
      bvModalEvt.preventDefault()
      const valid = await this.$refs.groupForm.validate()
      if (valid) {
        this.form.member_id = this.form.member_list.map((data) => data.id).toString()
        await this.editGroupDetail(this.form)
        if(this.$route && this.$route.name === 'home-group') {
          await this.getIssues({
            type: 'group',
            companyId: this.user.company_detail.id,
            take: this.issueTakeCount,
            skip: 0,
            groupId: this.modalGroupId
          })
        }
        await this.getIssuesCount()
        await this.onCancel()
      }
    },
    onCancel () {
      this.allMembers = []
      this.companyMembers = []
      this.form.name = ''
      this.form.member_id = []
      this.form.member_list = []
      this.$root.$emit('bv::hide::modal', 'groupsModal')
      setTimeout(function () {
        this.toggleModalGroup({ status: false })
      }.bind(this), 500)
    },
    init () {
      if (this.modalGroupAction === 'edit' && this.modalGroupId) {
        this.getGroupData()
      }
      this.getMembersListing()
    }
  }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"/>

<style lang="scss" scoped>
  ::v-deep {
    .group-modal-content {
      height: 600px;
      .modal-body {
        & > span {
          display: flex;
          flex-direction: column;
          position: relative;
          flex: 1;
        }
      }
    }

    .vue-multiselect {
      .multiselect__tags-wrap {
        display: none;
      }
    }

    .icon-remove-member {
      color: #E0E0E0;

      &:hover {
        color: #E03766;
      }
    }

    .members-list {
      max-height: 270px;
      overflow: auto;
      width: 100%;
      padding: 0 8px;
    }
  }
</style>
