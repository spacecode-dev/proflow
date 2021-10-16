<template>
  <div class="overflow-hidden app-editor">
    <b-row v-if="title" class="mb-2">
      <b-col>
        <span class="font-weight-semibold">{{ title }}</span>
        <label v-if="wordCount" class="typo__label">{{totalLength}} / {{maxCharLength}}</label>
      </b-col>
    </b-row>
    <b-row>
       <b-col
         class="app-ckeditor-width"
        :class="{
        'is-placeholder': isList && !currentValue,
        'd-bullet': isList
      }"
      >

      <ckeditor
          v-if="useDefaultPlaceholder"
          :editor="editor"
          :value="value"
          :config="editorConfig"
          :formName="formName"
          @ready="onEditorReady"
          @focus="onEditorFocusEnabled"
          :disabled="disabled"
          @input="onEditorInput"
          @blur="onEditorBlurEnabled"
          ref="ckblur"

        />

        <ckeditor
           v-else
          :editor="editor"
          :value="initialValue"
          :config="editorConfig"
          :formName="formName"
          :disabled="disabled"
          @focus="onEditorFocus"
          @blur="onEditorBlur"
          @ready="onEditorReady"

        />
      </b-col>
    </b-row>
  </div>
</template>

<script>
// required
import BalloonEditor from '@ckeditor/ckeditor5-editor-balloon/src/ballooneditor'
import EssentialsPlugin from '@ckeditor/ckeditor5-essentials/src/essentials'
import ParagraphPlugin from '@ckeditor/ckeditor5-paragraph/src/paragraph'
import List from '@ckeditor/ckeditor5-list/src/list'
// optional
import BoldPlugin from '@ckeditor/ckeditor5-basic-styles/src/bold'
import ItalicPlugin from '@ckeditor/ckeditor5-basic-styles/src/italic'
import MentionPlugin from '@ckeditor/ckeditor5-mention/src/mention'

import WordCountPlugin from '@ckeditor/ckeditor5-word-count/src/wordcount'

