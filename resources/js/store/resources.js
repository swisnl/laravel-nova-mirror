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
     * Determine if there are any filters for the resource.
     */
    hasFilters: state => Boolean(state.filters.length > 0),

    /**
     * The current unencoded filter value payload
     */
    currentFilters: (state, getters) => {
        return _.map(state.filters, f => {
            return {
                class: f.class,
                value: f.currentValue,
            }
        })
    },

    /**
     * Return the current filters encoded to a string.
     */
    currentEncodedFilters: (state, getters) => btoa(JSON.stringify(getters.currentFilters)),

    /**
     * Get a single filter from the list of filters.
     */
    getFilter: state => filterKey => {
        return _.find(state.filters, filter => {
            return filter.class == filterKey
        })
    },

    /**
     * Get the options for a single filter.
     */
    getOptionsForFilter: (state, getters) => filterKey => {
        const filter = getters.getFilter(filterKey)
        return filter ? filter.options : []
    },

    /**
     * Get the current value for a given filter at the provided key.
     */
    filterOptionValue: (state, getters) => (filterKey, optionKey) => {
        const filter = getters.getFilter(filterKey)

        return _.find(filter.currentValue, (value, key) => key == optionKey)
    },
}

/**
 * Actions
 */
const actions = {
    /**
     * Fetch the current filters for the given resource name.
     */
    async fetchFilters({ commit }, resourceName) {
        const { data } = await Nova.request().get('/nova-api/' + resourceName + '/filters')
        commit('storeFilters', data)
    },

    /**
     * Reset the default filter state to the original filter settings.
     */
    async resetFilterState({ commit, state, getters }, resourceName) {
        const { data } = await Nova.request().get('/nova-api/' + resourceName + '/filters')

        _.each(data, filter =>
            commit('updateFilterState', { filterClass: filter.class, value: filter.currentValue })
        )
    },

    /**
     * Initialize the current filter values from the decoded query string.
     */
    async initializeCurrentFilterValuesFromQueryString({ commit, getters }, encodedFilters) {
        if (encodedFilters) {
            const initialFilters = JSON.parse(atob(encodedFilters))

            _.each(initialFilters, f => {
                commit('updateFilterState', { filterClass: f.class, value: f.value })
            })
        }
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
     * Store the mutable filter settings
     */
    storeFilters(state, data) {
        state.filters = data
    },
}

export default {
    state,
    getters,
    actions,
    mutations,
}
