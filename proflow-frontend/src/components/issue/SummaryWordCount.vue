<template>
<div>
    <b-row>
        <b-col>
            Summary {{totalLength}} / {{maxCharacters}}
        </b-col>
    </b-row>
    <b-row>
        <b-col>
            <ckeditor :editor="editor" :value="(value.length > 0)?value[0].text:''" :disabled="editorDisabled" :config="editorConfig"></ckeditor>
        </b-col>
    </b-row>

</div>
</template>

<script>
import BalloonEditor from '@ckeditor/ckeditor5-editor-balloon/src/ballooneditor'
import EssentialsPlugin from '@ckeditor/ckeditor5-essentials/src/essentials'
import BoldPlugin from '@ckeditor/ckeditor5-basic-styles/src/bold'
import ItalicPlugin from '@ckeditor/ckeditor5-basic-styles/src/italic'
import ParagraphPlugin from '@ckeditor/ckeditor5-paragraph/src/paragraph'
import WordCount from '@ckeditor/ckeditor5-word-count/src/wordcount'

export default {
  name: 'SummaryWordCount',
  props: {
    value: {
      type: [Array],
      default: () => ([])
    }
  },
  data () {
    return {

      editorDisabled: false,
      totalLength: 0,
      maxCharacters: 36,
      editor: BalloonEditor,
      editorConfig: {
        plugins: [
          WordCount,
          EssentialsPlugin,
          BoldPlugin,
          ItalicPlugin,
          ParagraphPlugin
        ],
        wordCount: {
          onUpdate: stats => {
            // Prints the current content statistics.
            this.totalLength = stats.characters
            if (this.totalLength >= this.maxCharacters) {
              // this.editorDisabled = true;
            } else {
              // this.editorDisabled = false;
            }
          }
        },
        toolbar: []
      }
    }
  },
  methods: {

  }
}
</script>

<style lang="scss" scoped>

</style>
