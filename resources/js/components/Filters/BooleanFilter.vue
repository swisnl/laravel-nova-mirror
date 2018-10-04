<template>
    <div>
        <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">
            {{ filter.name }}
        </h3>

        <label
            v-for="option in options"
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

    // watch: {
    //     value: function(newData, oldData) {
    //         console.log('watcher fired')
    //         this.hydrateOptions()
    //     },
    //     //     filter: function(newData, oldData) {
    //     //         this.hydrateOptions()
    //     //     },
    // },

    // data: () => ({ options: null }),

    // created() {
    //     this.hydrateOptions()

    //     // Nova.$on('clear-selected-filters', () => {
    //     //     console.log('clearing-selected-filters')
    //     //     //     // this.$nextTick(() => {
    //     //     // _(this.options).each(option => {
    //     //     //     console.log(option.checked)
    //     //     //     return (option.checked = false)
    //     //     //     console.log(option.checked)
    //     //     // })

    //     //     this.$emit(
    //     //         'input',
    //     //         _(this.options).map(option => {
    //     //             return {
    //     //                 ...option,
    //     //                 ...{ checked: false },
    //     //             }
    //     //         })
    //     //     )

    //     //     this.$emit('change')
    //     // })
    // },

    // methods: {
    //     hydrateOptions() {
    //         this.options = _.map(this.filter.options, option => {
    //             return {
    //                 name: option.name,
    //                 value: option.value,
    //                 checked: false,
    //             }
    //         })
    //     },

    //     updateCheckedState(event, key) {
    //         console.log(`Updating checked state for ${key}`)
    //         const option = _(this.options).find(option => option.value == key)

    //         option.checked = !option.checked

    //         this.$emit('input', this.options)

    //         this.$emit('change')
    //     },
    // },
}
</script>
