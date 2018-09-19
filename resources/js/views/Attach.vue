<template>
    <loading-view :loading="loading">
        <heading class="mb-3">{{__('Attach')}} {{ relatedResourceLabel }}</heading>

        <card class="overflow-hidden">
            <form v-if="field" @submit.prevent="attachResource">
                <!-- Related Resource -->
                <field-wrapper>
                    <div class="w-1/5 px-8 py-6">
                        <slot>
                            <form-label>
                                {{ relatedResourceLabel }}
                            </form-label>
                        </slot>
                    </div>
                    <div class="w-1/2 px-8 py-6">
                        <search-input
                            v-if="field.searchable"
                            :data-testid="`${field.resourceName}-search-input`"
                            @input="performSearch"
                            @clear="clearSelection"
                            @selected="selectResource"
                            :value='selectedResource'
                            :data='availableResources'
                            trackBy='value'
                            searchBy='display'
                            class="mb-3"
                        >
                            <div slot="default" v-if="selectedResource" class="flex items-center">
                                <div v-if="selectedResource.avatar" class="mr-3">
                                    <img :src="selectedResource.avatar" class="w-8 h-8 rounded-full block" />
                                </div>

                                {{ selectedResource.display }}
                            </div>

                            <div slot="option" slot-scope="{option, selected}" class="flex items-center">
                                <div v-if="option.avatar" class="mr-3">
                                    <img :src="option.avatar" class="w-8 h-8 rounded-full block" />
                                </div>

                                {{ option.display }}
                            </div>
                        </search-input>

                        <select
                            v-else
                            dusk="attachable-select"
                            class="form-control form-select mb-3 w-full"
                            :class="{ 'border-danger': validationErrors.has(field.attribute) }"
                            :data-testid="`${field.resourceName}-select`"
                            @change="selectResourceFromSelectControl"
                        >
                            <option value="" disabled selected>{{__('Choose')}} {{ field.name }}</option>

                            <option
                                v-for="resource in availableResources"
                                :key="resource.value"
                                :value="resource.value"
                                :selected="selectedResourceId == resource.value"
                            >
                                {{ resource.display}}
                            </option>
                        </select>

                        <!-- Trashed State -->
                        <div v-if="softDeletes">
                            <label class="flex items-center" @input="toggleWithTrashed" @keydown.prevent.space.enter="toggleWithTrashed">
                                <checkbox :dusk="field.resourceName + '-with-trashed-checkbox'" :checked="withTrashed" />

                                <span class="ml-2">
                                    {{__('With Trashed')}}
                                </span>
                            </label>
                        </div>

                        <p v-if="true" class="my-2 text-danger">
                            {{ validationErrors.first(relatedResourceName) }}
                        </p>
                    </div>
                </field-wrapper>

                <!-- Pivot Fields -->
                <div v-for="field in fields">
                    <component
                        :is="'form-' + field.component"
                        :resource-name="resourceName"
                        :field="field"
                        :errors="validationErrors"
                        :via-resource="viaResource"
                        :via-resource-id="viaResourceId"
                        :via-relationship="viaRelationship"
                    />
                </div>

                <!-- Attach Button -->
                <div class="bg-30 flex px-8 py-4">
                    <button dusk="attach-and-attach-another-button" type="button" @click="attachAndAttachAnother" class="ml-auto btn btn-default btn-primary mr-3">
                        {{__('Attach &amp; Attach Another')}}
                    </button>

                    <button dusk="attach-button" class="btn btn-default btn-primary">
                        {{__('Attach')}} {{ relatedResourceLabel }}
                    </button>
                </div>
            </form>
        </card>
    </loading-view>
</template>

<script>
import { PerformsSearches, TogglesTrashed, Errors } from 'laravel-nova'

