<template>
    <div>
        <div v-if="expanded" class="markdown" v-html="content" />

        <a
            @click="toggle"
            class="cursor-pointer dim inline-block text-primary font-bold"
            :class="{ 'mt-6': expanded }"
            aria-role="button"
        >
            {{ showHideLabel }}
        </a>
    </div>
</template>

<script>
export default {
    props: {
        content: {
            type: String,
        },
    },

    data: () => ({ expanded: false }),

    methods: {
        toggle() {
            this.expanded = !this.expanded
        },
    },

    computed: {
        showHideLabel() {
            return !this.expanded ? 'Show Content' : 'Hide Content'
        },
    },
}
</script>
