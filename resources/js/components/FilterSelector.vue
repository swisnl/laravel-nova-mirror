<template>
    <div>
        <filter-select v-for="filter in filters" :key="filter.name">
            <h3 slot="default" class="text-sm uppercase tracking-wide text-80 bg-30 p-3">
                {{ filter.name }}
            </h3>

            <select slot="select"
                    :dusk="filter.name + '-filter-select'"
                    class="block w-full form-control-sm form-select"
                    v-model="filter.currentValue"
                    @change="filterChanged(filter)"
            >
                <option value="" selected>&mdash;</option>

                <option v-for="option in filter.options" :value="option.value">
                    {{ option.name }}
                </option>
            </select>
        </filter-select>
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
