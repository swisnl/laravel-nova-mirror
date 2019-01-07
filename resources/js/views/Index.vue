<template>
    <loading-view :loading="initialLoading" :dusk="resourceName + '-index-component'">
        <custom-index-header v-if="!viaResource" class="mb-3" :resource-name="resourceName" />

        <div v-if="shouldShowCards">
            <cards
                v-if="smallCards.length > 0"
                :cards="smallCards"
                class="mb-3"
                :resource-name="resourceName"
            />

            <cards
                v-if="largeCards.length > 0"
                :cards="largeCards"
                size="large"
                :resource-name="resourceName"
            />
        </div>

        <heading v-if="resourceResponse" class="mb-3">{{ headingTitle }}</heading>

        <div class="flex">
            <!-- Search -->
            <div
                v-if="resourceInformation.searchable && !viaHasOne"
                class="relative h-9 mb-6 flex-no-shrink"
            >
                <icon type="search" class="absolute search-icon-center ml-3 text-70" />

                <input
                    data-testid="search-input"
                    dusk="search"
                    class="appearance-none form-control form-input w-search pl-search"
                    :placeholder="__('Search')"
                    type="search"
                    v-model="search"
                    @keydown.stop="performSearch"
                    @search="performSearch"
                />
            </div>

            <div class="w-full flex items-center mb-6">
                <custom-index-toolbar v-if="!viaResource" :resource-name="resourceName" />

                <!-- Create / Attach Button -->
                <create-resource-button
                    :singular-name="singularName"
                    :resource-name="resourceName"
                    :via-resource="viaResource"
                    :via-resource-id="viaResourceId"
                    :via-relationship="viaRelationship"
                    :relationship-type="relationshipType"
                    :authorized-to-create="authorizedToCreate && !resourceIsFull"
                    :authorized-to-relate="authorizedToRelate"
                    class="flex-no-shrink ml-auto"
                />
            </div>
        </div>

        <loading-card :loading="loading">
            <div class="py-3 flex items-center border-b border-50">
                <div class="flex items-center">
                    <div class="px-3" v-if="shouldShowCheckBoxes">
                        <!-- Select All -->
                        <dropdown dusk="select-all-dropdown">
                            <dropdown-trigger slot-scope="{ toggle }" :handle-click="toggle">
                                <fake-checkbox :checked="selectAllChecked" />
                            </dropdown-trigger>

                            <dropdown-menu slot="menu" direction="ltr" width="250">
                                <div class="p-4">
                                    <ul class="list-reset">
                                        <li class="flex items-center mb-4">
                                            <checkbox-with-label
                                                :checked="selectAllChecked"
                                                @change="toggleSelectAll"
                                            >
                                                {{ __('Select All') }}
                                            </checkbox-with-label>
                                        </li>
                                        <li class="flex items-center">
                                            <checkbox-with-label
                                                dusk="select-all-matching-button"
                                                :checked="selectAllMatchingChecked"
                                                @change="toggleSelectAllMatching"
                                            >
                                                <template>
                                                    <span class="mr-1">{{
                                                        __('Select All Matching')
                                                    }}</span>
                                                    <span>({{ allMatchingResourceCount }})</span>
                                                </template>
                                            </checkbox-with-label>
                                        </li>
                                    </ul>
                                </div>
                            </dropdown-menu>
                        </dropdown>
                    </div>
                </div>

                <div class="flex items-center ml-auto px-3">
                    <!-- Action Selector -->
                    <action-selector
                        v-if="selectedResources.length > 0"
                        :resource-name="resourceName"
                        :actions="actions"
                        :pivot-actions="pivotActions"
                        :pivot-name="pivotName"
                        :query-string="{
                            currentSearch,
                            encodedFilters,
                            currentTrashed,
                            viaResource,
                            viaResourceId,
                            viaRelationship,
                        }"
                        :selected-resources="selectedResourcesForActionSelector"
                        @actionExecuted="getResources"
                    />

                    <!-- Lenses -->
                    <dropdown class="bg-30 hover:bg-40 mr-3 rounded" v-if="lenses.length > 0">
                        <dropdown-trigger
                            slot-scope="{
                                toggle,
                            }"
                            :handle-click="toggle"
                            class="px-3"
                        >
                            <h3
                                slot="default"
                                class="flex items-center font-normal text-base text-90 h-9"
                            >
                                {{ __('Lens') }}
                            </h3>
                        </dropdown-trigger>

                        <dropdown-menu slot="menu" width="240" direction="rtl">
                            <lens-selector :resource-name="resourceName" :lenses="lenses" />
                        </dropdown-menu>
                    </dropdown>

                    <!-- Filters -->
                    <filter-menu
                        :resource-name="resourceName"
                        :soft-deletes="softDeletes"
                        :via-resource="viaResource"
                        :via-has-one="viaHasOne"
                        :trashed="trashed"
                        :per-page="perPage"
                        @clear-selected-filters="clearSelectedFilters"
                        @filter-changed="filterChanged"
                        @trashed-changed="trashedChanged"
                        @per-page-changed="updatePerPageChanged"
                    />

                    <delete-menu
                        v-if="shouldShowDeleteMenu"
                        dusk="delete-menu"
                        :soft-deletes="softDeletes"
                        :resources="resources"
                        :selected-resources="selectedResources"
                        :via-many-to-many="viaManyToMany"
                        :all-matching-resource-count="allMatchingResourceCount"
                        :all-matching-selected="selectAllMatchingChecked"
                        :authorized-to-delete-selected-resources="
                            authorizedToDeleteSelectedResources
                        "
                        :authorized-to-force-delete-selected-resources="
                            authorizedToForceDeleteSelectedResources
                        "
                        :authorized-to-delete-any-resources="authorizedToDeleteAnyResources"
                        :authorized-to-force-delete-any-resources="
                            authorizedToForceDeleteAnyResources
                        "
                        :authorized-to-restore-selected-resources="
                            authorizedToRestoreSelectedResources
                        "
                        :authorized-to-restore-any-resources="authorizedToRestoreAnyResources"
                        @deleteSelected="deleteSelectedResources"
                        @deleteAllMatching="deleteAllMatchingResources"
                        @forceDeleteSelected="forceDeleteSelectedResources"
                        @forceDeleteAllMatching="forceDeleteAllMatchingResources"
                        @restoreSelected="restoreSelectedResources"
                        @restoreAllMatching="restoreAllMatchingResources"
                        @close="deleteModalOpen = false"
                    />
                </div>
            </div>

            <div v-if="!resources.length" class="flex justify-center items-center px-6 py-8">
                <div class="text-center">
                    <svg
                        class="mb-3"
                        xmlns="http://www.w3.org/2000/svg"
                        width="65"
                        height="51"
                        viewBox="0 0 65 51"
                    >
                        <g id="Page-1" fill="none" fill-rule="evenodd">
                            <g
                                id="05-blank-state"
                                fill="#A8B9C5"
                                fill-rule="nonzero"
                                transform="translate(-779 -695)"
                            >
                                <path
                                    id="Combined-Shape"
                                    d="M835 735h2c.552285 0 1 .447715 1 1s-.447715 1-1 1h-2v2c0 .552285-.447715 1-1 1s-1-.447715-1-1v-2h-2c-.552285 0-1-.447715-1-1s.447715-1 1-1h2v-2c0-.552285.447715-1 1-1s1 .447715 1 1v2zm-5.364125-8H817v8h7.049375c.350333-3.528515 2.534789-6.517471 5.5865-8zm-5.5865 10H785c-3.313708 0-6-2.686292-6-6v-30c0-3.313708 2.686292-6 6-6h44c3.313708 0 6 2.686292 6 6v25.049375c5.053323.501725 9 4.765277 9 9.950625 0 5.522847-4.477153 10-10 10-5.185348 0-9.4489-3.946677-9.950625-9zM799 725h16v-8h-16v8zm0 2v8h16v-8h-16zm34-2v-8h-16v8h16zm-52 0h16v-8h-16v8zm0 2v4c0 2.209139 1.790861 4 4 4h12v-8h-16zm18-12h16v-8h-16v8zm34 0v-8h-16v8h16zm-52 0h16v-8h-16v8zm52-10v-4c0-2.209139-1.790861-4-4-4h-44c-2.209139 0-4 1.790861-4 4v4h52zm1 39c4.418278 0 8-3.581722 8-8s-3.581722-8-8-8-8 3.581722-8 8 3.581722 8 8 8z"
                                />
                            </g>
                        </g>
                    </svg>

                    <h3 class="text-base text-80 font-normal mb-6">
                        {{
                            __('No :resource matched the given criteria.', {
                                resource: singularName.toLowerCase(),
                            })
                        }}
                    </h3>

                    <create-resource-button
                        classes="btn btn-sm btn-outline inline-flex items-center"
                        :singular-name="singularName"
                        :resource-name="resourceName"
                        :via-resource="viaResource"
                        :via-resource-id="viaResourceId"
                        :via-relationship="viaRelationship"
                        :relationship-type="relationshipType"
                        :authorized-to-create="authorizedToCreate && !resourceIsFull"
                        :authorized-to-relate="authorizedToRelate"
                    >
                    </create-resource-button>
                </div>
            </div>

            <div class="overflow-hidden overflow-x-auto relative">
                <!-- Resource Table -->
                <resource-table
                    :authorized-to-relate="authorizedToRelate"
                    :resource-name="resourceName"
                    :resources="resources"
                    :singular-name="singularName"
                    :selected-resources="selectedResources"
                    :selected-resource-ids="selectedResourceIds"
                    :actions-are-available="allActions.length > 0"
                    :should-show-checkboxes="shouldShowCheckBoxes"
                    :via-resource="viaResource"
                    :via-resource-id="viaResourceId"
                    :via-relationship="viaRelationship"
                    :relationship-type="relationshipType"
                    :update-selection-status="updateSelectionStatus"
                    @order="orderByField"
                    @delete="deleteResources"
                    @restore="restoreResources"
                    ref="resourceTable"
                />
            </div>

            <!-- Pagination -->
            <pagination-links
                v-if="resourceResponse"
                :resource-name="resourceName"
                :resources="resources"
                :resource-response="resourceResponse"
                @previous="selectPreviousPage"
                @next="selectNextPage"
            >
                <span v-if="resourceCountLabel" class="text-sm text-80">
                    {{ resourceCountLabel }}
                </span>
            </pagination-links>
        </loading-card>
    </loading-view>
