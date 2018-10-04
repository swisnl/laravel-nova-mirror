<template>
    <div>
        <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">
            {{ filter.name }}
        </h3>

        <div class="p-2">
            <select
                :dusk="filter.name + '-filter-select'"
                class="block w-full form-control-sm form-select"
                :value="value"
                @change="filterChanged"
            >
                <option value="" selected>&mdash;</option>

                <option v-for="option in filter.options" :value="option.value">
                    {{ option.name }}
                </option>
            </select>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        filter: {
            type: Object,
            required: true,
        },

        value: {
            type: [String, Number],
            required: true,
        },
    },

    methods: {
        filterChanged(event) {
            // this.$emit('input', event.target.value)
            // this.$emit('change')
        },
    },

    computed: {
        decodedFilters() {
            let encodedFilters = $route.query['users_filter']
            let currentFilters = JSON.parse(atob(this.encodedFilters))

            console.log('current:', currentFilters)
        },
    },
}
</script>
