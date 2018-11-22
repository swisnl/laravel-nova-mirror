<template>
    <div>
        <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">{{ filter.name }}</h3>

        <div class="p-2">
            <date-time-picker
                class="w-full form-control form-input form-input-bordered"
                dusk="date-filter"
                name="date-filter"
                :value="value"
                dateFormat="Y-m-d"
                :placeholder="placeholder"
                :enable-time="false"
                :enable-seconds="false"
                :first-day-of-week="firstDayOfWeek"
                @input.prevent=""
                @change="handleChange"
            />
        </div>
    </div>
</template>

<script>
import DateTimePicker from '../DateTimePicker'

export default {
    components: { DateTimePicker },

    props: {
        filterKey: {
            type: String,
            required: true,
        },
    },

    methods: {
        handleChange(value) {
            this.$store.commit('updateFilterState', { filterClass: this.filterKey, value })
            this.$emit('change')
        },
    },

    computed: {
        placeholder() {
            return this.__('Choose date')
        },

        value() {
            return this.filter.currentValue
        },

        filter() {
            return this.$store.getters.getFilter(this.filterKey)
        },

        options() {
            return this.$store.getters.getOptionsForFilter(this.filterKey)
        },

        firstDayOfWeek() {
            const option = _.find(this.filter.options, option => option.name === 'firstDayOfWeek')

            return option !== undefined ? option.value : 0
        },
    },
}
</script>
