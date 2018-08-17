<template>
    <div v-on-clickaway="close" class="dropdown relative">
        <slot :toggle="toggle" />

        <transition name="fade">
            <slot v-if="visible" name="menu" />
        </transition>
    </div>
</template>

<script>
import { mixin as clickaway } from 'vue-clickaway'

export default {
    mixins: [clickaway],

    data: () => ({ visible: false }),

    methods: {
        toggle() {
            this.visible = !this.visible
        },

        close() {
            this.visible = false
        },
    },
}
</script>
