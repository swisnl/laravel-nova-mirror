<template>
    <div>
        <div
            v-if="currentlySearching"
            @mousedown="closeSearch"
            class="fixed pin bg-80 z-0 opacity-25"
        />

        <div class="relative">
            <input
                ref="input"
                @input.stop="search"
                @keydown.esc.stop="closeSearch"
                @focus="openSearch"
                v-model="searchTerm"
                type="search"
                class="form-control form-input form-input-bordered w-search" placeholder="Global search"
            />

            <div v-if="shouldShowResults" class="overflow-hidden absolute border border-60 rounded-lg shadow-lg w-full mt-2">
                <div v-for="group in formattedResults">
                    <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3 border-b border-50">
                        {{ group.resourceName }}
                    </h3>

                    <ul class="list-reset">
                        <li
                            v-for="item in group.items"
                            class="border-b border-50"
                        >
                            <router-link :to="{
                                    name: 'detail',
                                    params: {
                                        resourceName: item.resourceName,
                                        resourceId: item.resourceId,
                                    }
                                }"
                                class="hover:bg-primary hover:text-white block p-3 no-underline text-sm font-bold"
                                :class="{
                                    'bg-white text-primary': highlightedResultIndex != item.index,
                                    'bg-primary text-white': highlightedResultIndex == item.index,
                                }"
                            >
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

const stubResults = [
    {
        label: 'First Post',
        resourceName: 'posts',
        resourceTitle: 'Posts',
        resourceId: 1,
    },
    {
        label: 'Second Post',
        resourceName: 'posts',
        resourceTitle: 'Posts',
        resourceId: 2,
    },
    {
        label: 'Third Post',
        resourceName: 'posts',
        resourceTitle: 'Posts',
        resourceId: 3,
    },
    {
        label: 'Taylor Otwell',
        resourceName: 'users',
        resourceTitle: 'Users',
        resourceId: 1,
    },
    {
        label: 'David Hemphill',
        resourceName: 'users',
        resourceTitle: 'Users',
        resourceId: 2,
    },
]

export default {
    data: () => ({
        open: false,
        searchTerm: '',
        results: [],
        highlightedResultIndex: 3,
    }),

    methods: {
        openSearch() {
            this.clearSearch()
            this.open = true
            this.clearResults()
        },

        closeSearch() {
            this.clearSearch()
            this.$refs.input.blur()
            this.open = false
        },

        clearSearch() {
            this.searchTerm = ''
        },

        clearResults() {
            this.results = []
        },

        search(event) {
            this.fetchResults(event.target.value)
        },

        async fetchResults(searchTerm) {
            // Something like this from the server
            // const { data: results } = await Nova.request(this.searchEndpoint, {
            //     params: { q: searchTerm },
            // })

            // But we'll fake it for now
            try {
                const {
                    data: { results },
                } = await Minimum(
                    new Promise((resolve, reject) => {
                        resolve({ data: { results: stubResults } })
                    }, 3000)
                )

                console.log(results)

                this.results = results
            } catch (e) {
                throw e
            }
        },
    },

    // Delete this
    created() {
        this.open = true
        this.results = stubResults
    },

    computed: {
        currentlySearching() {
            return this.open == true
        },

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
                .map(item => item.resourceName)
                .uniq()
                .value()
        },

        formattedResults() {
            return _.map(this.formattedGroups, group => {
                return {
                    resourceName: group,
                    items: _.filter(this.indexedResults, item => item.resourceName == group),
                }
            })
        },
    },
}
</script>
