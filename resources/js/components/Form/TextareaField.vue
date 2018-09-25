<template>
    <default-field :field="field" :errors="errors" :full-width-content="true">
        <template slot="field">
            <textarea
                :data-testid="field.attribute"
                :dusk="field.attribute"
                type="text"
                v-model="value"
                class="w-full form-control form-input form-input-bordered py-3 min-h-textarea"
                :class="errorClasses"
            />
        </template>
    </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: {
        resourceName: {},
        field: {},
    },
}
</script>
