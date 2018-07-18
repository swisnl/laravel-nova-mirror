<template>
    <div class="w-full max-w-xs">
        <div
            v-if="currentlySearching"
            @mousedown="closeSearch"
            class="fixed pin bg-80 z-0 opacity-25"
        />

        <div class="relative">
            <input
                dusk="global-search"
                ref="input"
                @input.stop="search"
                @keydown.stop=""
                @keydown.enter.stop="goToCurrentlySelectedResource"
                @keydown.esc.stop="closeSearch"
                @blur="closeSearch"
                @focus="openSearch"
                @keydown.down.prevent="move(1)"
                @keydown.up.prevent="move(-1)"
                v-model="searchTerm"
                type="search"
                placeholder="Global search"
                class="form-control form-input form-input-bordered w-full"
            />

            <div
                v-if="shouldShowResults"
                class="overflow-hidden absolute rounded-lg shadow-lg w-full mt-2 max-h-search overflow-y-auto"
                ref="container"
            >
                <div v-for="group in formattedResults">
                    <h3 class="text-xs uppercase tracking-wide text-80 bg-40 py-2 px-3">
                        {{ group.resourceName }}
                    </h3>

                    <ul class="list-reset">
                        <li
                            v-for="item in group.items"
                            :key="item.label + item.index"
                            :ref="item.index === highlightedResultIndex ? 'selected' : null"
                        >
                            <router-link :to="{
                                    name: 'detail',
                                    params: {
                                        resourceName: item.resourceName,
                                        resourceId: item.resourceId,
                                    }
                                }"
                                :dusk="item.resourceName + ' ' + item.index"
                                @click.native="closeSearch"
                                class="flex items-center text-90 hover:bg-20 block py-2 px-3 no-underline font-normal"
                                :class="{
                                    'bg-white': highlightedResultIndex != item.index,
                                    'bg-20': highlightedResultIndex == item.index,
                                }"
                            >
                                <img v-if="item.avatar" :src="item.avatar" class="h-8 w-8 rounded-full mr-3" />
                                {{ item.label }}
                            </router-link>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Minimum } from 'laravel-nova'

export default {
    data: () => ({
        currentlySearching: false,
        searchTerm: '',
        results: [],
        highlightedResultIndex: 0,
    }),

    mounted() {
        // Open search menu if the user types '/'
        document.addEventListener('keydown', this.handleKeydown)
    },

    destroyed() {
        document.removeEventListener('keydown', this.handleKeydown)
    },

    methods: {
        isNotInputElement(event) {
            const tagName = event.target.tagName
            return Boolean(tagName !== 'INPUT' && tagName !== 'TEXTAREA')
        },

        handleKeydown(event) {
            if (this.isNotInputElement(event) && event.keyCode == 191) {
                event.preventDefault()
                event.stopPropagation()
                this.openSearch()
            }
        },

        openSearch() {
            this.clearSearch()
            this.$refs.input.focus()
            this.currentlySearching = true
            this.clearResults()
        },

        closeSearch() {
            this.clearSearch()
            this.clearResults()
            this.$refs.input.blur()
            this.currentlySearching = false
        },

        clearSearch() {
            this.searchTerm = ''
        },

        clearResults() {
            this.results = []
        },

        search(event) {
            this.highlightedResultIndex = 0

            this.debouncer(() => {
                this.fetchResults(event.target.value)
            }, 500)
        },

        async fetchResults(search) {
            this.results = []

            if (search !== '') {
                try {
                    const { data: results } = await Nova.request().get('/nova-api/search', {
                        params: { search },
                    })

                    this.results = results
                } catch (e) {
                    throw e
                }
            }
        },

        /**
         * Debounce function for the search handler
         */
        debouncer: _.debounce(callback => callback(), 500),

        /**
         * Move the highlighted results
         */
        move(offset) {
            let newIndex = this.highlightedResultIndex + offset

            if (newIndex >= 0 && newIndex < this.results.length) {
                this.highlightedResultIndex = newIndex
                this.updateScrollPosition()
            }
        },

        updateScrollPosition() {
            this.$nextTick(() => {
                if (this.$refs.selected) {
                    if (
                        this.$refs.selected[0].offsetTop >
                        this.$refs.container.scrollTop +
                            this.$refs.container.clientHeight -
                            this.$refs.selected[0].clientHeight
                    ) {
                        this.$refs.container.scrollTop =
                            this.$refs.selected[0].offsetTop +
                            this.$refs.selected[0].clientHeight -
                            this.$refs.container.clientHeight
                    }
                    if (this.$refs.selected[0].offsetTop < this.$refs.container.scrollTop) {
                        this.$refs.container.scrollTop = this.$refs.selected[0].offsetTop
                    }
                }
            })
        },

        goToCurrentlySelectedResource() {
            const resource = _.find(
                this.indexedResults,
                res => res.index == this.highlightedResultIndex
            )

            this.$router.push({
                name: 'detail',
                params: {
                    resourceName: resource.resourceName,
                    resourceId: resource.resourceId,
                },
            })

            this.closeSearch()
        },
    },

    computed: {
        hasResults() {
            return this.results.length > 0
        },

        shouldShowResults() {
            return this.currentlySearching && this.hasResults
        },

        indexedResults() {
            return _.map(this.results, (item, index) => {
                return { index, ...item }
            })
        },

        formattedGroups() {
            return _.chain(this.indexedResults)
                .map(item => {
                    return {
                        resourceName: item.resourceName,
                        resourceTitle: item.resourceTitle,
                    }
                })
                .uniqBy('resourceName')
                .value()
        },

        formattedResults() {
            return _.map(this.formattedGroups, group => {
                return {
                    resourceName: group.resourceName,
                    resourceTitle: group.resourceTitle,
                    items: _.filter(
                        this.indexedResults,
                        item => item.resourceName == group.resourceName
                    ),
                }
            })
        },
    },
}
</script>
