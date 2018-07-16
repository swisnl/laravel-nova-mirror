<template>
    <default-field :field="field">
        <template slot="field">
            <div class="flex items-center">
                <date-time-picker
                    class="w-full form-control form-input form-input-bordered"
                    :field="field"
                    :value="localizedValue"
                    :twelve-hour-time="usesTwelveHourTime"
                    @change="handleChange"
                />

                <span class="text-80 text-sm ml-2">({{ userTimezone }})</span>
            </div>

            <p v-if="hasError" class="my-2 text-danger">
                {{ firstError }}
            </p>
        </template>
    </default-field>
</template>

<script>
import DateTimePicker from '../DateTimePicker'
import { Errors, FormField, HandlesValidationErrors, InteractsWithDates } from 'laravel-nova'

export default {
    mixins: [HandlesValidationErrors, FormField, InteractsWithDates],

    components: { DateTimePicker },

    data: () => ({ localizedValue: '' }),

    methods: {
        /*
         * Set the initial value for the field
         */
        setInitialValue() {
            // Set the initial value of the field
            this.value = this.field.value || ''

            // If the field has a value let's convert it from the app's timezone
            // into the user's local time to display in the field
            if (this.value !== '') {
                this.localizedValue = this.fromAppTimezone(this.value)
            }
        },

        /**
         * Update the field's internal value when it's value changes
         */
        handleChange(value) {
            this.value = this.toAppTimezone(value)
        },
    },
}
</script>
