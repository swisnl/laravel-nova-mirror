<template>
    <div v-if="!loading">
        <heading class="mb-3">{{ __('Edit') }} {{ singularName }}</heading>

        <card class="overflow-hidden">
            <form v-if="fields" @submit.prevent="updateResource" autocomplete="off">
                <!-- Validation Errors -->
                <validation-errors :errors="validationErrors" />

                <!-- Fields -->
                <div v-for="field in fields">
                    <component
                        @file-deleted="updateLastRetrievedAtTimestamp"
                        :is="'form-' + field.component"
                        :errors="validationErrors"
                        :resource-id="resourceId"
                        :resource-name="resourceName"
                        :field="field"
                    />
                </div>

                <!-- Update Button -->
                <div class="bg-30 flex px-8 py-4">
                    <progress-button
                        class="ml-auto mr-3"
                        dusk="update-and-continue-editing-button"
                        @click.native="updateAndContinueEditing"
                        :disabled="isWorking"
                        :processing="submittedViaUpdateAndContinueEditing"
                    >
                        {{ __('Update & Continue Editing') }}
                    </progress-button>

                    <progress-button
                        dusk="update-button"
                        type="submit"
                        :disabled="isWorking"
                        :processing="submittedViaUpdateResource"
                    >
                        {{ __('Update') }} {{ singularName }}
                    </progress-button>
                </div>
            </form>
        </card>
    </div>
</template>

<script>
import { Errors, InteractsWithResourceInformation } from 'laravel-nova'

export default {
    mixins: [InteractsWithResourceInformation],

    props: {
        resourceName: {
            type: String,
            required: true,
        },
        resourceId: {
            required: true,
        },
        viaResource: {
            default: '',
        },
        viaResourceId: {
            default: '',
        },
        viaRelationship: {
            default: '',
        },
    },

    data: () => ({
        relationResponse: null,
        loading: true,
        submittedViaUpdateAndContinueEditing: false,
        submittedViaUpdateResource: false,
        fields: [],
        validationErrors: new Errors(),
        lastRetrievedAt: null,
    }),

    async created() {
        if (Nova.missingResource(this.resourceName)) return this.$router.push({ name: '404' })

        // If this update is via a relation index, then let's grab the field
        // and use the label for that as the one we use for the title and buttons
        if (this.isRelation) {
            const { data } = await Nova.request(
                '/nova-api/' + this.viaResource + '/field/' + this.viaRelationship
            )
            this.relationResponse = data
        }

        this.getFields()

        this.updateLastRetrievedAtTimestamp()
    },

    methods: {
        /**
         * Get the available fields for the resource.
         */
        async getFields() {
            this.loading = true

            this.fields = []

            const { data: fields } = await Nova.request()
                .get(`/nova-api/${this.resourceName}/${this.resourceId}/update-fields`)
                .catch(error => {
                    if (error.response.status == 404) {
                        this.$router.push({ name: '404' })
                        return
                    }
                })

            this.fields = fields

            this.loading = false
        },

        /**
         * Update the resource using the provided data.
         */
        async updateResource() {
            this.submittedViaUpdateResource = true

            try {
                const response = await this.updateRequest()

                this.submittedViaUpdateResource = false

                this.$toasted.show(
                    this.__('The :resource was updated!', {
                        resource: this.resourceInformation.singularLabel.toLowerCase(),
                    }),
                    { type: 'success' }
                )

                this.$router.push({
                    name: 'detail',
                    params: {
                        resourceName: this.resourceName,
                        resourceId: response.data.id,
                    },
                })
            } catch (error) {
                this.submittedViaUpdateResource = false

                if (error.response.status == 422) {
                    this.validationErrors = new Errors(error.response.data.errors)
                }

                if (error.response.status == 409) {
                    this.$toasted.show(
                        this.__(
                            'Another user has updated this resource since this page was loaded. Please refresh the page and try again.'
                        ),
                        { type: 'error' }
                    )
                }
            }
        },

        /**
         * Update the resource and reset the form
         */
        async updateAndContinueEditing() {
            this.submittedViaUpdateAndContinueEditing = true

            try {
                const response = await this.updateRequest()

                this.submittedViaUpdateAndContinueEditing = false

                this.$toasted.show(
                    this.__('The :resource was updated!', {
                        resource: this.resourceInformation.singularLabel.toLowerCase(),
                    }),
                    { type: 'success' }
                )

                // Reset the form by refetching the fields
                this.getFields()

                this.validationErrors = new Errors()

                this.updateLastRetrievedAtTimestamp()
            } catch (error) {
                this.submittedViaUpdateAndContinueEditing = false

                if (error.response.status == 422) {
                    this.validationErrors = new Errors(error.response.data.errors)
                }

                if (error.response.status == 409) {
                    this.$toasted.show(
                        this.__(
                            'Another user has updated this resource since this page was loaded. Please refresh the page and try again.'
                        ),
                        { type: 'error' }
                    )
                }
            }
        },

        /**
         * Send an update request for this resource
         */
        updateRequest() {
            return Nova.request().post(
                `/nova-api/${this.resourceName}/${this.resourceId}`,
                this.updateResourceFormData
            )
        },

        /**
         * Update the last retrieved at timestamp to the current UNIX timestamp.
         */
        updateLastRetrievedAtTimestamp() {
            this.lastRetrievedAt = Math.floor(new Date().getTime() / 1000)
        },
    },

    computed: {
        /**
         * Create the form data for creating the resource.
         */
        updateResourceFormData() {
            return _.tap(new FormData(), formData => {
                _(this.fields).each(field => {
                    field.fill(formData)
                })

                formData.append('_method', 'PUT')
                formData.append('_retrieved_at', this.lastRetrievedAt)
            })
        },

        singularName() {
            if (this.relationResponse) {
                return this.relationResponse.singularLabel
            }

            return this.resourceInformation.singularLabel
        },

        isRelation() {
            return Boolean(this.viaResourceId && this.viaRelationship)
        },

        /**
         * Determine if the form is being processed
         */
        isWorking() {
            return this.submittedViaUpdateResource || this.submittedViaUpdateAndContinueEditing
        },
    },
}
</script>
