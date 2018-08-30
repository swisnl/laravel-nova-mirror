<template>
    <trix-editor
        ref="theEditor"
        @trix-change="handleChange"
        @trix-initialize="initialize"
        @trix-attachment-add="handleAddFile"
        @trix-attachment-remove="handleRemoveFile"
        :value="value"
        :placeholder="placeholder"
    />
</template>

<script>
import Trix from 'trix'
import 'trix/dist/trix.css'

export default {
    name: 'trix-vue',
    props: ['name', 'value', 'placeholder'],
    methods: {
        initialize() {
            this.$refs.theEditor.editor.insertHTML(this.value)
        },

        handleChange() {
            this.$emit('change', this.$refs.theEditor.value)
        },

        handleAddFile(event) {
            this.$emit('file-add', event)
        },

        handleRemoveFile(event) {
            this.$emit('file-remove', event)
        },
    },
}
</script>
