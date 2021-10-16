<template>
  <div class="pr-0 next-step-section-wrapper">
    <div class="font-weight-semibold">Next steps</div>
    <draggable tag="ul" :list="nextStepList" class="list-group" handle=".handle" @change="log">
      <div
        v-for="(step, idx) in nextStepList"
        :key="`next-step-${step.id}`"
        class="position-relative next-step-wrapper"
      >
        <div class="cursor-pointer position-absolute drag-handle">
          <AppIcon icon="icon-pf-drag" class="handle" />
        </div>

        <div class="d-flex align-items-center">
          <div
            class="d-flex flex-grow-1 justify-content-between mt-2 px-3 next-step"
            :class="step.is_resolved === 1 ? 'is-completed' : ''"
          >
            <AppCKEditor
              v-model="step.text"
              :placeholder="initialText"
              :formName="step.id"
              :clearData="false"
              enableEnterMode
              class="d-flex flex-row flex-grow-1 align-self-center"
              @save-data="updateTextData"
            />

            <!-- duedate picker -->
            <b-form-datepicker
              v-if="step.is_resolved === 0"
              class="duedate-datepicker"
              :class="{ 'has-due-date': step.due_date }"
              ref="dueDate"
              right
              button-only
              button-variant="transparent"
              v-model="form.due_date"
              :initial-date="step.due_date"
              @input="updateDueDate(step.id, form.due_date)"
            >
              <template v-slot:button-content>
                <div
                  v-if="!step.due_date"
                  class="d-flex align-self-center align-items-center justify-content-center duedate-icon rect-dotted"

                >
                  <AppIcon icon="icon-pf-card-success" icon-class="small mr-2" class="ml-2" />
                </div>
                <div
                  v-else
                  class="d-flex align-self-center align-items-center justify-content-center font-weight-normal duedate-icon mr-3"
                  :class="dateColor(step.due_date)"
                >
                  <AppIcon icon="icon-pf-card-success" icon-class="small mr-2" />
                  <span class="pf-subheading ml-2">
                    {{ step.due_date | formatDate("MMM D") }}
                  </span>
                </div>

                <!-- use .is-success (green), .is-warning (orange), .is-error (red) with .duedate-icon to color text and icon -->
                <!-- <div class="d-flex align-self-center align-items-center justify-content-center font-weight-normal duedate-icon mr-3">
                <AppIcon icon="calendar" />
                <span class=" pf-subheading ml-2">June 15</span>
                </div>-->
              </template>
            </b-form-datepicker>

           

            <!-- add member -->
            <div
              v-if="step.is_resolved === 0"
              class="d-flex align-self-center align-items-center justify-content-center rounded-circle cursor-pointer"
            >
              <MembersMultiSelectDropdown
                :stepId="step.id"
                :users="people"
                :selected-users="step.member_list"
                @add="updateMembers"
                @remove="updateMembers"
              >  <b-avatar-group
                  v-if="step.is_resolved === 0 && step.is_unassigned == 0 && step.member_list.length > 0"
                  size="24px"
                  class="d-flex align-items-center"
                  :class="{ 'ml-2': step.member_list.length > 0 }"
                >
                  <AppAvatar
                    v-for="item in step.member_list"
                    :key="item.id"
                    :text="(item.name)?item.name:item.email"
                    :src="item.profile_picture"
                    :grayscale="form.visibility === 'draft'"
                  />
                </b-avatar-group>
              <div class="d-flex rounded-circle bg-info mt-1 mb-1 ml-1 unassignedmin"   v-if="step.is_resolved === 0 && step.is_unassigned == 1">
                <AppIcon icon="question" class="mx-auto text-muted align-self-center" />
              </div>

                <b-dropdown-item
                  slot="after_search"
                  v-if="visibiltiy !== 'private'"
                >
                  <div class="d-flex py-1"  @click.stop="(step.is_unassigned == 1)?addUnassingedUser(step.id,'assigned'):addUnassingedUser(step.id,'unassigned')"
            >
                    <div class="d-flex rounded-circle bg-info mr-3 unassigned">
                      <AppIcon icon="question" class="mx-auto text-muted align-self-center" />
                    </div>
                    <div class="d-flex flex-column text-wrap mr-auto">
                      <p class="font-weight-semibold mb-2">Unassigned</p>
                      <p class="pf-subheading mb-1 small">
                        If you choose not to assign anyone to a next step, the
                        issue will be placed under “Unassigned” tab
                      </p>
                    </div>
                    <AppIcon
                      icon="icon-pf-check text-muted"
                      icon-class="mr-0"
                      v-if="step.is_unassigned == 1"
                    />
                  </div>
                </b-dropdown-item>
              </MembersMultiSelectDropdown>
            </div>
            <AppIcon
              v-if="step.is_resolved === 1"
              icon="icon-pf-check"
              class="align-self-center icon-check"
            />
          </div>

          <div class="d-inline-flex ml-1 next-step-rhs-wrapper">
            <b-dropdown
              class="next-step-action"
              variant="text"
              right no-caret
            >
              <template v-slot:button-content>
                <div class="px-1 actions">
                  <AppIcon icon="three-dots" icon-class="mr-0" />
                </div>
              </template>
              <b-dropdown-item  link-class="link-icon-resolved" v-if="step.is_resolved === 0" @click="changeStatus(step.id, 1)">
                <span class="pf-heading">
                  <AppIcon icon="icon-pf-icon-check-grey" />Resolve
                </span>
              </b-dropdown-item>
              <b-dropdown-item link-class="link-icon-unresolved" v-if="step.is_resolved === 1" @click="changeStatus(step.id, 0)">
                <span class="pf-heading">
                  <AppIcon icon-class="next-step-icon-unresolved mr-2" icon="icon-pf-reverse-arrow" />Unresolve
                </span>
              </b-dropdown-item>
              <b-dropdown-item  @click="deleteStep(step.id, idx)">
                <span>
                  <span class="pf-heading">
                    <AppIcon icon="icon-pf-icon-delete-grey" variant="info" />Delete
                  </span>
                </span>
              </b-dropdown-item>
            </b-dropdown>
            <IssueStickyComments
              type="steps"
              :commentLists="step.comments"
              :id="step.id"
              :issueId="issueId"
              :people="people"
            />
          </div>
        </div>
      </div>
    </draggable>
    <!-- <div class="d-flex justify-content-between mt-2 px-3 next-step">
       <input
        type="text"
        @input="createFormText"
        v-model="form.textname"
        class="d-flex flex-row flex-grow-1 align-self-center app-text-editor"
        :placeholder="initialText"
      /> -->
      <!-- <AppCKEditor
        v-model="form.text"
        :placeholder="initialText"
        formName="text"
         :clearData="false"
        class="d-flex flex-row flex-grow-1 align-self-center"
        @save-data="createFormText"
      /> -->

      <!-- duedate picker -->
      <!-- <b-form-datepicker
        class="duedate-datepicker"
        ref="dueDate"
        right
        button-only
        button-variant="transparent"
        v-model="form.stepsData.due_date"
      >
        <template v-slot:button-content>
          <div
            v-if="!form.stepsData.due_date"
            class="d-flex align-self-center align-items-center justify-content-center duedate-icon mr-3 rect-dotted"
          >
            <AppIcon icon="calendar" />
          </div>
          <div
            v-else
            class="d-flex align-self-center align-items-center justify-content-center font-weight-normal duedate-icon mr-3"
            :class="dateColor(form.stepsData.due_date)"
          >
            <AppIcon icon="calendar" />
            <span class="pf-subheading ml-2">
              {{
              form.stepsData.due_date | formatDate("MMM D")
              }}
            </span>
          </div> -->

          <!-- use .is-success (green), .is-warning (orange), .is-error (red) with .duedate-icon to color text and icon -->
          <!-- <div class="d-flex align-self-center align-items-center justify-content-center font-weight-normal duedate-icon mr-3">
              <AppIcon icon="calendar" />
              <span class=" pf-subheading ml-2">June 15</span>
          </div>-->
        <!-- </template>
      </b-form-datepicker>

          <b-avatar-group
            v-if="form.is_resolved === 0"
            size="24px"
            class="d-flex align-items-center"
          >
            <AppAvatar
              v-for="item in form.member_list"
              :key="item.id"
              :src="item.profile_picture"
              :grayscale="form.visibility === 'draft'"
            />
          </b-avatar-group> -->

      <!-- add member -->
      <!-- <div
        class="d-flex align-self-center align-items-center justify-content-center rounded-circle cursor-pointer"
      > -->
        <!-- <MembersMultiSelectDropdown
          :users="people"
          @add="addToDataMembers"
          @remove="addToDataMembers"
        >
          <b-dropdown-item slot="after_search">
            <div class="d-flex py-1">
              <div class="d-flex rounded-circle bg-info mr-3 unassigned">
                <AppIcon icon="question" class="mx-auto text-muted align-self-center" />
              </div>
              <div class="d-flex flex-column text-wrap mr-auto" v-if="visibiltiy !== 'private'">
                <p class="font-weight-semibold mb-2">Unassigned</p>
                <p class="pf-subheading mb-1 small">
                  If you choose not to assign anyone to a next step, the issue
                  will be placed under “Unassigned” tab
                </p>
              </div>
              <AppIcon icon="icon-pf-check text-muted" icon-class="mr-0" v-if="!form.member_id" />
            </div>
          </b-dropdown-item>
        </MembersMultiSelectDropdown>
      </div>
    </div> -->

    <div
      class="px-3 pt-1 mt-2 cursor-pointer font-weight-medium pf-subheading btn-add-another"
      @click="add"
    >+ Add Another</div>
  </div>
