<template>
    <table
        v-if="resources.length > 0"
        class="table w-full"
        cellpadding="0"
        cellspacing="0"
        data-testid="resource-table"
    >
        <thead>
            <tr>
                <!-- Select Checkbox -->
                <th
                    :class="{
                        'w-16': shouldShowCheckboxes,
                        'w-8': !shouldShowCheckboxes,
                    }"
                >
                    &nbsp;
                </th>

                <!-- Field Names -->
                <th v-for="field in fields" :class="`text-${field.textAlign}`">
                    <sortable-icon
                        @sort="requestOrderByChange(field)"
                        :resource-name="resourceName"
                        :uri-key="field.attribute"
                        v-if="field.sortable"
                    >
                        {{ field.indexName }}
                    </sortable-icon>

                    <span v-else> {{ field.indexName }} </span>
                </th>

                <th>
                    &nbsp;<!--
                        View, Edit, Delete
                    -->
                </th>
            </tr>
        </thead>
        <tbody>
            <tr
                v-for="(resource, index) in resources"
                :testId="`${resourceName}-items-${index}`"
                :key="resource.id.value"
                :delete-resource="deleteResource"
                :restore-resource="restoreResource"
                is="resource-table-row"
                :resource="resource"
                :resource-name="resourceName"
                :relationship-type="relationshipType"
                :via-relationship="viaRelationship"
                :via-resource="viaResource"
                :via-resource-id="viaResourceId"
                :via-many-to-many="viaManyToMany"
                :checked="selectedResources.indexOf(resource) > -1"
                :actions-are-available="actionsAreAvailable"
                :should-show-checkboxes="shouldShowCheckboxes"
                :update-selection-status="updateSelectionStatus"
            />
        </tbody>
    </table>
</template>

<script>
import { InteractsWithResourceInformation } from 'laravel-nova'

export default {
    mixins: [InteractsWithResourceInformation],

    props: {
        authorizedToRelate: {
            type: Boolean,
            required: true,
        },
        resourceName: {
            default: null,
        },
        resources: {
            default: [],
        },
        singularName: {
            type: String,
            required: true,
        },
        selectedResources: {
            default: [],
        },
        selectedResourceIds: {},
        shouldShowCheckboxes: {
            type: Boolean,
            default: false,
        },
        actionsAreAvailable: {
            type: Boolean,
            default: false,
        },
        viaResource: {
            default: null,
        },
        viaResourceId: {
            default: null,
        },
        viaRelationship: {
            default: null,
        },
        relationshipType: {
            default: null,
        },
        updateSelectionStatus: {
            type: Function,
        },
    },

    data: () => ({
        selectAllResources: false,
        selectAllMatching: false,
        resourceCount: null,
    }),

    methods: {
        /**
         * Delete the given resource.
         */
        deleteResource(resource) {
            this.$emit('delete', [resource])
        },

        /**
         * Restore the given resource.
         */
        restoreResource(resource) {
            this.$emit('restore', [resource])
        },

        /**
         * Broadcast that the ordering should be updated.
         */
        requestOrderByChange(field) {
            this.$emit('order', field)
        },
    },

    computed: {
        /**
         * Get all of the available fields for the resources.
         */
        fields() {
            if (this.resources) {
                return this.resources[0].fields
            }
        },

        /**
         * Determine if the current resource listing is via a many-to-many relationship.
         */
        viaManyToMany() {
            return (
                this.relationshipType == 'belongsToMany' || this.relationshipType == 'morphToMany'
            )
        },

        /**
         * Determine if the current resource listing is via a has-one relationship.
         */
        viaHasOne() {
            return this.relationshipType == 'hasOne' || this.relationshipType == 'morphOne'
        },
    },
}
</script>
