<template>
    <loading-view :loading="loading">
        <heading class="mb-6">{{ __('New') }} {{ singularName }}</heading>

        <form v-if="fields" @submit.prevent="createResource" autocomplete="off">
            <component
                    v-for="panel in availablePanels"
                    :key="panel.name"
                    :is="panel.component"
                    :panel="panel"
                    class="mb-6"
            >
                <heading class="mb-3" :level="2">{{ panel.name }}</heading>
                <template slot="panel" slot-scope="{ fields }">
                    <component
                        v-for="field in fields"
                        :key="field.attribute"
                        :is="'form-' + field.component"
                        :errors="validationErrors"
                        :resource-name="resourceName"
                        :field="field"
                        :via-resource="viaResource"
                        :via-resource-id="viaResourceId"
                        :via-relationship="viaRelationship"
                    />
                </template>
            </component>
            <div class="rounded-lg bg-30 flex px-8 py-4">
                <progress-button
                    class="ml-auto mr-3"
                    dusk="create-and-add-another-button"
                    @click.native="createAndAddAnother"
                    :disabled="isWorking"
                    :processing="submittedViaCreateAndAddAnother"
                >
                    {{ __('Create & Add Another') }}
                </progress-button>

                <progress-button
                    dusk="create-button"
                    type="submit"
                    :disabled="isWorking"
                    :processing="submittedViaCreateResource"
                >
                    {{ __('Create') }} {{ singularName }}
                </progress-button>
            </div>
        </form>
    </loading-view>
</template>

<script>
import { Errors, Minimum, InteractsWithResourceInformation } from 'laravel-nova'
import InteractWithPanels from '@/util/InteractWithPanels'

export default {
    mixins: [InteractsWithResourceInformation, InteractWithPanels],

    props: {
        resourceName: {
            type: String,
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
        submittedViaCreateAndAddAnother: false,
        submittedViaCreateResource: false,
        fields: [],
        validationErrors: new Errors(),
    }),

    async created() {
        if (Nova.missingResource(this.resourceName)) return this.$router.push({ name: '404' })

        // If this create is via a relation index, then let's grab the field
        // and use the label for that as the one we use for the title and buttons
        if (this.isRelation) {
            const { data } = await Nova.request(
                '/nova-api/' + this.viaResource + '/field/' + this.viaRelationship
            )
            this.relationResponse = data
        }

        this.getFields()
    },

    methods: {
        /**
         * Get the available fields for the resource.
         */
        async getFields() {
            this.fields = []

            const { data: { fields, panels } } = await Nova.request().get(
                `/nova-api/${this.resourceName}/creation-fields`,
                {
                    params: {
                        viaResource: this.viaResource,
                        viaResourceId: this.viaResourceId,
                        viaRelationship: this.viaRelationship,
                    },
                }
            )

            this.panels = panels
            this.fields = fields
            this.loading = false
        },

        /**
         * Create a new resource instance using the provided data.
         */
        async createResource() {
            this.submittedViaCreateResource = true

            try {
                const response = await this.createRequest()

                this.submittedViaCreateResource = false

                this.$toasted.show(
                    this.__('The :resource was created!', {
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
                this.submittedViaCreateResource = false

                if (error.response.status == 422) {
                    this.validationErrors = new Errors(error.response.data.errors)
                }
            }
        },

        /**
         * Create a new resource and reset the form
         */
        async createAndAddAnother() {
            this.submittedViaCreateAndAddAnother = true

            try {
                const response = await this.createRequest()

                this.submittedViaCreateAndAddAnother = false

                this.$toasted.show(
                    this.__('The :resource was created!', {
                        resource: this.resourceInformation.singularLabel.toLowerCase(),
                    }),
                    { type: 'success' }
                )

                // Reset the form by refetching the fields
                this.getFields()

                this.validationErrors = new Errors()
            } catch (error) {
                this.submittedViaCreateAndAddAnother = false

                if (error.response.status == 422) {
                    this.validationErrors = new Errors(error.response.data.errors)
                }
            }
        },

        /**
         * Send a create request for this resource
         */
        createRequest() {
            return Nova.request().post(
                `/nova-api/${this.resourceName}`,
                this.createResourceFormData()
            )
        },

        /**
         * Create the form data for creating the resource.
         */
        createResourceFormData() {
            return _.tap(new FormData(), formData => {
                _.each(this.fields, field => {
                    field.fill(formData)
                })

                formData.append('viaResource', this.viaResource)
                formData.append('viaResourceId', this.viaResourceId)
                formData.append('viaRelationship', this.viaRelationship)
            })
        },

        panelFields(panel) {
            return this.fields.filter(field => field.panel == panel)
        },

        nonPanelFields() {
            return this.fields.filter(field => field.panel == null)
        },
    },

    computed: {
        // panels() {
        //     let panels =  this.fields
        //         .map(field => field.panel)
        //         .filter(panel => panel !== null)
        //         .filter((panel, key, collection) => collection.indexOf(panel) === key)
        //         .map((panel) => ({
        //             name: panel,
        //             fields: this.fields.filter(field => field.panel === panel),
        //         }))
        //
        //     let first = this.fields.filter(field => field.panel === null)
        //
        //     if (first.length > 0) panels.splice(0, 0, {name: null, fields: first})
        //
        //     return panels;
        // },

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
            return this.submittedViaCreateResource || this.submittedViaCreateAndAddAnother
        },
    },
}
</script>
