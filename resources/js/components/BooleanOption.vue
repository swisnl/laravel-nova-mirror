<template>
    <div>
        <checkbox-with-label
            class="m-2"
            :checked="isChecked"
            @change="updateCheckedState(option.value, $event)"
        >
            {{ option.name }}
        </checkbox-with-label>
    </div>
</template>

<script>
import Checkbox from '@/components/Index/Checkbox'

export default {
    components: { Checkbox },

    props: {
        resourceName: {
            type: String,
            required: true,
        },
        filter: Object,
        option: Object,
    },

    methods: {
        updateCheckedState(optionKey, event) {
            let oldValue = this.filter.currentValue
            let newValue = { ...oldValue, [optionKey]: event.target.checked }

            this.$store.commit(`${this.resourceName}/updateFilterState`, {
                filterClass: this.filter.class,
                value: newValue,
            })

            this.$emit('change')
        },
    },

    computed: {
        isChecked() {
            return (
                this.$store.getters[`${this.resourceName}/filterOptionValue`](
                    this.filter.class,
                    this.option.value
                ) == true
            )
        },
    },
}
</script>
