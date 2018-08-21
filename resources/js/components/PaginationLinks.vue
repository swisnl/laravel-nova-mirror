<template>
    <div class="bg-20 rounded-b">
        <nav v-if="resources.length > 0" class="flex">
            <!-- Previous Link -->
            <button
                :disabled="!hasPreviousPages"
                class="btn btn-link py-3 px-4"
                :class="{
                    'text-primary dim': hasPreviousPages,
                    'text-80 opacity-50': !hasPreviousPages,
                }"
                rel="prev"
                @click.prevent="selectPreviousPage()"
                dusk="previous"
            >
                {{__('Previous')}}
            </button>

            <!-- Next Link -->
            <button
                :disabled="!hasMorePages"
                class="ml-auto btn btn-link py-3 px-4"
                :class="{
                    'text-primary dim': hasMorePages,
                    'text-80 opacity-50': !hasMorePages,
                }"
                rel="next"
                @click.prevent="selectNextPage()"
                dusk="next"
            >
                {{__('Next')}}
            </button>
        </nav>
    </div>
</template>

<script>
export default {
    props: ['resourceName', 'resources', 'resourceResponse'],

    methods: {
        /**
         * Select the previous page.
         */
        selectPreviousPage() {
            this.$emit('previous')
        },

        /**
         * Select the next page.
         */
        selectNextPage() {
            this.$emit('next')
        },
    },

    computed: {
        /**
         * Determine if prior pages are available.
         */
        hasPreviousPages: function() {
            return Boolean(this.resourceResponse && this.resourceResponse.prev_page_url)
        },

        /**
         * Determine if more pages are available.
         */
        hasMorePages: function() {
            return Boolean(this.resourceResponse && this.resourceResponse.next_page_url)
        },
    },
}
</script>
