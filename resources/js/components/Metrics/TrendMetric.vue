<template>
    <BaseTrendMetric
        @selected="handleRangeSelected"
        :title="card.name"
        :value="value"
        :chart-data="data"
        :ranges="card.ranges"
        :format="format"
        :prefix="prefix"
        :suffix="suffix"
        :selected-range-key="selectedRangeKey"
        :loading="loading"
    />
</template>

<script>
import _ from 'lodash'
import { InteractsWithDates, Minimum } from 'laravel-nova'
import BaseTrendMetric from './Base/TrendMetric'

export default {
    name: 'TrendMetric',

    mixins: [InteractsWithDates],

    components: {
        BaseTrendMetric,
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
        value: '',
        data: [],
        format: '(0.00a)',
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
        this.fetch()
    },

    methods: {
        handleRangeSelected(key) {
            this.selectedRangeKey = key
            this.fetch()
        },

        fetch() {
            this.loading = true

            Minimum(Nova.request().get(this.metricEndpoint, this.metricPayload)).then(
                ({
                    data: {
                        value: { labels, trend, value, prefix, suffix, format },
                    },
                }) => {
                    this.value = value
                    this.labels = Object.keys(trend)
                    this.data = {
                        labels: Object.keys(trend),
                        series: [
                            _.map(trend, (value, label) => {
                                return {
                                    meta: label,
                                    value: value,
                                }
                            }),
                        ],
                    }
                    this.format = format || this.format
                    this.prefix = prefix || this.prefix
                    this.suffix = suffix || this.suffix
                    this.loading = false
                }
            )
        },
    },

    computed: {
        hasRanges() {
            return this.card.ranges.length > 0
        },

        metricPayload() {
            const payload = {
                params: {
                    timezone: this.userTimezone,
                    twelveHourTime: this.usesTwelveHourTime,
                },
            }

            if (this.hasRanges) {
                payload.params.range = this.selectedRangeKey
            }

            return payload
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
