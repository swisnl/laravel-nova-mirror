<template>
    <div>
        <default-field :field="field" :show-errors="false" :field-name="fieldName">
            <select
                :disabled="isLocked"
                :data-testid="`${field.attribute}-type`"
                :dusk="`${field.attribute}-type`"
                slot="field"
                :value="resourceType"
                @change="refreshResourcesForTypeChange"
                class="block w-full form-control form-input form-input-bordered form-select mb-3"
            >
                <option value="" selected :disabled="!field.nullable">
                    {{ __('Choose Type') }}
                </option>

                <option
                    v-for="option in field.morphToTypes"
                    :key="option.value"
                    :value="option.value"
                    :selected="resourceType == option.value"
                >
                    {{ option.singularLabel }}
                </option>
            </select>
        </default-field>

        <default-field
            :field="field"
            :errors="errors"
            :show-help-text="false"
            :field-name="fieldTypeName"
        >
            <template slot="field">
                <search-input
                    v-if="isSearchable && !isLocked"
                    :data-testid="`${field.attribute}-search-input`"
                    @input="performSearch"
                    @clear="clearSelection"
                    @selected="selectResource"
                    :value="selectedResource"
                    :data="availableResources"
                    trackBy="value"
                    searchBy="display"
                    class="mb-3"
                >
                    <div slot="default" v-if="selectedResource" class="flex items-center">
                        <div v-if="selectedResource.avatar" class="mr-3">
                            <img
                                :src="selectedResource.avatar"
                                class="w-8 h-8 rounded-full block"
                            />
                        </div>

                        {{ selectedResource.display }}
                    </div>

                    <div slot="option" slot-scope="{ option, selected }" class="flex items-center">
                        <div v-if="option.avatar" class="mr-3">
                            <img :src="option.avatar" class="w-8 h-8 rounded-full block" />
                        </div>

                        {{ option.display }}
                    </div>
                </search-input>

                <select
                    v-if="!isSearchable || isLocked"
                    :data-testid="`${field.attribute}-select`"
                    :dusk="`${field.attribute}-select`"
                    class="form-control form-select mb-3 w-full"
                    :class="{ 'border-danger': hasError }"
                    :disabled="!resourceType || isLocked"
                    @change="selectResourceFromSelectControl"
                >
                    <option
                        value=""
                        :disabled="!field.nullable"
                        :selected="selectedResourceId == ''"
                    >
                        {{ __('Choose') }} {{ fieldTypeName }}
                    </option>

                    <option
                        v-for="resource in availableResources"
                        :key="resource.value"
                        :value="resource.value"
                        :selected="selectedResourceId == resource.value"
                    >
                        {{ resource.display }}
                    </option>
                </select>

                <!-- Trashed State -->
                <div v-if="softDeletes && !isLocked">
                    <checkbox-with-label
                        :dusk="field.attribute + '-with-trashed-checkbox'"
                        :checked="withTrashed"
                        @change="toggleWithTrashed"
                    >
                        {{ __('With Trashed') }}
                    </checkbox-with-label>
                </div>
            </template>
        </default-field>
    </div>
</template>

<script>
import _ from 'lodash'
import storage from '@/storage/MorphToFieldStorage'
import { PerformsSearches, TogglesTrashed, HandlesValidationErrors } from 'laravel-nova'

