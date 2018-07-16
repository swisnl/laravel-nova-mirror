<template>
    <default-field :field="field">
        <template slot="field">
            <textarea
                :data-testid="field.attribute"
                :dusk="field.attribute"
                type="text"
                v-model="value"
                class="w-full form-control form-input form-input-bordered py-3 min-h-textarea"
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
    mixins: [FormField, HandlesValidationErrors],

    props: {
        resourceName: {},
        field: {},
    },
}
</script>
