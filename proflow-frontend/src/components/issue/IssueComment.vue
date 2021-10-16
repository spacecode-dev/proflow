<template>
  <div>
    <div class="thin-scroll comment-list-wrapper pr-4">
      <template v-if="commentLists.length > 0">
        <b-row
          v-for="commentList in commentLists"
          :key="commentList.id"
          class="d-flex
           flex-nowrap pl-3 pr-3 pr-md-0 pt-3"
        >

        <AppAvatar :text="(commentList.name)?commentList.name:commentList.email" :src="commentList.profile_picture" size="24px" class="mt-1" />
          <div class="ml-2 bg-info rounded p-2">
            <p class="mb-2">
              <b>{{ commentList.name }}</b>
            </p>
            <span class="pf-heading" v-html="commentList.body"></span>
          </div>
        </b-row>
      </template>

      <div v-else class="d-flex align-items-center justify-content-center no-comment">
        <img src="@/assets/images/icons/comment.svg" alt="comment" />
        <span class="ml-3 text-secondary">No Comments Yet</span>
      </div>
    </div>

    <b-row class="d-flex flex-nowrap mt-3 pl-3 pr-3 pr-md-0">
      <AppAvatar  :text="(user.name)?user.name:user.email" :src="user.user_detail.profile_picture" size="24px" class="mt-2" />
      <div class="d-flex issue-comment ml-3 mr-2 w-100">
      <AppCKEditor
        class="ml-2 px-3"
        placeholder="Add comments..."
        formName="body"
        :people="people"
         mention
         @save-data="createFormBody"
         @mention-data="getMentionId"
        v-model="form.body"
      />

        <b-button type="submit" class="d-flex align-items-center modal-action btn-add-comment mr-2" variant="primary" @click="createIssueComment()">
          <span class="btn-add-comment-text">Send</span>
        </b-button>
      </div>
    </b-row>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import AppCKEditor from '@/components/ckeditor/AppCKEditor'

export default {
  name: 'IssueComment',
  components: {
    AppCKEditor
  },
  props: {
    issueId: {
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
      commentLists: [],
      form: {
        issue_id: this.issueId,
        body: ''
      }
    }
  },
  computed: {
    ...mapGetters(['user'])
  },
  watch: {
    issueId: function (id) {
      this.form.issue_id = id
      this.getIssueComment()
    }
  },
  mounted () {
    this.getIssueComment()
  },
  updated () {
    this.scroll()
  },
  methods: {
    ...mapActions(['saveIssueComment']),
    scroll () {
      const container = this.$el.querySelector('.comment-list-wrapper')
      container.scrollTop = container.scrollHeight
    },
    async createIssueComment () {
      const stringData = document.querySelector('.mention')
      if (stringData) {
        const mentionId = stringData.dataset.userId
        if (mentionId) {
          this.form.mention_id = mentionId
        }
      }
      this.commentLists = await this.saveIssueComment(this.form)
      this.form.body = ''
    },

    async getIssueComment () {
      if (this.issueId) {
        this.commentLists = await this.saveIssueComment({
          issue_id: this.issueId
        })
      }
    },
    createFormBody (value, data) {
      this.form[value] = data
    },

    getMentionId (id) {
      this.form.mention_id = id
    }
  }
}
</script>

<style lang="scss" scoped>
.comment-list-wrapper {
  max-height: 300px;
  overflow-y: scroll;
}

.issue-comment {
  min-height: 36px;
  border: 1px solid var(--gray-outline);
  box-sizing: border-box;
  border-radius: 6px;

  &.is-completed {
    border: 2px solid var(--blue-light);
  }

  .btn-add-comment {
    min-width: 80px;
    height: 30px;
    margin: auto;

    .btn-add-comment-text {
      margin: 0 auto;
    }
  }
}

.no-comment {
  height: 200px;
}
</style>
