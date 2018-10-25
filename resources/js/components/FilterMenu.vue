<template>
    <dropdown
        v-if="filters.length > 0 || softDeletes || !viaResource"
        dusk="filter-selector"
        class="bg-30 hover:bg-40 rounded"
    >
        <dropdown-trigger slot-scope="{toggle}" :handle-click="toggle" class="px-3">
            <icon type="filter" class="text-80" />
        </dropdown-trigger>

        <dropdown-menu slot="menu" width="290" direction="rtl" :dark="true">
            <scroll-wrap :height="350">
                <div class="bg-30 border-b border-60 ">
                    <button
                        @click="clearSelectedFilters"
                        class="py-2 w-full block text-xs uppercase tracking-wide text-center text-80 dim font-bold focus:outline-none"
                    >
                        {{ __('&times; Clear Filters') }}
                    </button>
                </div>

                <!-- Custom Filters -->
                <filter-selector
                    :filters="filters"
                    :current-filters="currentFilters"
                    @changed="filterChanged"
                    v-if="! viaHasOne">
                </filter-selector>

                <!-- Soft Deletes -->
                <filter-select v-if="softDeletes && showTrashedOption">
                    <h3 slot="default" class="text-sm uppercase tracking-wide text-80 bg-30 p-3">
                        {{__('Trashed')}}:
                    </h3>

                    <select slot="select"
                        class="block w-full form-control-sm form-select"
                        dusk="trashed-select"
                        :value="trashed"
                        @change="trashedChanged"
                    >
                        <option value="" selected>&mdash;</option>
                        <option value="with">{{__('With Trashed')}}</option>
                        <option value="only">{{__('Only Trashed')}}</option>
                    </select>
                </filter-select>

                <!-- Per Page -->
                <filter-select v-if="!viaResource">
                    <h3 slot="default" class="text-sm uppercase tracking-wide text-80 bg-30 p-3">
                        {{__('Per Page')}}
                    </h3>

                    <select slot="select"
                        dusk="per-page-select"
                        class="block w-full form-control-sm form-select"
                        :value="perPage"
                        @change="perPageChanged"
                    >
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </filter-select>
            </scroll-wrap>
        </dropdown-menu>
    </dropdown>
</template>

<script>
export default {
    props: {
        filters: Array,
        currentFilters: Array,
        softDeletes: Boolean,
        viaResource: String,
        viaHasOne: Boolean,
        softDeletes: Boolean,
        trashed: {
            type: String,
            validator: value => ['', 'with', 'only'].indexOf(value) != -1,
        },
        perPage: [String, Number],
        showTrashedOption: {
            type: Boolean,
            default: true,
        },
    },

    methods: {
        clearSelectedFilters() {
            this.$emit('clear-selected-filters')
        },

        filterChanged(newFilters) {
            this.$emit('filter-changed', newFilters)
        },

        trashedChanged(event) {
            this.$emit('trashed-changed', event.target.value)
        },

        perPageChanged(event) {
            this.$emit('per-page-changed', event.target.value)
        },
    },
}
</script>
