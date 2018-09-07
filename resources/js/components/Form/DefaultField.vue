<template>
    <field-wrapper>
        <div class="w-1/5 px-8 py-6">
            <slot>
                <form-label :for="field.name" :class="{
                    'mb-2': field.helpText && showHelpText
                }">
                    {{ fieldLabel }}
                </form-label>

                <help-text :show-help-text="showHelpText">
                    {{ field.helpText }}
                </help-text>
            </slot>
        </div>
        <div class="w-1/2 px-8 py-6">
            <slot name="field"/>
        </div>
    </field-wrapper>
</template>

<script>
export default {
    props: {
        field: { type: Object, required: true },
        fieldName: { type: String },
        showHelpText: { type: Boolean, default: true },
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
    },
}
</script>
