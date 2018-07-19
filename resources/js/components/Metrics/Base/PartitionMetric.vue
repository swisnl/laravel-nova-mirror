<template>
    <loading-card :loading="loading" class="px-6 py-4">
        <h3 class="flex mb-3 text-base text-80 font-bold">
            {{ title }}
            <span class="ml-auto font-semibold text-70 text-sm">({{ formattedTotal}} total)</span>
        </h3>

        <div class="overflow-hidden overflow-y-scroll max-h-90px">
            <ul class="list-reset">
                <li v-for="item in formattedItems" class="text-xs text-80 leading-normal">
                    <span class="inline-block rounded-full w-2 h-2 mr-2" :style="{
                        backgroundColor: item.color
                    }"/>{{ item.label }} ({{item.value}} - {{ (item.value * 100 / formattedTotal).toFixed(2) }}%)
                </li>
            </ul>
        </div>

        <div
            ref="chart"
            class="z-40 vertical-center rounded-b-lg ct-chart"
            style="width: 90px; height: 90px; right: 20px; bottom: 30px; top: calc(50% + 15px);"
        />
    </loading-card>
</template>

<script>
import Chartist from 'chartist'
import 'chartist/dist/chartist.min.css'

const colorForIndex = index =>
    [
        '#F5573B',
        '#F99037',
        '#F2CB22',
        '#8FC15D',
        '#098F56',
        '#47C1BF',
        '#1693EB',
        '#6474D7',
        '#9C6ADE',
        '#E471DE',
    ][index]

export default {
    name: 'PartitionMetric',

    props: {
        loading: Boolean,
        title: String,
        chartData: Array,
    },

    data: () => ({ chartist: null }),

    watch: {
        chartData: function(newData, oldData) {
            this.renderChart()
        },
    },

    mounted() {
        this.chartist = new Chartist.Pie(this.$refs.chart, this.formattedChartData, {
            donut: true,
            donutWidth: 10,
            donutSolid: true,
            startAngle: 270,
            showLabel: false,
        })
    },

    methods: {
        renderChart() {
            this.chartist.update(this.formattedChartData)
        },
    },

    computed: {
        formattedChartData() {
            return { labels: this.formattedLabels, series: this.formattedData }
        },

        formattedItems() {
            return _(this.chartData)
                .map((item, index) => {
                    return {
                        label: item.label,
                        value: item.value,
                        color: colorForIndex(index),
                    }
                })
                .value()
        },

        formattedLabels() {
            return _(this.chartData)
                .map(item => item.label)
                .value()
        },

        formattedData() {
            return _(this.chartData)
                .map(item => item.value)
                .value()
        },

        formattedTotal() {
            return _.sumBy(this.chartData, 'value')
        },
    },
}
</script>

<style>
.ct-series-a .ct-area,
.ct-series-a .ct-slice-donut-solid,
.ct-series-a .ct-slice-pie {
    fill: #f5573b;
}
.ct-series-b .ct-area,
.ct-series-b .ct-slice-donut-solid,
.ct-series-b .ct-slice-pie {
    fill: #f99037;
}
.ct-series-c .ct-area,
.ct-series-c .ct-slice-donut-solid,
.ct-series-c .ct-slice-pie {
    fill: #f2cb22;
}
.ct-series-d .ct-area,
.ct-series-d .ct-slice-donut-solid,
.ct-series-d .ct-slice-pie {
    fill: #8fc15d;
}
.ct-series-e .ct-area,
.ct-series-e .ct-slice-donut-solid,
.ct-series-e .ct-slice-pie {
    fill: #098f56;
}
.ct-series-f .ct-area,
.ct-series-f .ct-slice-donut-solid,
.ct-series-f .ct-slice-pie {
    fill: #47c1bf;
}
.ct-series-g .ct-area,
.ct-series-g .ct-slice-donut-solid,
.ct-series-g .ct-slice-pie {
    fill: #1693eb;
}
.ct-series-h .ct-area,
.ct-series-h .ct-slice-donut-solid,
.ct-series-h .ct-slice-pie {
    fill: #6474d7;
}
.ct-series-i .ct-area,
.ct-series-i .ct-slice-donut-solid,
.ct-series-i .ct-slice-pie {
    fill: #9c6ade;
}
.ct-series-j .ct-area,
.ct-series-j .ct-slice-donut-solid,
.ct-series-j .ct-slice-pie {
    fill: #e471de;
}
</style>
