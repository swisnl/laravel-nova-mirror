<template>
    <div class="my-2">
        <label
            v-for="option in filter.options"
            class="flex items-center m-2"
        >
            <div class="flex-no-shrink">
                <Checkbox
                    v-model="option.checked"
                    @input="updateCheckedState($event, option.value)"
                />
            </div>

            <div class="ml-2">
                {{ option.name }}
            </div>
        </label>
    </div>
</template>

<script>
import Checkbox from '@/components/Checkbox'

export default {
    components: { Checkbox },

    props: {
        filter: {
            type: Object,
            required: true,
        },

        value: {},
    },

    data: () => ({ options: null }),

    created() {
        this.options = _.map(this.filter.options, option => {
            return {
                name: option.name,
                value: option.value,
                checked: false,
            }
        })
    },

    methods: {
        updateCheckedState(event, key) {
            const option = _(this.options).find(option => option.value == key)

            option.checked = !option.checked

            this.$emit('input', this.options)

            this.$emit('change')
        },
    },
}
</script>
