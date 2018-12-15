<template>
    <div>
        <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">
            {{ filter.name }}
        </h3>

        <div class="p-2">
            <select-control
                :dusk="filter.name + '-filter-select'"
                class="block w-full form-control-sm form-select"
                @change="handleChange"
                :options="filter.options"
                :label="'name'"
                :selected="value"
            >
                <option value="" selected>&mdash;</option>
            </select-control>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        filterKey: {
            type: String,
            required: true,
        },
    },

    methods: {
        handleChange(event) {
            this.$store.commit('updateFilterState', {
                filterClass: this.filterKey,
                value: event.target.value,
            })

            this.$emit('change')
        },
    },

    computed: {
        filter() {
            return this.$store.getters.getFilter(this.filterKey)
        },

        value() {
            return this.filter.currentValue
        },
    },
}
</script>
