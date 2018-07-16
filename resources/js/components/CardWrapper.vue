<template>
    <div
        class="px-3 mb-6"
        :class="widthClass"
        :key="`${card.component}.${card.name}`"
    >
        <component
            :class="cardSizeClass"
            :is="card.component"
            :card="card"
            :resourceName="resourceName"
            :resourceId="resourceId"
        />
    </div>
</template>

<script>
import cardSizes from '@/util/sizes'

export default {
    props: {
        card: {
            type: Object,
            required: true,
        },

        size: {
            type: String,
            default: '',
        },

        resourceName: {
            type: String,
        },

        resourceId: {
            type: [Number, String],
        },
    },

    computed: {
        /**
         * The class given to the card wrappers based on its width
         */
        widthClass() {
            return this.size == 'large'
                ? 'w-full'
                : cardSizes.indexOf(this.card.width) !== -1
                    ? `w-${this.card.width}`
                    : 'w-1/3'
        },

        /**
         * The class given to the card based on its size
         */
        cardSizeClass() {
            return this.size !== 'large' ? 'card-panel' : ''
        },
    },
}
</script>
