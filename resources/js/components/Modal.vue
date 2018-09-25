<template>
    <div class="modal select-none fixed pin z-50 overflow-x-hidden overflow-y-auto">
        <div class="relative mx-auto flex justify-center z-20 py-view">
            <div v-on-clickaway="close">
                <slot />
            </div>
        </div>
        <portal to="modal-background">
            <div class="absolute pin bg-80 z-20 opacity-75" />
        </portal>
    </div>
</template>

<script>
import { mixin as clickaway } from 'vue-clickaway'

export default {
    mixins: [clickaway],

    created() {
        document.addEventListener('keydown', this.handleEscape)
    },

    destroyed() {
        document.removeEventListener('keydown', this.handleEscape)
    },

    methods: {
        handleEscape(e) {
            e.stopPropagation()

            if (e.keyCode == 27) {
                this.close()
            }
        },

        close() {
            this.$emit('modal-close')
        },
    },
}
</script>
