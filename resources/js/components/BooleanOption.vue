<template>
    <label class="flex items-center m-2">
        <div class="flex-no-shrink">
            <Checkbox 
                :checked="isChecked"
                @input="updateCheckedState(option.value, $event)" 
            />
        </div>

        <div class="ml-2">
            {{ option.name }}
        </div>
    </label>
</template>

<script>
// import Checkbox from '@/components/Checkbox'
import Checkbox from '@/components/Index/Checkbox'

export default {
    components: { Checkbox },

    props: {
        filter: Object,
        option: Object,
    },

    methods: {
        updateCheckedState(optionKey, value) {
            let oldValue = this.filter.currentValue
            let newValue = { ...oldValue, [optionKey]: value }

            this.$store.commit('updateFilterState', {
                filterClass: this.filter.class,
                value: newValue,
            })

            this.$emit('change')
        },
    },

    computed: {
        isChecked() {
            return (
                this.$store.getters.filterOptionValue(this.filter.class, this.option.value) == true
            )
        },
    },
}
</script>
