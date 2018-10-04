<template>
    <div>
        <component
            v-for="filter in filters"
            :key="filter.name"
            :is="filter.component"
            :filter="filter"
            v-model="filter.currentValue"
            @change="filterChanged(filter)"
        />
    </div>
</template>

<script>
export default {
    props: ['filters', 'currentFilters'],

    /**
     * Mount the component.
     */
    mounted() {
        this.current = this.currentFilters
    },

    methods: {
        /**
         * Handle a filter selection change.
         */
        filterChanged(filter) {
            let newCurrent = _.reject(this.currentFilters, f => f.class == filter.class)

            if (filter.currentValue !== '') {
                newCurrent.push({
                    class: filter.class,
                    value: filter.currentValue,
                })
            }

            // Broadcast the new filter selections to the parent component
            this.$emit('changed', newCurrent)
        },
    },
}
</script>