</template>

<script>
import { mapActions } from 'vuex'
import draggable from 'vuedraggable'

import AppCKEditor from '@/components/ckeditor/AppCKEditor'
import MembersMultiSelectDropdown from '@/components/members/MembersMultiSelectDropdown'
import IssueStickyComments from '@/components/issue/IssueStickyComments'

const moment = require('moment-timezone')

export default {
  name: 'IssueNextStepSection',
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
    },
    visibiltiy: {
      type: [String],
      default: () => []
    }

  },
  components: {
    draggable,
    AppCKEditor,
    MembersMultiSelectDropdown,
    IssueStickyComments
  },
  data () {
    return {
      clearNextStepData: false,
      options: [
        'Select option',
        'options',
        'selected',
        'mulitple',
        'label',
        'searchable',
        'clearOnSelect',
        'hideSelected',
        'maxHeight',
        'allowEmpty',
        'showLabels',
        'onChange',
        'touched'
      ],
      initialText: 'Conduct a meeting to define and assign next steps',
      testText: '',
      form: {
      },
      hoveredIndex: -1,
      nextStepList: this.value

    }
  },

  computed: {

  },

  watch: {
    issueId: function (id) {
      this.form.issue_id = id
    },
    value: function (data) {
      this.nextStepList = data
    }

  },
  methods: {
    ...mapActions([
      'updateIssueStepsData',
      'deleteIssueStepsData',
      'savePositionData'
    ]),

    dateColor (date) {
      const today = moment()
      const tommorow = moment().add(1, 'day')
      const dayaftertom = moment().add(2, 'day')
      if (moment(date).isSame(today, 'day') || moment(date).isBefore(today)) {
        return 'is-error'
      } else if (
        moment(date).isSame(tommorow, 'day') ||
        moment(date).isSame(dayaftertom, 'day')
      ) {
        return 'is-warning'
      } else if (moment(date).isAfter(dayaftertom, 'day')) {
        return 'is-success'
      }
    },
    // add the data
    async add () {
      this.form.step_id = ''
      this.form.issue_id = this.issueId
      this.nextStepList = await this.createIssueStepsData()
    },
    // create data members
    addToDataMembers (data) {
      this.form = {}
      this.form.issue_id = this.issueId
      this.form.member_id = data
    },
    // update form text
    updateTextData (value, data) {
      this.form = {}
      if (value) {
        this.form.step_id = value
      }
      this.form.issue_id = this.issueId
      this.form.text = data
      this.createIssueStepsData()
      // this.$emit('get-data')
    },
    // update due date
    async updateDueDate (stepId, dueDate) {
      this.form = {}
      if (stepId) {
        this.form.step_id = stepId
      }
      this.form.issue_id = this.issueId
      this.form.due_date = dueDate
      this.nextStepList = await this.createIssueStepsData()
    },
    showDueDate () {
      return this.$refs.dueDate.$el.childNodes[0].click()
    },
    // change resolve status
    async changeStatus (stepId, resolved) {
      this.form = {}
      this.form.step_id = stepId
      this.form.issue_id = this.issueId
      this.form.is_resolved = resolved
      this.nextStepList = await this.createIssueStepsData()
    },
    // change position data
    log () {
      this.savePositionData(this.nextStepList)
    },
    // create issue step
    async createIssueStepsData () {
      return await this.updateIssueStepsData(this.form)
    },
    // delete step
    deleteStep (id, deleteID) {
      this.nextStepList.splice(deleteID, 1)
      this.deleteIssueStepsData(id)
      this.$emit('get-data')
    },
    // update members
    updateMembers (data, stepId) {
      this.form = {}
      if (stepId) {
        this.form.step_id = stepId
      }
      this.form.issue_id = this.issueId
      this.form.member_id = data
      this.createIssueStepsData()
      this.$emit('get-data')
    },

    async addUnassingedUser (stepId = '', assigned) {
      this.form = {}
      if (stepId) {
        this.form.step_id = stepId
      }
      this.form.issue_id = this.issueId
      this.form.member_id = assigned
      this.nextStepList = await this.createIssueStepsData()
    }
  }
}
</script>