export default {
  name: 'AppCKEditor',
  props: {
    formName: {
      type: [String, Number],
      default: ''
    },
    value: {
      type: String,
      default: ''
    },
    title: {
      type: String,
      default: ''
    },
    /* enables bold plugin */
    bold: {
      type: Boolean,
      default: false
    },
    /* enables italic plugin */
    italic: {
      type: Boolean,
      default: false
    },
    /* any other toolbar option */
    toolbarOptions: {
      type: Array,
      default: () => []
    },
    /* enables mention plugin */
    mention: {
      type: [Boolean, Object],
      default: false
    },
    /* pass with mention to list out people */
    people: {
      type: Array,
      default: () => []
    },
    /* enables placeholder */
    placeholder: {
      type: String,
      default: ''
    },
    /* enables word count plugin */
    wordCount: {
      type: [Boolean, Object],
      default: false
    },
    maxCharLength: {
      type: Number,
      default: 360
    },
    enableEnterMode: {
      type: Boolean,
      default: false
    },
    enableNewEditorOnEnter: {
      type: Boolean,
      default: false
    },
    clearData: {
      type: Boolean,
      default: true
    },
    // display with bullet points
    isList: {
      type: Boolean,
      default: false
    },
    useDefaultPlaceholder: {
      type: Boolean,
      default: true
    }
  },
  data () {
    return {
      disabled: false,
      isFocused: false,
      currentValue: this.value,
      initialValue: this.value || this.placeholder,
      comment: '',
      editor: BalloonEditor,
      editorConfig: {
        maxLength: 13,
        plugins: [EssentialsPlugin, ParagraphPlugin, List]
      },
      defaultMentionConfig: {
        feeds: [
          {
            marker: '@',
            feed: this.getFeedItems,
            itemRenderer: this.customItemRenderer
          }
        ]
      },
      totalLength: 0,
      defaultWordCountConfig: {

        onUpdate: stats => {
          // prints the current content statistics.
          this.totalLength = stats.characters
          if (this.totalLength >= this.maxCharLength) {
          } else {
            // this.editorDisabled = false;
          }
        }
      }
    }
  },
  computed: {

  },
  created () {
    if (this.useDefaultPlaceholder) {
      this.editorConfig.placeholder = this.placeholder
    }
    if (this.bold) this.initializeBoldPlugin()
    if (this.italic) this.initializeItalicPlugin()
    if (this.mention) this.initializeMentionPlugin()
    if (this.wordCount) this.initializewordCountPlugin()
  },

  methods: {
    initializeBoldPlugin () {
      this.editorConfig.toolbar.push('bold')
      this.editorConfig.plugins.push(BoldPlugin)
    },
    initializeItalicPlugin () {
      this.editorConfig.toolbar.push('italic')
      this.editorConfig.plugins.push(ItalicPlugin)
    },
    initializeMentionPlugin () {
      const isObject = typeof this.mention === 'object'

      this.editorConfig.mention = isObject
        ? this.mention
        : this.defaultMentionConfig
      this.editorConfig.plugins.push(MentionPlugin)
    },
    initializewordCountPlugin () {
      const isObject = typeof this.wordCount === 'object'

      this.editorConfig.wordCount = isObject
        ? this.wordCount
        : this.defaultWordCountConfig
      this.editorConfig.plugins.push(WordCountPlugin)
    },
    getFeedItems (queryText) {
      // As an example of an asynchronous action, return a promise
      // that resolves after a 100ms timeout.
      // This can be a server request or any sort of delayed action.
      return new Promise(resolve => {
        setTimeout(() => {
          const searchString = queryText.toLowerCase()
          // Filter out the full list of all people to only those matching the query text.
          const itemsToDisplay = this.people
            .filter(item => item.name.toLowerCase().includes(searchString))
            // Return 10 people max - needed for generic queries when the list may contain hundreds of elements.
            .slice(0, 10)
          resolve(itemsToDisplay)
        }, 100)
      })
    },
    customItemRenderer (item) {
      const itemElement = document.createElement('div')
      const avatar = document.createElement('img')
      const userNameElement = document.createElement('span')
      const avatarText = document.createElement('span')
      itemElement.classList.add('mention__item')
      avatarText.setAttribute('class', 'mention-avatar')

      avatar.setAttribute('src', `${item.profile_picture}`)
      userNameElement.classList.add('mention__item__full-name')

      avatarText.textContent = item.email.charAt(0).toUpperCase()
      if (item.profile_picture === '') {
        itemElement.appendChild(avatarText)
        userNameElement.textContent = item.email
      } else {
        itemElement.appendChild(avatar)
        userNameElement.textContent = item.name
      }
      itemElement.appendChild(userNameElement)

      return itemElement
    },
    onEditorFocus () {
      this.isFocused = true
      if (this.currentValue) return
      this.initialValue = ''
    },
    onEditorFocusEnabled (editor, data) {

      
    },
    onEditorBlurEnabled (editor, data) {
      // if (this.enableEnterMode) {
      //   this.currentValue = data.getData()
      //   this.$emit('save-data', this.formName, this.currentValue)
      // }
    },

    // onEditorBlur (editor, data) {
    //   this.isFocused = false
    //   if (!this.value && !this.currentValue) {
    //     this.initialValue = this.placeholder
    //   }
    //   if (!this.enableEnterMode) {
    //     this.currentValue = data.getData()
    //     this.initialValue = data.getData() || this.placeholder
    //     this.$emit('save-data', this.formName, this.currentValue)
    //   }
    // },

    onEditorInput (input) {

        this.currentValue = input
        this.$emit('save-data', this.formName, this.currentValue)
      
    },
    onEditorReady (editor) {
      this.MentionCustomization(editor)
    
      if (this.wordCount) {
        editor.editing.view.document.on('keydown', (evt, data) => {
          if (this.totalLength >= this.maxCharLength) {
            editor.setData(editor.getData().substr(0, this.maxCharLength))
            editor.model.change(writer => {
              writer.setSelection(writer.createPositionAt(editor.model.document.getRoot(), 'end'))
            })
          }
        })
      }

      if (this.enableEnterMode) {
        editor.editing.view.document.on('enter', (evt, data) => {
          this.$emit('save-data', this.formName, editor.getData())
          // Find the next input
          this.$refs.ckblur.$el.blur()

          data.preventDefault()
          evt.stop()
        }, { priority: 'high' })
      }

      // if (this.enableNewEditorOnEnter) {
      //   editor.editing.view.document.on('keydown', (evt, data) => {
      //     if (data.keyCode === 13 && !data.shiftKey) {
      //       this.$emit('new-editor', this.formName, editor.getData())
      //     }
      //   })
      // }
    },
    MentionCustomization (editor) {
    // The upcast converter will convert view <a class="mention" href="" data-user-id="">
    // elements to the model 'mention' text attribute.
      editor.conversion.for('upcast').elementToAttribute({
        view: {
          name: 'span',
          key: 'data-mention',
          classes: 'mention',
          attributes: {
            'data-user-id': true,
            'data-mention': true
          }
        },
        model: {
          key: 'mention',
          value: viewItem => {
            // The mention feature expects that the mention attribute value
            // in the model is a plain object with a set of additional attributes.
            // In order to create a proper object use the toMentionAttribute() helper method:
            const mentionAttribute = editor.plugins.get('Mention').toMentionAttribute(viewItem, {
              // Add any other properties that you need.
              userId: viewItem.getAttribute('data-user-id')
            })

            return mentionAttribute
          }
        },
        converterPriority: 'high'
      })

      // Downcast the model 'mention' text attribute to a view <a> element.
      editor.conversion.for('downcast').attributeToElement({
        model: 'mention',
        view: (modelAttributeValue, viewWriter) => {
          // Do not convert empty attributes (lack of value means no mention).
          if (!modelAttributeValue) {
            return
          }
          if (modelAttributeValue.userId !== 'undefined') {
            this.$emit('mention-data', modelAttributeValue.userId)
          }

          return viewWriter.createAttributeElement('span', {
            class: 'mention',
            'data-mention': modelAttributeValue.id,
            'data-user-id': modelAttributeValue.userId
          }, {
            // Make mention attribute to be wrapped by other attribute elements.
            priority: 20,
            // Prevent merging mentions together.
            id: modelAttributeValue.uid

          })
        },
        converterPriority: 'high'
      })
    }
  }
}
</script>

