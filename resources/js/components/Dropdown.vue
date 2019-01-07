<template>
    <div v-on-clickaway="close" class="dropdown relative">
        <slot :toggle="toggle" />

        <transition name="fade"> <slot v-if="visible" name="menu" /> </transition>
    </div>
</template>

<script>
import { mixin as clickaway } from 'vue-clickaway'
import composedPath from '@/polyfills/composedPath'

export default {
    props: {
        classWhitelist: [Array, String],
    },

    mixins: [clickaway],

    data: () => ({ visible: false }),

    methods: {
        toggle() {
            this.visible = !this.visible
        },

        close(event) {
            let classArray = Array.isArray(this.classWhitelist)
                ? this.classWhitelist
                : [this.classWhitelist]

            if (_.filter(classArray, className => pathIncludesClass(event, className)).length > 0) {
                return
            }

            this.visible = false
        },
    },
}

function pathIncludesClass(event, className) {
    return composedPath(event)
        .filter(el => el !== document && el !== window)
        .reduce((acc, e) => acc.concat([...e.classList]), [])
        .includes(className)
}
</script>
