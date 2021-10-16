<template>
  <b-dropdown
    ref="dropdown"
    id="dropdown-form"
    class="sticky-comment ml-1"
    :class="{ 'no-comments': !hasComments }"
    variant="transparent"
    menu-class="comments-list"
    no-caret right
    @hide="onHide"
    @show="forceDropdownClose = false"
  >

    <template v-slot:button-content>
      <div class="d-inline-flex align-items-center comment-icon" :class="{ 'has-comments': hasComments, 'pr-2 mr-1': !hasComments }" >
        <AppIcon icon="icon-pf-comment" icon-class="mr-0" />
        <span v-if="hasComments" class="ml-1">{{ commentListing.length }}</span>
      </div>
    </template>

    <div class="w-100" >
      <div class="thin-scroll comments-list">
        <div
          v-for="commentList in commentListing"
          :key="commentList.id"
          class="d-flex px-3 pt-3 border-bottom comment"
        >
          <AppAvatar   :text="(commentList.name)?commentList.name:commentList.email" :src="commentList.profile_picture" size="20px" />
          <div class="ml-2 w-100">
            <div class="d-flex align-items-center mt-1 mb-2">
              <span class="pf-heading">
                <b>{{commentList.name}}</b>
              </span>
              <span
                class="ml-2 text-muted pf-subheading"
              >{{ getDateFromNow(commentList.created_at) }}</span>
            </div>
            <p class="pf-subheading" v-html="commentList.body"></p>
          </div>
        </div>
      </div>
      <div class="px-3 py-2 border-top">
        <div class="d-flex align-items-start">
          <AppAvatar  :text="(user.name)?user.name:user.email" :src="user.user_detail.profile_picture" size="20px" class="mt-2 mr-2" />
          <AppCKEditor
          v-model="form.body"
            class="w-100"
            placeholder="Add comment..."
            formName="body"
            :people="people"
            :clearData="true"
            mention
            @save-data="createFormBody"
            @mention-data="getMentionId"
          />

        </div>
      </div>
    </div>

    <div class="d-flex justify-content-end pr-2 pb-2">
      <b-button class="mr-3 modal-action" type="button" size="sm" variant="outline-secondary"  @click="cancleComment()">
        Cancel
      </b-button>

      <b-button type="submit" class="modal-action mr-2" variant="primary" @click.stop="saveComment()">
        Send
      </b-button>
    </div>
  </b-dropdown>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import AppCKEditor from '@/components/ckeditor/AppCKEditor'

const moment = require('moment-timezone')

export default {
  name: 'IssueStickyComments',
  components: {
    AppCKEditor
  },
  props: {
    issueId: {
      type: String,
      default: ''
    },
    id: {
      type: Number,
      default: 0
    },
    commentLists: {
      type: [Array],
      default: () => []
    },
    type: {
      type: String,
      default: ''
    },
    people: {
      type: [Array],
      default: () => []
    }
  },
  data () {
    return {
      commentListing: this.commentLists,
      forceDropdownClose: false,
      form: {
        issue_id: this.issueId,
        id: this.id,
        type: this.type,
        body: ''
      }
    }
  },
  computed: {
    ...mapGetters(['user']),
    hasComments () {
      return this.commentListing.length > 0
    }
  },
  watch: {
    issueId: function (id) {
      this.form.issue_id = id
    }
  },
  mounted () {
    this.scroll()
  },
  methods: {
    ...mapActions(['saveIssueComment']),
    scroll () {
      const container = this.$el.querySelector('.thin-scroll')
      container.scrollTop = container.scrollHeight
    },
    onHide (event) {
      console.log('forceDropdownClose', this.forceDropdownClose)
      if (this.forceDropdownClose) return
      event.preventDefault()
    },
    getDateFromNow (data) {
      return moment(data).fromNow()
    },
    createFormBody (value, data) {
      this.form[value] = data
    },

    async saveComment () {
      this.commentListing = await this.saveIssueComment(this.form)
      this.form.body = ''
    },
    cancleComment () {
      this.forceDropdownClose = true
      this.$refs.dropdown.hide(true)
    },
    getMentionId (id) {
      this.form.mention_id = id
    }
  }
}
</script>

<style lang="scss" scoped>
.comments-list {
  max-height: 322px;
  overflow-y: scroll;

  .comment:last-child {
    border: none !important;
  }
}

.comment-icon {
  &:not(.has-comments) {
    opacity: 0;
  }

  &:hover {
    opacity: 1;
  }
}

.modal-action {
  padding: 6px;
  width: 80px;
  border-radius: 6px;
  font-size: 12px;
}

::v-deep .btn:not(.modal-action) {
  height: 20px;
  padding: 0px;
  border-radius: 0px;
  color: var(--gray);
}

::v-deep ul.comments-list {
  width: 372px;
}
</style>