<style lang="scss" scoped>
.next-step-section-wrapper {
  padding-right: 20px;
}

::v-deep .next-step-wrapper {
  &:hover {
    .drag-handle, .actions, .comment-icon {
      opacity: 1 !important;
    }
  }
}

.next-step {
  min-width: calc(100% - 64px);
  min-height: 36px;
  border: 1px solid var(--gray-outline);
  box-sizing: border-box;
  border-radius: 6px;

  &.is-completed {
    border: 2px solid var(--blue-light);
  }

  .app-text-editor {
    max-width: 100%;
    border: none;
  }

  ::v-deep .app-editor .row {
    width: 100%;
  }

  ::v-deep .dropdown-toggle {
    padding: 0px;
  }

  .duedate-icon {
    &.rect-dotted {
      width: 59px;
      height: 18px;
      border: 1px dashed var(--black-90);
      box-sizing: border-box;
      border-radius: 4px;
    }

    &.is-success {
      color: var(--green-light);
    }

    &.is-warning {
      color: var(--orange-light);
    }

    &.is-error {
      color: var(--pink);
    }

    svg {
      width: 10px;
      height: 10px;
      margin-right: 0px !important;
    }
  }

  .duedate-datepicker {
    right: 0px;

    &.has-due-date {
      .pf-subheading {
        width: 30px;
      }
    }
  }

  .icon-check {
    color: var(--blue-light);
  }

  .unassigned {
    min-width: 30px;
    height: 30px;
  }

   .unassignedmin {
    min-width: 24px;
    height: 24px;
  }

  ::v-deep .b-avatar {
    min-width: 24px;
  }
}

.drag-handle {
  opacity: 0;
  top: 18px;
  left: -20px;
  color: var(--gray);

  &:hover {
    opacity: 1;
    cursor: move;
  }
}

.next-step-rhs-wrapper {
  ::v-deep .next-step-action {
    .btn {
      height: 20px;
      padding: 0px;
      border-radius: 0px;
      color: var(--gray);

      .actions {
        opacity: 0;
        transition: 0.3s;

        &:hover {
          opacity: 1;
          background-color: var(--info);
        }
      }
    }
  }

  ::v-deep .no-comments {
    margin-right: 8px !important;
  }
}

.btn-add-another {
  color: var(--blue-light);
}
::v-deep .next-step-icon-unresolved {
  font-size: 11px;
}

::v-deep .link-icon-unresolved  {
  padding-left: 20px;
}

::v-deep .link-icon-resolved  {
  padding-left: 21px;
}
</style>
