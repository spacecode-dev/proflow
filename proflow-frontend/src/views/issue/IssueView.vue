<template>
  <div>
    <AppCardFullHeight>
      <AppCardNotification
        v-if="form.visibility === 'draft' && form.is_resolved === 0"
        slot="header"
        icon="icon-pf-file-edit"
        heading="Draft Issue"
        text="This is a draft issue, only you can see it. People that are tagged will be notified once the issue goes public. <br/>You can change the visibility of the issue on the right hand side."
      />

      <AppCardNotification
        v-if="form.is_resolved === 1"
        slot="header"
        icon="icon-pf-check-square"
        heading="Issue Resolved"
        :text="form.resolve_text"
        bg-color="card-green-success"
      />
      <b-row class="pr-40">
        <b-col class="d-flex flex-column flex-md-row flex-nowrap align-items-start">
          <AppCKEditor
            v-if="form.title.indexOf('Untitled') > -1"
            class="placeholder-draft-title"
            placeholder="Untitled"
            formName="title"
            :clearData="false"
            enableEnterMode
            @save-data="saveSummaryData"
          />
          <AppCKEditor
            v-else
            v-model="form.title"
            class="draft-title"
            placeholder="Untitled"
            formName="title"
            :clearData="false"
            enableEnterMode
            @save-data="saveSummaryData"
          />
          <TagInput
            v-model="form.tags"
            :tags="allTags"
            class="ml-md-3"
            @create-tag="setFormTag"
            @add-tag="setFormTag"
            @remove-tag="deleteFormTag"
          />
        </b-col>

        <b-col cols="12" md="auto" class="ml-md-auto px-0 d-flex align-items-start">
          <Dropdown
            v-if="form.visibility !== 'draft'"
            v-model="form.visibility"
            :options="visibilityOptions"
            class="visibility-options"
            label="Visibility"
            btn-border
            @click="saveFormVisibility"
          />
          <Dropdown
            v-else
            v-model="visibilitySelected"
            :options="visibilityOptions"
            class="visibility-options"
            label="Visibility"
            btn-border
            @click="setFormVisibility"
          />
        </b-col>
      </b-row>

      <div class="section-info-wrapper">
        <b-row>
          <b-col cols="12" class="d-flex flex-wrap">
            <IssueInfoSection label="Submitted By:" icon="icon-pf-user-single">
              <AppAvatar
                :text="(form.created_by_name)?form.created_by_name:form.created_by_email"
                :src="form.created_by_profile_pic"
                size="20px"
                :grayscale="form.visibility === 'draft'"
                class="mx-2"
              />
              <div>
                <small class="font-weight-medium break-word text-wrap">{{ form.created_by_name }}</small>
              </div>
            </IssueInfoSection>

            <IssueInfoSection label="Following:" icon="icon-pf-user-arrow">
              <b-avatar-group size="20px">
                <AppAvatar
                  v-for="item in form.people_follow_user_detail"
                  :key="item.id"
                  :text="(item.name)?item.name:item.email"
                  :src="item.profile_picture"
                  :grayscale="form.visibility === 'draft'"
                />
              </b-avatar-group>

              <MembersMultiSelectDropdown
                :users="people"
                :selected-users="form.people_follow_user_detail"
                @add="addMembers"
                @remove="removeMembers"
              >
                <b-dropdown-item slot="after_search">
                  <div class="d-flex py-1">
                    <!-- <AppIcon icon="icon-pf-check text-muted" icon-class="mr-0" /> -->
                  </div>
                </b-dropdown-item>
              </MembersMultiSelectDropdown>
            </IssueInfoSection>
          </b-col>
        </b-row>

        <b-row>
          <b-col cols="12" class="d-flex flex-wrap">
            <IssueInfoSection label="Due Date:" icon="icon-pf-calendar">
              <small
                class="ml-2 mt-1 font-weight-medium"
                @click="showDueDate"
              >{{ form.due_date | formatDate('D MMM YYYY') }}</small>
              <b-form-datepicker
                class="duedate-datepicker"
                ref="dueDate"
                button-only
                v-model="form.due_date"
                aria-controls="example-input"
                @input="saveIssueData"
              />
            </IssueInfoSection>

            <IssueInfoSection label="Priority:" icon="flag">
              <Dropdown
                v-model="form.priority"
                :options="priorityOptions"
                btn-classes="small mt-px"
                @click="setFormPriority"
              />
            </IssueInfoSection>
          </b-col>
        </b-row>
      </div>

      <b-row>
        <b-col cols="12">
          <IssueSummaryList
            v-model="summaryData"
            type="summary"
            :placeholder="placeholderData.summary"
            :people="people"
            @get-data="saveSummaryData"
            :issueId="id"
          />
        </b-col>
      </b-row>

      <b-row class="mt-5">
        <b-col>
          <span class="font-weight-semibold">Why resolving this issue is important</span>
        </b-col>
      </b-row>
      <b-row>
        <b-col>
          <IssueSummaryList
            v-model="summaryImpData"
            type="summary-imp"
            :placeholder="placeholderData.summaryImp"
            :people="people"
            :issueId="id"
            is-list
            @get-data="saveSummaryData"
          />
        </b-col>
      </b-row>

      <b-row class="mt-5">
        <b-col>
          <span class="font-weight-semibold">What is the ideal output</span>
        </b-col>
      </b-row>
      <b-row>
        <b-col>
          <IssueSummaryList
            v-model="summaryOutputData"
            type="summary-output"
            :placeholder="placeholderData.summaryOutput"
            :people="people"
            :issueId="id"
            is-list
            @get-data="saveSummaryData"
          />
        </b-col>
      </b-row>

      <b-row class="mt-5">
        <b-col>
          <IssueNextStepSection
            v-model="form.issue_step"
            :people="people"
            :issueId="id"
            @get-data="getIssueIdData"
            :visibiltiy="form.visibility"
          />
        </b-col>
      </b-row>

      <b-row class="mt-5">
        <b-col>
          <span class="font-weight-semibold">Additional Information</span>
        </b-col>
      </b-row>
      <b-row>
        <b-col>
          <IssueSummaryList
            v-model="summaryAdditionalInfoData"
            type="additional-info"
            :placeholder="placeholderData.additionalInfo"
            :people="people"
            :issueId="id"
            @get-data="saveSummaryData"
          />
        </b-col>
      </b-row>

      <b-row class="mt-5 pr-40">
        <b-col>
          <span class="font-weight-semibold">Files</span>
        </b-col>
      </b-row>

      <b-row class="mt-3 pr-40">
        <b-col cols="6" v-if="showFileInput">
          <!-- use @upload="" for file upload -->
          <AppFileInput @upload="saveFile" @remove="removeFile" />
        </b-col>

        <b-col v-for="file in form.issue_file" :key="file.id" cols="6" class="mb-3">
          <AppFileInput
            :file="file"
            @getData="getFileData"
            @upload="saveFile"
            @remove="removeFile"
          />
        </b-col>

        <b-col cols="12" v-if="!showFileInput">
          <div
            class="px-3 pt-1 mt-2 cursor-pointer font-weight-medium pf-subheading btn-add-another"
            @click="triggerFileEvent"
          >+ Add Another</div>
        </b-col>
      </b-row>

      <b-row class="mt-5 mb-2 pr-40">
        <b-col>
          <span class="font-weight-semibold">Comments</span>
        </b-col>
      </b-row>
      <b-row class="pr-40">
        <b-col>
          <IssueComment :people="people" :issueId="id" />
        </b-col>
      </b-row>
    </AppCardFullHeight>
    <IssueInviteMembers
      v-if="showPrivatePopup"
      v-model="form.people_invited_user_detail"
      :people="people"
      :issueId="id"
      @close="showPrivatePopup = false"
    />
    <b-row v-if="form.is_archived === 1" class="mt-2 mb-2">
      <b-col>
        <b-button
          class="mr-3 bg-white modal-action"
          type="submit"
          variant="outline-secondary"
          @click="updateArchiveStatus(0)"
        >UnArchive
        </b-button>
      </b-col>
    </b-row>
    <b-row v-else-if="form.visibility === 'draft'" class="mt-2 mb-2">
      <b-col cols="10">
        <b-button
          class="mr-3 bg-white modal-action"
          type="submit"
          variant="outline-secondary"
          @click="deleteDraft"
        >Delete Draft</b-button>

        <b-button
          class="bg-white modal-action"
          type="submit"
          variant="outline-secondary"
          @click="updateArchiveStatus(1)"
        >Archive</b-button>
      </b-col>
      <b-col class="d-flex justify-content-end mr-0">
        <b-button
          type="submit"
          class="modal-action"
          variant="primary"
          @click="saveFormVisibility(visibilitySelected)"
        >Share</b-button>
      </b-col>
    </b-row>
    <b-row v-else-if="form.visibility !== 'draft'" class="mt-2 mb-2">
      <b-col cols="10">
        <b-button
          class="mr-3 bg-white modal-draft"
          type="submit"
          variant="outline-secondary"
          @click="saveFormVisibility('draft', 'change')"
        >Move to Drafts</b-button>

        <b-button
          class="bg-white modal-action"
          type="submit"
          variant="outline-secondary"
          @click="updateArchiveStatus(1)"
        >Archive</b-button>
      </b-col>
      <b-col class="d-flex justify-content-end">
        <b-button
          type="submit"
          class="modal-action"
          variant="primary"
          @click="showResolveIssueModal = true"
        >Resolve</b-button>
      </b-col>
    </b-row>
    <!-- <div class="d-flex justify-content-end mb-1" v-if="form.is_resolved === 0">
            <b-button
              type="submit"
              class="modal-action mr-2"
              variant="primary"
              @click="showResolveIssueModal = true"
            >Resolve</b-button>
    </div>-->
    <!-- <div class="d-flex justify-content-end mb-1" v-if="form.is_resolved === 1">
      <b-button
        class="mr-3 bg-white modal-action"
        type="submit"
        variant="outline-secondary"
        @click="reopenResolveIssue"
      >Reopen Issue</b-button>
