<template>
    <div>
        <heading v-if="cards.length > 1" class="mb-6">Dashboard</heading>

        <div v-if="shouldShowCards">
            <cards v-if="cards.length > 0" :cards="smallCards" class="mb-3"/>
            <cards v-if="cards.length > 0" :cards="largeCards" size="large"/>
        </div>
    </div>
</template>

<script>
import { HasCards } from 'laravel-nova'
import cardSizes from '@/util/sizes'

export default {
    mixins: [HasCards],

    computed: {
        /**
         * Get the endpoint for this dashboard's metrics.
         */
        cardsEndpoint() {
            return `/nova-api/cards`
        },

        /**
         * Return the small cards used for the Dashboard
         */
        smallCards() {
            return _.filter(this.cards, c => cardSizes.indexOf(c.width) !== -1)
        },

        /**
         * Return the full-width cards used for the Dashboard
         */
        largeCards() {
            return _.filter(this.cards, c => c.width == 'full')
        },

        /**
         * Determine whether we have cards to show on the Dashboard
         */
        shouldShowCards() {
            return this.cards.length > 0
        },
    },
}
</script>
