<template>
    <div v-if="!loading">
        <heading class="mb-3">{{__('Edit')}} {{ singularName }}</heading>

        <card class="overflow-hidden">
            <form v-if="fields" @submit.prevent="updateResource">
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
                    <button type="button" dusk="update-and-continue-editing-button" @click="updateAndContinueEditing" class="ml-auto btn btn-default btn-primary mr-3">
                        {{__('Update &amp; Continue Editing')}}
                    </button>

                    <button dusk="update-button" class="btn btn-default btn-primary">
                        {{__('Update')}} {{ singularName }}
                    </button>
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
    },

    data: () => ({
        loading: true,
        fields: [],
        validationErrors: new Errors(),
        lastRetrievedAt: null,
    }),

    created() {
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
            try {
                const response = await this.updateRequest()

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
                        resourceId: this.resourceId,
                    },
                })
            } catch (error) {
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
            try {
                const response = await this.updateRequest()

                this.$toasted.show(
                    this.__('The :resource was updated!', {
                        resource: this.resourceInformation.singularLabel.toLowerCase(),
                    }),
                    { type: 'success' }
                )

                // Reset the form by refetching the fields
                this.getFields()
                this.updateLastRetrievedAtTimestamp()
            } catch (error) {
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
            return this.resourceInformation.singularLabel
        },
    },
}
</script>
