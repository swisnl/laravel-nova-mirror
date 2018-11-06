import _ from 'lodash'

/**
 * State
 */
const state = {
    filters: [],
}

/**
 * Getters
 */
const getters = {
    /**
     * Return all the filters for the resource
     */
    allFilters: state => state.filters,

    /**
     * Determine if there are any filters for the resource
     */
    hasFilters: state => Boolean(state.filters.length > 0),

    /**
     * The current unencoded filter value payload
     */
    currentFilters: (state, getters) => {
        return _.map(getters.allFilters, f => {
            return {
                class: f.class,
                value: f.currentValue,
            }
        })
    },

    /**
     * Return the current filters encoded to a string
     */
    currentEncodedFilters: (state, getters) => btoa(JSON.stringify(getters.currentFilters)),

    /**
     * Get a single filter from the list of filters
     */
    getFilter: state => filterKey => {
        return _.find(state.filters, filter => {
            return filter.class == filterKey
        })
    },

    /**
     * Get the options for a single filter
     */
    getOptionsForFilter: (state, getters) => filterKey => {
        const filter = getters.getFilter(filterKey)
        return filter ? filter.options : []
    },

    /**
     * Get the current value for a given filter at the provided key
     */
    filterOptionValue: (state, getters) => (filterKey, optionKey) => {
        const filter = getters.getFilter(filterKey)

        return _.find(filter.currentValue, (value, key) => {
            return key == optionKey
        })
    },
}

/**
 * Actions
 */
const actions = {
    async fetchFilters({ commit, rootGetters }, resourceName) {
        const { data } = await Nova.request().get('/nova-api/' + resourceName + '/filters')
        commit('storeFilters', data)

        // this.initializeCurrentFilterValuesFromQueryString()
    },
}

/**
 * Mutations
 */
const mutations = {
    updateFilterState(state, { filterClass, value }) {
        const filter = _(state.filters).find(f => f.class == filterClass)
        filter.currentValue = value
    },

    /**
     * Reset the filters in the store
     */
    resetFilters(state) {
        state.filters = []
        state.currentFilters = []
    },

    storeFilters(state, data) {
        state.filters = data
    },

    /**
     * Initialize the current filter values from the decoded query string.
     */
    initializeCurrentFilterValuesFromQueryString(state, encodedFilters) {
        if (encodedFilters) {
            const initialFilters = JSON.parse(atob(encodedFilters))

            _.each(initialFilters, f => {
                const filter = _(state.filters).find(filter => filter.class == f.class)
                filter.currentValue = f.value
            })
        }
    },
}

export default {
    state,
    getters,
    actions,
    mutations,
}
