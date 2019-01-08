<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
            <search-input
                v-if="isSearchable && !isLocked"
                :data-testid="`${field.resourceName}-search-input`"
                @input="performSearch"
                @clear="clearSelection"
                @selected="selectResource"
                :error="hasError"
                :value="selectedResource"
                :data="availableResources"
                trackBy="value"
                searchBy="display"
                class="mb-3"
            >
                <div slot="default" v-if="selectedResource" class="flex items-center">
                    <div v-if="selectedResource.avatar" class="mr-3">
                        <img :src="selectedResource.avatar" class="w-8 h-8 rounded-full block" />
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
                class="form-control form-select mb-3 w-full"
                :class="{ 'border-danger': hasError }"
                :data-testid="`${field.resourceName}-select`"
                :dusk="field.attribute"
                @change="selectResourceFromSelectControl"
                :disabled="isLocked"
            >
                <option value="" selected :disabled="!field.nullable">&mdash;</option>

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
                    :dusk="`${field.resourceName}-with-trashed-checkbox`"
                    :checked="withTrashed"
                    @change="toggleWithTrashed"
                >
                    {{ __('With Trashed') }}
                </checkbox-with-label>
            </div>
        </template>
    </default-field>
</template>

<script>
import _ from 'lodash'
import storage from '@/storage/BelongsToFieldStorage'
import { TogglesTrashed, PerformsSearches, HandlesValidationErrors } from 'laravel-nova'
import { mixin as clickaway } from 'vue-clickaway'

export default {
    mixins: [TogglesTrashed, PerformsSearches, HandlesValidationErrors],
    props: {
        resourceName: String,
        field: Object,
        viaResource: {},
        viaResourceId: {},
        viaRelationship: {},
    },

    data: () => ({
        availableResources: [],
        initializingWithExistingResource: false,
        selectedResource: null,
        selectedResourceId: null,
        softDeletes: false,
        withTrashed: false,
        search: '',
    }),

    /**
     * Mount the component.
     */
    mounted() {
        this.initializeComponent()
    },

    methods: {
        initializeComponent() {
            this.withTrashed = false

            // If a user is editing an existing resource with this relation
            // we'll have a belongsToId on the field, and we should prefill
            // that resource in this field
            if (this.editingExistingResource) {
                this.initializingWithExistingResource = true
                this.selectedResourceId = this.field.belongsToId
            }

            // If the user is creating this resource via a related resource's index
            // page we'll have a viaResource and viaResourceId in the params and
            // should prefill the resource in this field with that information
            if (this.creatingViaRelatedResource) {
                this.initializingWithExistingResource = true
                this.selectedResourceId = this.viaResourceId
            }

            if (this.shouldSelectInitialResource && !this.isSearchable) {
                // If we should select the initial resource but the field is not
                // searchable we should load all of the available resources into the
                // field first and select the initial option
                this.initializingWithExistingResource = false
                this.getAvailableResources().then(() => this.selectInitialResource())
            } else if (this.shouldSelectInitialResource && this.isSearchable) {
                // If we should select the initial resource and the field is
                // searchable, we won't load all the resources but we will select
                // the initial option
                this.getAvailableResources().then(() => this.selectInitialResource())
            } else if (!this.shouldSelectInitialResource && !this.isSearchable) {
                // If we don't need to select an initial resource because the user
                // came to create a resource directly and there's no parent resource,
                // and the field is searchable we'll just load all of the resources
                this.getAvailableResources()
            }

            this.determineIfSoftDeletes()

            this.field.fill = this.fill
        },

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
            formData.append(
                this.field.attribute,
                this.selectedResource ? this.selectedResource.value : ''
            )

            formData.append(this.field.attribute + '_trashed', this.withTrashed)
        },

        /**
         * Get the resources that may be related to this resource.
         */
        getAvailableResources() {
            return storage
                .fetchAvailableResources(this.resourceName, this.field.attribute, this.queryParams)
                .then(({ data: { resources, softDeletes, withTrashed } }) => {
                    if (this.initializingWithExistingResource || !this.isSearchable) {
                        this.withTrashed = withTrashed
                    }

                    // Turn off initializing the existing resource after the first time
                    this.initializingWithExistingResource = false
                    this.availableResources = resources
                    this.softDeletes = softDeletes
                })
        },

        /**
         * Determine if the relatd resource is soft deleting.
         */
        determineIfSoftDeletes() {
            return storage.determineIfSoftDeletes(this.field.resourceName).then(response => {
                this.softDeletes = response.data.softDeletes
            })
        },

        /**
         * Determine if the given value is numeric.
         */
        isNumeric(value) {
            return !isNaN(parseFloat(value)) && isFinite(value)
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
         * Determine if we are editing and existing resource
         */
        editingExistingResource() {
            return Boolean(this.field.belongsToId)
        },

        /**
         * Determine if we are creating a new resource via a parent relation
         */
        creatingViaRelatedResource() {
            return (
                this.viaResource == this.field.resourceName &&
                this.viaRelationship === this.field.reverseRelation &&
                this.viaResourceId
            )
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
            return this.field.searchable
        },

        /**
         * Get the query params for getting available resources
         */
        queryParams() {
            return {
                params: {
                    current: this.selectedResourceId,
                    first: this.initializingWithExistingResource,
                    search: this.search,
                    withTrashed: this.withTrashed,
                },
            }
        },

        isLocked() {
            return (
                this.viaResource == this.field.resourceName &&
                this.viaRelationship === this.field.reverseRelation
            )
        },
    },
}
</script>
