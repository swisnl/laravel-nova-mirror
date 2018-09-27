<template>
    <default-field :field="field">
        <template slot="field">
            <textarea
                class="w-full form-control form-input form-input-bordered py-3 h-auto"
                :id="field.attribute"
                :dusk="field.attribute"
                v-model="value"
                v-bind="extraAttributes"
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
    mixins: [FormField, HandlesValidationErrors],

    props: {
        resourceName: { type: String },
        field: {
            type: Object,
            required: true,
        },
    },

    computed: {
        defaultAttributes() {
            return {
                rows: this.field.rows,
                class: this.errorClasses,
            }
        },

        extraAttributes() {
            const attrs = this.field.extraAttributes

            return {
                ...this.defaultAttributes,
                ...attrs,
            }
        },
    },
}
</script>
