<template>
    <default-field :field="field">
        <template slot="field">
            <date-time-picker
                class="w-full form-control form-input form-input-bordered"
                :field="field"
                :name="field.name"
                :value="value"
                dateFormat="Y-m-d"
                :placeholder="placeholder"
                :enable-time="false"
                :enable-seconds="false"
                @change="handleChange"
            />

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

    computed: {
        placeholder() {
            return moment().format('YYYY-MM-DD')
        },
    },
}
</script>
