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
                @keydown.stop=""
                @keydown.enter.stop="goToCurrentlySelectedResource"
                @keydown.esc.stop="closeSearch"
                @focus="openSearch"
                @keydown.down.prevent="move(1)"
                @keydown.up.prevent="move(-1)"
                v-model="searchTerm"
                type="search"
                placeholder="Global search"
                class="form-control form-input form-input-bordered w-search"
            />

            <div v-if="shouldShowResults" class="overflow-hidden absolute rounded-lg shadow-lg w-full mt-2">
                <div v-for="group in formattedResults">
                    <h3 class="text-xs uppercase tracking-wide text-80 bg-40 py-2 px-3">
                        {{ group.resourceName }}
                    </h3>

                    <ul class="list-reset">
                        <li v-for="item in group.items">
                            <router-link :to="{
                                    name: 'detail',
                                    params: {
                                        resourceName: item.resourceName,
                                        resourceId: item.resourceId,
                                    }
                                }"
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

const stubResults = [
    {
        label: 'First Post',
        resourceName: 'posts',
        resourceTitle: 'Posts',
        resourceId: 1,
        url: 'http://nova-app.test/nova/resources/posts/1',
    },
    {
        label: 'Second Post',
        resourceName: 'posts',
        resourceTitle: 'Posts',
        resourceId: 2,
        url: 'http://nova-app.test/nova/resources/posts/2',
    },
    {
        label: 'Third Post',
        resourceName: 'posts',
        resourceTitle: 'Posts',
        resourceId: 3,
        url: 'http://nova-app.test/nova/resources/posts/3',
    },
    {
        label: 'Taylor Otwell',
        resourceName: 'users',
        resourceTitle: 'Users',
        resourceId: 1,
        url: 'http://nova-app.test/nova/resources/users/1',
    },
    {
        label: 'David Hemphill',
        resourceName: 'users',
        resourceTitle: 'Users',
        resourceId: 2,
        url: 'http://nova-app.test/nova/resources/users/2',
        avatar: 'https://www.gravatar.com/avatar/2821f93cef33ccd01b1262ac41f87d9c?s=300',
    },
]

export default {
    data: () => ({
        currentlySearching: false,
        searchTerm: '',
        results: [],
        highlightedResultIndex: 0,
    }),

    methods: {
        openSearch() {
            this.clearSearch()
            this.currentlySearching = true
            this.clearResults()
        },

        closeSearch() {
            this.clearSearch()
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

        async fetchResults(searchTerm) {
            this.results = []

            try {
                // Something like this from the server
                // const { data: results } = await Nova.request(this.searchEndpoint, {
                //     params: { q: searchTerm },
                // })

                // But we'll fake it for now
                const {
                    data: { results },
                } = await Minimum(
                    new Promise((resolve, reject) => {
                        resolve({ data: { results: stubResults } })
                    }, 3000)
                )

                this.results = results
            } catch (e) {
                throw e
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
                // this.updateScrollPosition()
            }
        },

        goToCurrentlySelectedResource() {
            this.closeSearch()

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
