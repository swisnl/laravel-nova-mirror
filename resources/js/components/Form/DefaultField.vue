<template>
    <field-wrapper>
        <div class="w-1/5 px-8 py-6">
            <slot>
                <form-label :for="field.name">
                    {{ field.name || fieldName }}
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
}
</script>
