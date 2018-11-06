<template>
    <div>
        <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">{{ filter.name }}</h3>

       <BooleanOption 
            key="option.value" 
            v-for="option in options" 
            :filter="filter" 
            :option="option" 
            @change="handleChange"
        />
    </div>
</template>

<script>
import BooleanOption from '@/components/BooleanOption.vue'
// import { mapGetters, mapMutations, mapActions } from 'vuex'
// import Checkbox from '@/components/Checkbox'

export default {
    components: { BooleanOption },

    props: {
        filterKey: {
            type: String,
            required: true,
        },
    },

    mounted() {
        let initialState = {}

        _.each(this.filter.options, o => (initialState[o.value] = ''))

        this.$store.commit('updateFilterState', {
            filterClass: this.filterKey,
            value: initialState,
        })
    },

    methods: {
        handleChange() {
            this.$emit('change')
        },
    },

    computed: {
        filter() {
            return this.$store.getters.getFilter(this.filterKey)
        },

        options() {
            return this.$store.getters.getOptionsForFilter(this.filterKey)
        },
    },
}
</script>
