<template>
    <default-field :field="field">
        <template slot="field">
            <input
                :id="field.name"
                :dusk="field.attribute"
                :type="inputType"
                :min="inputMin"
                :max="inputMax"
                :step="inputStep"
                :pattern="inputPattern"
                v-model="value"
                class="w-full form-control form-input form-input-bordered"
                :class="errorClasses"
                :placeholder="field.name"
            />

            <p v-if="hasError" class="my-2 text-danger">
                {{ firstError }}
            </p>
        </template>
    </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'

export default {
    mixins: [HandlesValidationErrors, FormField],

    computed: {
        /**
         * Get the input type.
         */
        inputType() {
            return this.field.type || 'text'
        },

        /**
         * Get the input step amount.
         */
        inputStep() {
            return this.field.step
        },

        /**
         * Get the input minimum amount.
         */
        inputMin() {
            return this.field.min
        },

        /**
         * Get the input maximum amount.
         */
        inputMax() {
            return this.field.max
        },

        /**
         * Get the pattern that should be used for the field
         */
        inputPattern() {
            return this.field.pattern
        },
    },
}
</script>
