<template>
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
            this.$emit('input', event.target.value)
            this.$emit('change')
        },
    },
}
</script>