<style lang='scss' scoped>
.app-editor {
  .ck.ck-editor__editable {
    &:not(.ck-editor__nested-editable).ck-focused {
      outline: none;
      border: none;
      box-shadow: none;
    }
  }

  .ck.ck-editor__editable_inline {
    padding: 0px;
    border: none;
  }
 
}

::v-deep p {
  color: #1A1A1A;
}
::v-deep .d-bullet:not(.is-placeholder) {
  p {
    position: relative;
    padding-left: 12px;
    margin-bottom: 6px;

    &:before {
      position: absolute;
      left: 0px;
      content: "•";
    }
  }
}

::v-deep .is-placeholder * {
  color: var(--placeholder-color) !important;
}

span.mention {
  color: var(--primary) !important;
}

.ck-content .mention {
  background: white;
}

.ck-mentions {
  .mention__item {
    display: block;
    img {
      border-radius: 100%;
      height: 30px;
      width: 30px;
    }
    span {
      margin-left: 0.5em;
    }
    .mention__item__full-name {
      color: hsl(0, 0%, 45%);
    }
    &:hover {
      &:not(.ck-on) {
        .mention__item__full-name {
          color: hsl(0, 0%, 40%);
        }
      }
    }
  }
  .mention__item.ck-on {
    span {
      color: var(--ck-color-base-background);
    }
  }

  .mention-avatar {
    width: 30px;
    height: 30px;
    font-weight: 600;
    font-size: 14px;
    line-height: 150%;
    display: inline-block;
    border-radius: 100%;
    background-color: var(--info);
    color: var(--gray);
    padding: 11px;
    margin-left: 0rem !important;
    padding-top: 5px;
    padding-left: 10px;
  }

}
</style>
