<template>
    <loading-card :loading="loading" class="px-6 py-4">
        <div class="flex mb-4">
            <h3 class="mr-3 text-base text-80 font-bold">{{ title }}</h3>

            <select
                v-if="ranges.length > 0"
                @change="handleChange"
                class="ml-auto min-w-24 h-6 text-xs no-appearance bg-40"
            >
                <option
                    v-for="option in ranges"
                    :key="option.value"
                    :value="option.value"
                    :selected="option.value == selectedRangeKey"
                >
                    {{ option.label }}
                </option>
            </select>
        </div>

        <p class="flex items-center text-4xl mb-4">
            {{ formattedValue }}
            <span v-if="suffix" class="ml-2 text-sm font-bold text-80">{{ formattedSuffix }}</span>
        </p>

        <div
            ref="chart"
            class="z-40 absolute pin rounded-b-lg ct-chart"
            style="top: 60%"
        />
    </loading-card>
</template>

<script>
import numeral from 'numeral'
import _ from 'lodash'
import Chartist from 'chartist'
import 'chartist-plugin-tooltips'
import 'chartist/dist/chartist.min.css'
import { SingularOrPlural } from 'laravel-nova'
import 'chartist-plugin-tooltips/dist/chartist-plugin-tooltip.css'

// const getLabelForValue = (value, vm) => {
//     const { labels, series } = vm.chartData

//     return labels[_.findIndex(series[0], (item) => {
//         return item.value == value;
//     })]
// }

export default {
    name: 'BaseTrendMetric',

    props: {
        loading: Boolean,
        title: {},
        value: {},
        chartData: {},
        prefix: '',
        suffix: '',
        ranges: { type: Array, default: () => [] },
        selectedRangeKey: [String, Number],
    },

    data: () => ({ chartist: null }),

    watch: {
        selectedRangeKey: function(newRange, oldRange) {
            this.renderChart()
        },

        chartData: function(newData, oldData) {
            this.renderChart()
        },
    },

    mounted() {
        this.chartist = new Chartist.Line(this.$refs.chart, this.chartData, {
            lineSmooth: Chartist.Interpolation.none(),
            fullWidth: true,
            showPoint: true,
            showLine: true,
            showArea: true,
            chartPadding: {
                top: 10,
                right: 0,
                bottom: 0,
                left: 0,
            },
            low: 0,
            axisX: {
                showGrid: false,
                showLabel: true,
                offset: 0,
            },
            axisY: {
                showGrid: false,
                showLabel: true,
                offset: 0,
            },
            plugins: [
                // Chartist.plugins.tooltip({
                //     anchorToPoint: true,
                // }),

                Chartist.plugins.tooltip({
                    anchorToPoint: true,
                    transformTooltipTextFnc: value => {
                        if (this.prefix) {
                            return `${this.prefix}${value}`
                        }

                        if (this.suffix) {
                            const suffix = SingularOrPlural(value, this.suffix)
                            return `${value} ${suffix}`
                        }

                        return `${value}`
                    },
                }),
            ],
        })
    },

    methods: {
        renderChart() {
            this.chartist.update(this.chartData)
        },

        handleChange(event) {
            this.$emit('selected', event.target.value)
        },
    },

    computed: {
        isNullValue() {
            return this.value == null
        },

        formattedValue() {
            if (!this.isNullValue) {
                const numeralValue = numeral(this.value)

                return numeralValue.value() > 1000
                    ? this.prefix + numeralValue.format('(0.00a)')
                    : this.prefix + this.value
            }

            return ''
        },

        formattedSuffix() {
            return SingularOrPlural(this.value, this.suffix)
        },
    },
}
</script>