export default {
    mixins: [PerformsSearches, TogglesTrashed],

    props: {
        resourceName: {
            type: String,
            required: true,
        },
        resourceId: {
            required: true,
        },
        relatedResourceName: {
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
        polymorphic: {
            default: false,
        },
    },

    data: () => ({
        loading: true,
        field: null,
        softDeletes: false,
        fields: [],
        validationErrors: new Errors(),
        selectedResource: null,
        selectedResourceId: null,
    }),

    /**
     * Mount the component.
     */
    mounted() {
        this.initializeComponent()
    },

    methods: {
        /**
         * Initialize the component's data.
         */
        initializeComponent() {
            this.softDeletes = false
            this.disableWithTrashed()
            this.clearSelection()
            this.getField()
            this.getPivotFields()
            this.resetErrors()
        },

        /**
         * Get the many-to-many relationship field.
         */
        getField() {
            this.field = null

            Nova.request()
                .get('/nova-api/' + this.resourceName + '/field/' + this.viaRelationship)
                .then(({ data }) => {
                    this.field = data
                    this.field.searchable
                        ? this.determineIfSoftDeletes()
                        : this.getAvailableResources()
                    this.loading = false
                })
        },

        /**
         * Get all of the available pivot fields for the relationship.
         */
        getPivotFields() {
            this.fields = []

            Nova.request()
                .get(
                    '/nova-api/' +
                        this.resourceName +
                        '/creation-pivot-fields/' +
                        this.relatedResourceName
                )
                .then(({ data }) => {
                    this.fields = data

                    _.each(this.fields, field => {
                        field.fill = () => ''
                    })
                })
        },

        resetErrors() {
            this.validationErrors = new Errors()
        },

        /**
         * Get all of the available resources for the current search / trashed state.
         */
        getAvailableResources(search = '') {
            Nova.request()
                .get(
                    `/nova-api/${this.resourceName}/${this.resourceId}/attachable/${
                        this.relatedResourceName
                    }`,
                    {
                        params: {
                            search,
                            current: this.selectedResourceId,
                            withTrashed: this.withTrashed,
                        },
                    }
                )
                .then(response => {
                    this.availableResources = response.data.resources
                    this.withTrashed = response.data.withTrashed
                    this.softDeletes = response.data.softDeletes
                })
        },

        /**
         * Determine if the related resource is soft deleting.
         */
        determineIfSoftDeletes() {
            Nova.request()
                .get('/nova-api/' + this.relatedResourceName + '/soft-deletes')
                .then(response => {
                    this.softDeletes = response.data.softDeletes
                })
        },

        /**
         * Attach the selected resource.
         */
        async attachResource() {
            try {
                await this.attachRequest()

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
            }
        },

        /**
         * Attach a new resource and reset the form
         */
        async attachAndAttachAnother() {
            try {
                await this.attachRequest()

                // Reset the form by refetching the fields
                this.initializeComponent()
            } catch (error) {
                if (error.response.status == 422) {
                    this.validationErrors = new Errors(error.response.data.errors)
                }
            }
        },

        /**
         * Send an attach request for this resource
         */
        attachRequest() {
            return Nova.request().post(this.attachmentEndpoint, this.attachmentFormData)
        },

        /**
         * Select a resource using the <select> control
         */
        selectResourceFromSelectControl(e) {
            this.selectedResourceId = e.target.value
            this.selectInitialResource()
        },

        /**
         * Select the initial selected resource
         */
        selectInitialResource() {
            this.selectedResource = _.find(
                this.availableResources,
                r => r.value == this.selectedResourceId
            )
        },

        /**
         * Toggle the trashed state of the search
         */
        toggleWithTrashed() {
            this.withTrashed = !this.withTrashed

            // Reload the data if the component doesn't support searching
            if (!this.isSearchable) {
                this.getAvailableResources()
            }
        },
    },

    computed: {
        /**
         * Get the attachment endpoint for the relationship type.
         */
        attachmentEndpoint() {
            return this.polymorphic
                ? '/nova-api/' +
                      this.resourceName +
                      '/' +
                      this.resourceId +
                      '/attach-morphed/' +
                      this.relatedResourceName
                : '/nova-api/' +
                      this.resourceName +
                      '/' +
                      this.resourceId +
                      '/attach/' +
                      this.relatedResourceName
        },

        /**
         * Get the form data for the resource attachment.
         */
        attachmentFormData() {
            return _.tap(new FormData(), formData => {
                _.each(this.fields, field => {
                    field.fill(formData)
                })

                if (!this.selectedResource) {
                    formData.append(this.relatedResourceName, '')
                } else {
                    formData.append(this.relatedResourceName, this.selectedResource.value)
                }

                formData.append(this.relatedResourceName + '_trashed', this.withTrashed)
                formData.append('viaRelationship', this.viaRelationship)
            })
        },

        /**
         * Get the label for the related resource.
         */
        relatedResourceLabel() {
            return _.find(Nova.config.resources, resource => {
                return resource.uriKey == this.relatedResourceName
            }).singularLabel
        },

        /**
         * Determine if the related resources is searchable
         */
        isSearchable() {
            return this.field.searchable
        },
    },
}
</script>