<<<<<<< HEAD
    </div>-->

=======
    </div> -->
>>>>>>> c00bbbef8efea3fef37c8a6f7ad6b5c4fc9f82a3
    <ResolveIssue
      v-if="showResolveIssueModal"
      :issueId="id"
      header-title="Resolve Issue"
      @clicked="updateResolveStatus"
      @cancel="showResolveIssueModal = false"
    />
  </div>
</template>

<script>
// ⚠️ NOTE: We don't use @ckeditor/ckeditor5-build-classic any more!
// Since we're building CKEditor from source, we use the source version of ClassicEditor.
import {mapActions, mapGetters} from 'vuex'

import AppCKEditor from "@/components/ckeditor/AppCKEditor";
import AppCardFullHeight from "@/components/cards/AppCardFullHeight";
import AppCardNotification from "@/components/cards/AppCardNotification";
import AppFileInput from "@/components/inputs/AppFileInput";
import Dropdown from "@/components/inputs/Dropdown";
import TagInput from "@/components/inputs/TagInput";

// issue components
import IssueComment from "@/components/issue/IssueComment";
import IssueInfoSection from "@/components/issue/IssueInfoSection";
import IssueNextStepSection from "@/components/issue/IssueNextStepSection";
import IssueInviteMembers from "@/components/issue/IssueInviteMembers";
import IssueSummaryList from "@/components/issue/IssueSummaryList";
import MembersMultiSelectDropdown from "@/components/members/MembersMultiSelectDropdown";
import ResolveIssue from "@/views/issue/ResolveIssue";

