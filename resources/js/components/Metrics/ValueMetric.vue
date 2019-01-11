<template>
    <BaseValueMetric
        @selected="handleRangeSelected"
        :title="card.name"
        :previous="previous"
        :value="value"
        :ranges="card.ranges"
        :format="format"
        :prefix="prefix"
        :suffix="suffix"
        :selected-range-key="selectedRangeKey"
        :loading="loading"
    />
</template>

<script>
import { Minimum } from 'laravel-nova'
import BaseValueMetric from './Base/ValueMetric'

export default {
    name: 'ValueMetric',

    components: {
        BaseValueMetric,
    },

    props: {
        card: {
            type: Object,
            required: true,
        },
        resourceName: {
            type: String,
            default: '',
        },
        resourceId: {
            type: [Number, String],
            default: '',
        },
    },

    data: () => ({
        loading: true,
        format: '(0.00a)',
        value: 0,
        previous: 0,
        prefix: '',
        suffix: '',
        selectedRangeKey: null,
    }),

    created() {
        if (this.hasRanges) {
            this.selectedRangeKey = this.card.ranges[0].value
        }
    },

    mounted() {
        this.fetch(this.selectedRangeKey)
    },

    methods: {
        handleRangeSelected(key) {
            this.selectedRangeKey = key
            this.fetch()
        },

        fetch() {
            this.loading = true

            Minimum(Nova.request().get(this.metricEndpoint, this.rangePayload)).then(
                ({
                    data: {
                        value: { value, previous, prefix, suffix, format },
                    },
                }) => {
                    this.value = value
                    this.format = format || this.format
                    this.prefix = prefix || this.prefix
                    this.suffix = suffix || this.suffix
                    this.previous = previous
                    this.loading = false
                }
            )
        },
    },

    computed: {
        hasRanges() {
            return this.card.ranges.length > 0
        },

        rangePayload() {
            return this.hasRanges ? { params: { range: this.selectedRangeKey } } : {}
        },

        metricEndpoint() {
            if (this.resourceName && this.resourceId) {
                return `/nova-api/${this.resourceName}/${this.resourceId}/metrics/${
                    this.card.uriKey
                }`
            } else if (this.resourceName) {
                return `/nova-api/${this.resourceName}/metrics/${this.card.uriKey}`
            } else {
                return `/nova-api/metrics/${this.card.uriKey}`
            }
        },
    },
}
</script>
