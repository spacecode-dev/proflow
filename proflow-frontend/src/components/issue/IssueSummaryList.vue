<template>
  <b-row>
    <b-col>
      <div
        class="d-flex justify-content-between issue-summary-list"
        v-for="(summaryList) in summaryLists"
        :key="summaryList.id"
      >
        <AppCKEditor
          v-if="type === 'summary'"
          v-model="summaryList.text"
          :placeholder="placeholder"
          :formName="summaryList.id"
          :clearData="false"
          :people="people"
          mention
          title="Summary"
          @mention-data="getMentionId"
          @save-data="saveSummaryData"
          wordCount
          :useDefaultPlaceholder="true"
          class="issue-summary"
        />

        <AppCKEditor
          v-if="type === 'additional-info'"
          v-model="summaryList.text"
          :placeholder="placeholder"
          :formName="summaryList.id"
          :clearData="false"
          :people="people"
          mention
          @mention-data="getMentionId"
          @save-data="saveSummaryData"
          :useDefaultPlaceholder="true"
          class="issue-additional-info"
        />

        <AppCKEditor
          v-if="((type === 'summary-imp') || (type === 'summary-output'))"
          v-model="summaryList.text"
          :placeholder="placeholder"
          :formName="summaryList.id"
          :clearData="false"
          :people="people"
          :is-list="isList"
          mention
          @mention-data="getMentionId"
          @save-data="saveSummaryData"
          :useDefaultPlaceholder="true"
           class="issue-summary-imp-output"
        />

        <IssueStickyComments
          type="summary"
          :commentLists="summaryList.comments"
          :id="summaryList.id"
          :issueId="issueId"
          :people="people"
        />
      </div>
    </b-col>
  </b-row>
</template>

<script>
import { mapActions } from "vuex";
import AppCKEditor from "@/components/ckeditor/AppCKEditor";
import IssueStickyComments from "@/components/issue/IssueStickyComments";

export default {
  name: "IssueSummaryList",
  components: {
    AppCKEditor,
    IssueStickyComments
  },
  props: {
    value: {
      type: [Array],
      default: () => ""
    },
    placeholder: {
      type: [String],
      default: () => []
    },
    people: {
      type: [Array],
      default: () => []
    },
    issueId: {
      type: String,
      default: ""
    },
    type: {
      type: String,
      default: ""
    },
    isList: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      summaryLists: this.value,
      form: {
        issue_id: this.issueId,
        type: this.type
      }
    };
  },

  watch: {
    issueId: function(id) {
      this.form.issue_id = id;
    },
    value: function(data) {
      this.summaryLists = data;
    }
  },

  methods: {
    ...mapActions(["saveSummaryList"]),
    saveMentionTag(id) {
      this.saveMentionMember({ issue_id: this.issueId, mention_id: id });
    },
    saveSummaryData(value, data) {
      if (value) {
        this.form.summary_id = value;
      }
      this.form.new_data = false;
      this.form.text = data;
      // if (dataString) {
      //   const mentionId = dataString.dataset.userId
      //   if (mentionId) {
      //     this.form.mention_id = mentionId
      //   }
      // }
      this.saveSummaryList(this.form);
    },
    //  saveData () {
    //   console.log(this.form)
    //   //  await this.saveData(this.form)
    // },
    async refreshData(value, data) {
      this.form.new_data = true;
      if (value) {
        this.form.summary_id = value;
      }
      this.form.text = data;
      const dataList = await this.saveSummaryList(this.form);
      this.$set(this.$data, "summaryLists", dataList);
    },
    getMentionId(id) {
      this.form.mention_id = id;
    }
  }
};
</script>

<style lang="scss" scoped>
.issue-summary-list {
  min-height: 30px;
  border-radius: 6px;
}

::v-deep .issue-summary-list {
  &:hover {
    .comment-icon {
      opacity: 1 !important;
    }
  }
}

.issue-summary {
  ::v-deep .ck-content {
    min-height: 80px !important;
  }
 
  
}

.issue-summary-imp-output {
  ::v-deep .ck-content {
    min-height: 59px !important;
  }
   ::v-deep .app-ckeditor-width {
    min-width: 500px;
 }
}

.issue-additional-info {
  ::v-deep .ck-content {
    min-height: 59px !important;
  }
   ::v-deep .app-ckeditor-width {
    min-width: 500px;
 }
}
</style>