// import SummaryWordCount from '@/components/issue/SummaryWordCount'

export default {
  name: "IssueView",
  props: ["id"],
  components: {
    AppCardFullHeight,
    AppCardNotification,
    AppCKEditor,
    AppFileInput,
    Dropdown,
    TagInput,
    IssueInfoSection,
    IssueComment,
    IssueNextStepSection,
    IssueSummaryList,
    IssueInviteMembers,
    MembersMultiSelectDropdown,
    ResolveIssue
    // SummaryWordCount,
  },
  data() {
    return {
<<<<<<< HEAD
      visibilitySelected: "draft",
=======
      loading: true,
      visibilitySelected: 'draft',
>>>>>>> c00bbbef8efea3fef37c8a6f7ad6b5c4fc9f82a3
      showResolveIssueModal: false,
      showSummaryData: false,
      showPrivatePopup: false,
      allTags: [],
      visibilityOptions: [
        {
          title: "Company",
          subtitle: "Visible to the entire company",
          icon: "icon-pf-company",
          value: "company"
        },
        {
          title: "Private",
          subtitle: "Visible only to people you choose to share with",
          icon: "icon-pf-lock",
          value: "private"
        }
      ],
      form: {
        title: "<h4>Untitled</h4>",
        visibility: "draft",
        priority: 0,
        due_date: new Date(),
        people_involved: [],
        issue_file: [],
        issue_summary: [
          {
            type: "summary",
            text: ""
          },
          {
            type: "summary-imp",
            text: ""
          },

          {
            type: "summary-output",
            text: ""
          }
        ],
        issue_step: [
          {
            text: ""
          }
        ]
      },
      tag_id: [],
      selected_tag_list: [],
      priorityOptions: [
        {
          title: "Urgent",
          icon: "icon-pf-flag",
          variant: "danger",
          value: 0
        },
        {
          title: "High",
          icon: "icon-pf-flag",
          variant: "warning",
          value: 1
        },
        {
          title: "Medium",
          icon: "icon-pf-flag",
          variant: "success",
          value: 2
        },
        {
          title: "Low",
          icon: "icon-pf-flag",
          variant: "primary",
          value: 3
        }
      ],
      people: [],
      people_invited_user_detail: [],
      commentData: [],
      placeholderData: {
        summary: `We recently had a meeting and agreed terms for our new marketing partnership  with ABC company. Formalising the contract and getting the campaign live requires input
          from a number of our teams and is taking far longer than we first thought.
          We need to get the contract signed and ensure campaign is live before the end of the month`,
        summaryImp:
          "• This partnership could drive up to 15% of our new user acquisition in;",
        summaryOutput: "• Finalise and sign contract by 31 July",
        additionalInfo:
          "James is the point of contact on the marketing side, while Sarah will be responsible to ensure the affiliate tracking is set up."
      },
      actions: []
    };
  },
  computed: {
<<<<<<< HEAD
    ...mapGetters(["user"]),

=======
    ...mapGetters(['user']),
>>>>>>> c00bbbef8efea3fef37c8a6f7ad6b5c4fc9f82a3
    summaryData() {
      return this.showSummaryData && this.form.issue_summary.length > 0
        ? this.form.issue_summary.filter(data => data.type === "summary")
        : [];
    },
    summaryImpData() {
      return this.form.issue_summary.length > 0
        ? this.form.issue_summary.filter(data => data.type === "summary-imp")
        : [];
    },
    summaryOutputData() {
      return this.form.issue_summary.length > 0
        ? this.form.issue_summary.filter(data => data.type === "summary-output")
        : [];
    },
    summaryAdditionalInfoData() {
      return this.form.issue_summary.length > 0
        ? this.form.issue_summary.filter(
            data => data.type === "additional-info"
          )
        : [];
    },
    showFileInput() {
      return this.form.issue_file.length === 0
    }
  },
  watch: {
    "$route.params.id": {
      immediate: true,
      handler: function(id) {
        this.id = id;
        this.getIssueIdData();
      }
    }
  },
  mounted() {
    this.getMembersListing()
    this.getTags()
  },
  methods: {
    ...mapActions([
      "getMentionsList",
      "createIssueData",
      "updateIssueData",
      "getDefaultTags",
      "updateTag",
      "deleteTag",
      "addMembersToIssue",
      "updateIssueStatus",
      "saveUploadedFile",
      "removeUploadedFile",
      "getIssuesCount",
      // 'updateDraftIssueTitle',
      "deleteIssue"
      // 'getDraftIssues'
    ]),
    showDueDate() {
      return this.$refs.dueDate.$el.childNodes[0].click()
    },
    async setFormTag(option) {
      this.form.tags = await this.updateTag({
        id: this.id,
        tag_name: option
      });
    },
    async deleteFormTag(option) {
      this.form.tags = await this.deleteTag({
        id: this.id,
        tag_id: option
      });
    },
    setFormVisibility(option) {
      this.visibilitySelected = option
    },
    saveFormVisibility(option, additionalParam) {
      if (option === 'draft' && !additionalParam) {
        option = 'company'
      }
      // const oldVisibility = this.form.visibility
      this.form.visibility = option;
      if (option === "private") {
        //  this.showPrivatePopup = true
      }
      this.saveIssueData()
      this.actions.push({action: 'updateVisibility'})
      // if (oldVisibility === 'draft' || option === 'draft') {
      //   this.actions.push({ action: 'updateDraftsList' })
      // }
    },
    setFormPriority(option) {
      this.form.priority = option;
      this.saveIssueData();
    },
    async getMembersListing() {
      this.people = await this.getMentionsList();
    },
    saveSummaryData(value, data) {
      this.form[value] = data;
      this.saveIssueData();
      // if (this.form.visibility === 'draft' && value === 'title') {
      //   this.actions.push({ action: 'updateDraftTitle', id: this.form.id, title: data })
      // }
    },
    async saveIssueData() {
      if (this.id) {
        this.form.id = this.id;
        await this.updateIssueData(this.form);
        this.todoActions();
      } else {
        const data = await this.createIssueData(this.form);
        await this.$router.push({
          name: "issue-view",
          params: {
            id: data.unique_id
          }
        });
      }
    },
    todoActions() {
      if (this.actions.length) {
        for (const [key, act] of Object.entries(this.actions)) {
          if (act.action === "updateVisibility") {
            this.getIssuesCount();
          }
          // if (act.action === 'updateDraftTitle') {
          //   this.updateDraftIssueTitle({ id: act.id, title: act.title })
          // } else if (act.action === 'updateVisibility') {
          //   this.getIssuesCount()
          // }
          // else if (act.action === 'updateDraftsList') {
          //   this.getDraftIssues()
          // }
        }
        this.actions = [];
      }
    },
    async getSidebarComments() {
      this.commentData = await this.getComments(this.id)
    },
    async getTags() {
      this.allTags = await this.getDefaultTags()
    },
    async getIssueIdData() {
      const data = await this.updateIssueData({
        id: this.id
      });
      if (data) {
        this.showSummaryData = true;
        this.form = Object.assign({}, data);
      }
    },
    async addMembers(option) {
      this.form.people_follow_user_detail = await this.addMembersToIssue({
        type: "follower",
        issue_id: this.id,
        member_id: option,
        is_follower: 1
      });
    },
    async removeMembers(option) {
      this.form.people_follow_user_detail = await this.addMembersToIssue({
        type: "follower",
        issue_id: this.id,
        member_id: option,
        is_follower: 0
      });
    },
    async updateArchiveStatus(deleteId) {
      await this.updateIssueStatus({
        is_delete: deleteId,
        issue_id: this.id
      });
      await this.$router.push({ name: "home" });
    },
    async updateResolveStatus() {
      this.showResolveIssueModal = false;
      await this.getIssueIdData();
      await this.getIssuesCount()
      await this.$router.push({name: 'home'})
    },
<<<<<<< HEAD


=======
>>>>>>> c00bbbef8efea3fef37c8a6f7ad6b5c4fc9f82a3
    async reopenResolveIssue() {
      this.form.is_resolved = await this.updateIssueStatus({
        is_reopen: 1,
        issue_id: this.id
      });
    },
    async saveFile(data) {
      const formData = new FormData();
      formData.append("file", data);
      formData.append("name", data.name);
      formData.append("issue_id", this.id);
      formData.append("type", data.type);
      this.form.issue_file = await this.saveUploadedFile(formData);
    },
    async removeFile(id) {
      this.form.issue_file = await this.removeUploadedFile({
        issue_id: this.id,
        id: id
      });
    },
    triggerFileEvent() {
      this.form.issue_file.push({});
    },
    getFileData(data) {
      this.form.issue_file = data;
    },
    async deleteDraft() {
      await this.deleteIssue({ issue_id: this.id });
      await this.$router.push({ name: "home" });
    }
  }
};
</script>

<style lang="scss" scoped>
.draft-title ::v-deep .ck.ck-editor__editable_inline p {
  margin-top: 8px;
  font-weight: 600;
  font-size: 20px;
  color: var(--black);
}

.visibility-options {
  ::v-deep .dropdown-menu {
    width: 371px;
  }
}

.duedate-datepicker {
  ::v-deep .dropdown-toggle {
    visibility: hidden;
    top: -10px;
    left: -75px;
  }
}

.modal-action {
  padding: 9px;
  width: 108px;
}

.modal-draft {
  padding: 9px;
  width: 157px;
}

.section-info-wrapper {
  margin-top: 42px;
  margin-bottom: 24px;

  ::v-deep .mt-px {
    margin-top: 1px;
  }
}

.pr-40 {
  padding-right: 60px;
}
::v-deep .placeholder-draft-title {
  .ck-placeholder::before,
  p {
    font-weight: 600;
    font-size: 20px;
    color: var(--black);
  }
  p {
    min-width: 91px !important;
    min-height: 20px !important;
  }
  .ck.ck-editor__editable_inline > :last-child {
    margin: 8px;
  }
}
</style>
