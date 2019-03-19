<template>
    <field-wrapper>
        <div class="w-1/5 py-6 px-8">
            <slot>
                <form-label
                    :label-for="field.attribute"
                    :class="{ 'mb-2': showHelpText && field.helpText }"
                >
                    {{ fieldLabel }}
                </form-label>
            </slot>
        </div>
        <div class="py-6 px-8" :class="fieldClasses">
            <slot name="field" />

            <help-text class="error-text mt-2 text-danger" v-if="showErrors && hasError">
                {{ firstError }}
            </help-text>

            <help-text class="help-text mt-2" v-if="showHelpText"> {{ field.helpText }} </help-text>
        </div>
    </field-wrapper>
</template>

<script>
import { HandlesValidationErrors, Errors } from 'laravel-nova'

export default {
    mixins: [HandlesValidationErrors],

    props: {
        field: { type: Object, required: true },
        fieldName: { type: String },
        showHelpText: { type: Boolean, default: true },
        showErrors: { type: Boolean, default: true },
        fullWidthContent: { type: Boolean, default: false },
    },

    computed: {
        fieldLabel() {
            // If the field name is purposefully an empty string, then
            // let's show it as such
            if (this.fieldName === '') {
                return ''
            }

            return this.fieldName || this.field.singularLabel || this.field.name
        },

        fieldClasses() {
            return this.fullWidthContent ? 'w-4/5' : 'w-1/2'
        },
    },
}
</script>