export default {
    mixins: [PerformsSearches, TogglesTrashed, HandlesValidationErrors],
    props: ['resourceName', 'field', 'viaResource', 'viaResourceId', 'viaRelationship'],

    data: () => ({
        resourceType: '',
        initializingWithExistingResource: false,
        softDeletes: false,
        selectedResourceId: null,
        selectedResource: null,
        search: '',
    }),

    /**
     * Mount the component.
     */
    mounted() {
        if (this.editingExistingResource) {
            this.initializingWithExistingResource = true
            this.resourceType = this.field.morphToType
            this.selectedResourceId = this.field.morphToId
        }

        if (this.creatingViaRelatedResource) {
            this.initializingWithExistingResource = true
            this.resourceType = this.viaResource
            this.selectedResourceId = this.viaResourceId
        }

        if (this.shouldSelectInitialResource && !this.isSearchable) {
            this.getAvailableResources().then(() => this.selectInitialResource())
        } else if (this.shouldSelectInitialResource && this.isSearchable) {
            this.getAvailableResources().then(() => this.selectInitialResource())
        }

        if (this.resourceType) {
            this.determineIfSoftDeletes()
        }

        this.field.fill = this.fill
    },

    methods: {
        /**
         * Select a resource using the <select> control
         */
        selectResourceFromSelectControl(e) {
            this.selectedResourceId = e.target.value
            this.selectInitialResource()
        },

        /**
         * Fill the forms formData with details from this field
         */
        fill(formData) {
            if (this.selectedResource && this.resourceType) {
                formData.append(this.field.attribute, this.selectedResource.value)
                formData.append(this.field.attribute + '_type', this.resourceType)
            } else {
                formData.append(this.field.attribute, '')
                formData.append(this.field.attribute + '_type', '')
            }

            formData.append(this.field.attribute + '_trashed', this.withTrashed)
        },

        /**
         * Get the resources that may be related to this resource.
         */
        getAvailableResources(search = '') {
            return storage
                .fetchAvailableResources(this.resourceName, this.field.attribute, this.queryParams)
                .then(({ data: { resources, softDeletes, withTrashed } }) => {
                    if (this.initializingWithExistingResource || !this.isSearchable) {
                        this.withTrashed = withTrashed
                    }

                    this.initializingWithExistingResource = false
                    this.availableResources = resources
                    this.softDeletes = softDeletes
                })
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
         * Determine if the selected resource type is soft deleting.
         */
        determineIfSoftDeletes() {
            return storage
                .determineIfSoftDeletes(this.resourceType)
                .then(({ data: { softDeletes } }) => (this.softDeletes = softDeletes))
        },

        /**
         * Handle the changing of the resource type.
         */
        async refreshResourcesForTypeChange(event) {
            this.resourceType = event.target.value
            this.availableResources = []
            this.selectedResource = ''
            this.selectedResourceId = ''
            this.withTrashed = false

            // if (this.resourceType == '') {
            this.softDeletes = false
            // } else if (this.field.searchable) {
            this.determineIfSoftDeletes()
            // }

            if (!this.isSearchable && this.resourceType) {
                this.getAvailableResources()
            }
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
         * Determine if an existing resource is being updated.
         */
        editingExistingResource() {
            return Boolean(this.field.morphToId && this.field.morphToType)
        },

        /**
         * Determine if we are creating a new resource via a parent relation
         */
        creatingViaRelatedResource() {
            return Boolean(this.viaResource && this.viaResourceId)
        },

        /**
         * Determine if we should select an initial resource when mounting this field
         */
        shouldSelectInitialResource() {
            return Boolean(this.editingExistingResource || this.creatingViaRelatedResource)
        },

        /**
         * Determine if the related resources is searchable
         */
        isSearchable() {
            return Boolean(this.field.searchable)
        },

        shouldLoadFirstResource() {
            return (
                this.isSearchable &&
                this.shouldSelectInitialResource &&
                this.initializingWithExistingResource
            )
        },

        /**
         * Get the query params for getting available resources
         */
        queryParams() {
            return {
                params: {
                    type: this.resourceType,
                    current: this.selectedResourceId,
                    first: this.shouldLoadFirstResource,
                    search: this.search,
                    withTrashed: this.withTrashed,
                },
            }
        },

        /**
         * Determine if the field is locked
         */
        isLocked() {
            return Boolean(this.viaResource)
        },

        /**
         * Return the morphable type label for the field
         */
        fieldName() {
            return this.field.name
        },

        /**
         * Return the selected morphable type's label
         */
        fieldTypeName() {
            if (this.resourceType) {
                return _.find(this.field.morphToTypes, type => {
                    return type.value == this.resourceType
                }).singularLabel
            }

            return ''
        },
    },
}
</script>