</template>

<script>
import { Capitalize, Inflector, SingularOrPlural } from 'laravel-nova'
import {
    Errors,
    Deletable,
    Filterable,
    HasCards,
    Minimum,
    Paginatable,
    PerPageable,
    InteractsWithQueryString,
    InteractsWithResourceInformation,
} from 'laravel-nova'

export default {
    mixins: [
        Deletable,
        Filterable,
        HasCards,
        Paginatable,
        PerPageable,
        InteractsWithResourceInformation,
        InteractsWithQueryString,
    ],

    props: {
        field: {
            type: Object,
        },
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
        relationshipType: {
            type: String,
            default: '',
        },
    },

    data: () => ({
        actionEventsRefresher: null,
        initialLoading: true,
        loading: true,

        resourceResponse: null,
        resources: [],
        softDeletes: false,
        selectedResources: [],
        selectAllMatchingResources: false,
        allMatchingResourceCount: 0,

        deleteModalOpen: false,

        actions: [],
        pivotActions: null,

        search: '',
        lenses: [],

        authorizedToRelate: false,

        orderBy: '',
        orderByDirection: '',
        trashed: '',
    }),

    /**
     * Mount the component and retrieve its initial data.
     */
    async created() {
        if (Nova.missingResource(this.resourceName)) return this.$router.push({ name: '404' })

        // Bind the keydown even listener when the router is visited if this
        // component is not a relation on a Detail page
        if (!this.viaResource && !this.viaResourceId) {
            document.addEventListener('keydown', this.handleKeydown)
        }

        this.initializeSearchFromQueryString()
        this.initializePerPageFromQueryString()
        this.initializeTrashedFromQueryString()
        this.initializeOrderingFromQueryString()

        await this.initializeFilters()
        await this.getResources()
        await this.getAuthorizationToRelate()

        this.getLenses()
        this.getActions()

        this.initialLoading = false

        this.$watch(
            () => {
                return (
                    this.resourceName +
                    this.encodedFilters +
                    this.currentSearch +
                    this.currentPage +
                    this.currentPerPage +
                    this.currentOrderBy +
                    this.currentOrderByDirection +
                    this.currentTrashed
                )
            },
            () => {
                this.getResources()

                this.initializeSearchFromQueryString()
                this.initializePerPageFromQueryString()
                this.initializeTrashedFromQueryString()
                this.initializeOrderingFromQueryString()
            }
        )

        // Refresh the action events
        if (this.resourceName === 'action-events') {
            Nova.$on('refresh-action-events', () => {
                this.getResources()
            })

            this.actionEventsRefresher = setInterval(() => {
                this.getResources()
            }, 15 * 1000)
        }
    },

    /**
     * Unbind the keydown even listener when the component is destroyed
     */
    destroyed() {
        if (this.actionEventsRefresher) {
            clearInterval(this.actionEventsRefresher)
        }

        document.removeEventListener('keydown', this.handleKeydown)
    },

    methods: {
        /**
         * Handle the keydown event
         */
        handleKeydown(e) {
            // `c`
            if (
                !e.ctrlKey &&
                !e.altKey &&
                !e.metaKey &&
                !e.shiftKey &&
                e.keyCode == 67 &&
                e.target.tagName != 'INPUT' &&
                e.target.tagName != 'TEXTAREA'
            ) {
                this.$router.push({ name: 'create', params: { resourceName: this.resourceName } })
            }
        },

        /**
         * Select all of the available resources
         */
        selectAllResources() {
            this.selectedResources = this.resources.slice(0)
        },

        /**
         * Toggle the selection of all resources
         */
        toggleSelectAll(event) {
            if (this.selectAllChecked) return this.clearResourceSelections()
            this.selectAllResources()
        },

        /**
         * Toggle the selection of all matching resources in the database
         */
        toggleSelectAllMatching() {
            if (!this.selectAllMatchingResources) {
                this.selectAllResources()
                this.selectAllMatchingResources = true

                return
            }

            this.selectAllMatchingResources = false
        },

        /*
         * Update the resource selection status
         */
        updateSelectionStatus(resource) {
            if (!_(this.selectedResources).includes(resource))
                return this.selectedResources.push(resource)
            const index = this.selectedResources.indexOf(resource)
            if (index > -1) return this.selectedResources.splice(index, 1)
        },

        /**
         * Get the resources based on the current page, search, filters, etc.
         */
        getResources() {
            this.$nextTick(() => {
                this.clearResourceSelections()

                return Minimum(
                    Nova.request().get('/nova-api/' + this.resourceName, {
                        params: this.resourceRequestQueryString,
                    })
                ).then(({ data }) => {
                    this.resources = []

                    this.resourceResponse = data
                    this.resources = data.resources
                    this.softDeletes = data.softDeletes

                    this.loading = false

                    this.getAllMatchingResourceCount()
                })
            })
        },

        /**
         * Get the relatable authorization status for the resource.
         */
        getAuthorizationToRelate() {
            if (
                !this.authorizedToCreate &&
                (this.relationshipType != 'belongsToMany' && this.relationshipType != 'morphToMany')
            ) {
                return
            }

            if (!this.viaResource) {
                return (this.authorizedToRelate = true)
            }

            return Nova.request()
                .get(
                    '/nova-api/' +
                        this.resourceName +
                        '/relate-authorization' +
                        '?viaResource=' +
                        this.viaResource +
                        '&viaResourceId=' +
                        this.viaResourceId +
                        '&viaRelationship=' +
                        this.viaRelationship +
                        '&relationshipType=' +
                        this.relationshipType
                )
                .then(response => {
                    this.authorizedToRelate = response.data.authorized
                })
        },

        /**
         * Get the lenses available for the current resource.
         */
        getLenses() {
            this.lenses = []

            if (this.viaResource) {
                return
            }

            return Nova.request()
                .get('/nova-api/' + this.resourceName + '/lenses')
                .then(response => {
                    this.lenses = response.data
                })
        },

        /**
         * Get the actions available for the current resource.
         */
        getActions() {
            this.actions = []
            this.pivotActions = null
            return Nova.request()
                .get(
                    '/nova-api/' +
                        this.resourceName +
                        '/actions' +
                        '?viaResource=' +
                        this.viaResource +
                        '&viaResourceId=' +
                        this.viaResourceId +
                        '&viaRelationship=' +
                        this.viaRelationship
                )
                .then(response => {
                    this.actions = _.filter(response.data.actions, action => {
                        return !action.onlyOnDetail
                    })
                    this.pivotActions = response.data.pivotActions
                })
        },

        /**
         * Execute a search against the resource.
         */
        performSearch(event) {
            this.debouncer(() => {
                // Only search if we're not tabbing into the field
                if (event.which != 9) {
                    this.updateQueryString({
                        [this.pageParameter]: 1,
                        [this.searchParameter]: this.search,
                    })
                }
            })
        },

        debouncer: _.debounce(callback => callback(), 500),

        /**
         * Clear the selected resouces and the "select all" states.
         */
        clearResourceSelections() {
            this.selectAllMatchingResources = false
            this.selectedResources = []
        },

        /**
         * Get the count of all of the matching resources.
         */
        getAllMatchingResourceCount() {
            Nova.request()
                .get('/nova-api/' + this.resourceName + '/count', {
                    params: this.resourceRequestQueryString,
                })
                .then(response => {
                    this.allMatchingResourceCount = response.data.count
                })
        },

        /**
         * Sort the resources by the given field.
         */
        orderByField(field) {
            var direction = this.currentOrderByDirection == 'asc' ? 'desc' : 'asc'
            if (this.currentOrderBy != field.attribute) {
                direction = 'asc'
            }
            this.updateQueryString({
                [this.orderByParameter]: field.attribute,
                [this.orderByDirectionParameter]: direction,
            })
        },

        /**
         * Sync the current search value from the query string.
         */
        initializeSearchFromQueryString() {
            this.search = this.currentSearch
        },

        /**
         * Sync the current order by values from the query string.
         */
        initializeOrderingFromQueryString() {
            this.orderBy = this.currentOrderBy
            this.orderByDirection = this.currentOrderByDirection
        },

        /**
         * Sync the trashed state values from the query string.
         */
        initializeTrashedFromQueryString() {
            this.trashed = this.currentTrashed
        },

        /**
         * Update the trashed constraint for the resource listing.
         */
        trashedChanged(trashedStatus) {
            this.trashed = trashedStatus
            this.updateQueryString({ [this.trashedParameter]: this.trashed })
        },

        /**
         * Update the per page parameter in the query string
         */
        updatePerPageChanged(perPage) {
            this.perPage = perPage
            this.perPageChanged()
        },
    },

    computed: {
        /**
         * Determine if the resource has any filters
         */
        hasFilters() {
            return this.$store.getters[`${this.resourceName}/hasFilters`]
        },

        /**
         * Determine if the resource should show any cards
         */
        shouldShowCards() {
            // Don't show cards if this resource is beings shown via a relations
            return this.cards.length > 0 && this.resourceName == this.$route.params.resourceName
        },

        /**
         * Get the endpoint for this resource's metrics.
         */
        cardsEndpoint() {
            return `/nova-api/${this.resourceName}/cards`
        },

        /**
         * Get the name of the search query string variable.
         */
        searchParameter() {
            return this.resourceName + '_search'
        },

        /**
         * Get the name of the order by query string variable.
         */
        orderByParameter() {
            return this.resourceName + '_order'
        },

        /**
         * Get the name of the order by direction query string variable.
         */
        orderByDirectionParameter() {
            return this.resourceName + '_direction'
        },

        /**
         * Get the name of the trashed constraint query string variable.
         */
        trashedParameter() {
            return this.resourceName + '_trashed'
        },

        /**
         * Get the name of the per page query string variable.
         */
        perPageParameter() {
            return this.resourceName + '_per_page'
        },

        /**
         * Get the name of the page query string variable.
         */
        pageParameter() {
            return this.resourceName + '_page'
        },

        /**
         * Build the resource request query string.
         */
        resourceRequestQueryString() {
            return {
                search: this.currentSearch,
                filters: this.encodedFilters,
                orderBy: this.currentOrderBy,
                orderByDirection: this.currentOrderByDirection,
                perPage: this.currentPerPage,
                trashed: this.currentTrashed,
                page: this.currentPage,
                viaResource: this.viaResource,
                viaResourceId: this.viaResourceId,
                viaRelationship: this.viaRelationship,
                viaResourceRelationship: this.viaResourceRelationship,
                relationshipType: this.relationshipType,
            }
        },

        /**
         * Determine if all resources are selected.
         */
        selectAllChecked() {
            return this.selectedResources.length == this.resources.length
        },

        /**
         * Determine if all matching resources are selected.
         */
        selectAllMatchingChecked() {
            return (
                this.selectedResources.length == this.resources.length &&
                this.selectAllMatchingResources
            )
        },

        /**
         * Get the IDs for the selected resources.
         */
        selectedResourceIds() {
            return _.map(this.selectedResources, resource => resource.id.value)
        },

        /**
         * Get all of the actions available to the resource.
         */
        allActions() {
            return this.hasPivotActions
                ? this.actions.concat(this.pivotActions.actions)
                : this.actions
        },

        /**
         * Determine if the resource has any pivot actions available.
         */
        hasPivotActions() {
            return this.pivotActions && this.pivotActions.actions.length > 0
        },

        /**
         * Determine if the resource has any actions available.
         */
        actionsAreAvailable() {
            return this.allActions.length > 0
        },

        /**
         * Get the name of the pivot model for the resource.
         */
        pivotName() {
            return this.pivotActions ? this.pivotActions.name : ''
        },

        /**
         * Get the current search value from the query string.
         */
        currentSearch() {
            return this.$route.query[this.searchParameter] || ''
        },

        /**
         * Get the current order by value from the query string.
         */
        currentOrderBy() {
            return this.$route.query[this.orderByParameter] || ''
        },

        /**
         * Get the current order by direction from the query string.
         */
        currentOrderByDirection() {
            return this.$route.query[this.orderByDirectionParameter] || 'desc'
        },

        /**
         * Get the current trashed constraint value from the query string.
         */
        currentTrashed() {
            return this.$route.query[this.trashedParameter] || ''
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
         * Determine if the resource / relationship is "full".
         */
        resourceIsFull() {
            return this.viaHasOne && this.resources.length > 0
        },

        /**
         * Determine if the current resource listing is via a has-one relationship.
         */
        viaHasOne() {
            return this.relationshipType == 'hasOne' || this.relationshipType == 'morphOne'
        },

        /**
         * Get the singular name for the resource
         */
        singularName() {
            if (this.isRelation && this.field) {
                return Capitalize(this.field.singularLabel)
            }

            return Capitalize(this.resourceInformation.singularLabel)
        },

        /**
         * Get the selected resources for the action selector.
         */
        selectedResourcesForActionSelector() {
            return this.selectAllMatchingChecked ? 'all' : this.selectedResourceIds
        },

        /**
         * Determine if there are any resources for the view
         */
        hasResources() {
            return Boolean(this.resources.length > 0)
        },

        /**
         * Determine if there any lenses for this resource
         */
        hasLenses() {
            return Boolean(this.lenses.length > 0)
        },

        /**
         * Determine whether to show the selection checkboxes for resources
         */
        shouldShowCheckBoxes() {
            return (
                Boolean(this.hasResources && !this.viaHasOne) &&
                Boolean(
                    this.actionsAreAvailable ||
                        this.authorizedToDeleteAnyResources ||
                        this.canShowDeleteMenu
                )
            )
        },

        /**
         * Determine if any selected resources may be deleted.
         */
        authorizedToDeleteSelectedResources() {
            return Boolean(_.find(this.selectedResources, resource => resource.authorizedToDelete))
        },

        /**
         * Determine if any selected resources may be force deleted.
         */
        authorizedToForceDeleteSelectedResources() {
            return Boolean(
                _.find(this.selectedResources, resource => resource.authorizedToForceDelete)
            )
        },

        /**
         * Determine if the user is authorized to delete any listed resource.
         */
        authorizedToDeleteAnyResources() {
            return (
                this.resources.length > 0 &&
                Boolean(_.find(this.resources, resource => resource.authorizedToDelete))
            )
        },

        /**
         * Determine if the user is authorized to force delete any listed resource.
         */
        authorizedToForceDeleteAnyResources() {
            return (
                this.resources.length > 0 &&
                Boolean(_.find(this.resources, resource => resource.authorizedToForceDelete))
            )
        },

        /**
         * Determine if any selected resources may be restored.
         */
        authorizedToRestoreSelectedResources() {
            return Boolean(_.find(this.selectedResources, resource => resource.authorizedToRestore))
        },

        /**
         * Determine if the user is authorized to restore any listed resource.
         */
        authorizedToRestoreAnyResources() {
            return (
                this.resources.length > 0 &&
                Boolean(_.find(this.resources, resource => resource.authorizedToRestore))
            )
        },

        /**
         * Determinw whether the delete menu should be shown to the user
         */
        shouldShowDeleteMenu() {
            return Boolean(this.selectedResources.length > 0) && this.canShowDeleteMenu
        },

        /**
         * Determine whether the user is authorized to perform actions on the delete menu
         */
        canShowDeleteMenu() {
            return Boolean(
                this.authorizedToDeleteSelectedResources ||
                    this.authorizedToForceDeleteSelectedResources ||
                    this.authorizedToRestoreSelectedResources ||
                    this.selectAllMatchingChecked
            )
        },

        /**
         * Determine if the index is a relation field
         */
        isRelation() {
            return Boolean(this.viaResourceId && this.viaRelationship)
        },

        /**
         * Return the heading for the view
         */
        headingTitle() {
            return this.isRelation && this.field ? this.field.name : this.resourceResponse.label
        },

        /**
         * Return the resource count label
         */
        resourceCountLabel() {
            let label = this.resources.length > 1 ? this.__('resources') : this.__('resource')

            return (
                this.resources.length &&
                `${this.resources.length}/${this.allMatchingResourceCount} ${label}`
            )
        },

        /**
         * Return the currently encoded filter string from the store
         */
        encodedFilters() {
            return this.$store.getters[`${this.resourceName}/currentEncodedFilters`]
        },

        /**
         * Return the initial encoded filters from the query string
         */
        initialEncodedFilters() {
            return this.$route.query[this.filterParameter] || ''
        },
    },
}
</script>
