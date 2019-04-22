<template>
    <div v-if="isNotObject" class="flex items-center">
        <div class="flex flex-grow border-b border-50">
            <div class="w-48 cursor-text">
                <textarea
                    :dusk="`key-value-key-${index}`"
                    v-model="item.key"
                    @focus="handleKeyFieldFocus"
                    ref="keyField"
                    type="text"
                    class="font-mono text-sm resize-none block min-h-input w-full form-control form-input form-input-row py-4"
                    :disabled="disabled"
                    :class="{
                        '!bg-white': disabled,
                        'hover:bg-20 focus:bg-white': !disabled,
                    }"
                />
            </div>

            <div @click="handleValueFieldFocus" class="flex-grow border-l border-50">
                <textarea
                    :dusk="`key-value-value-${index}`"
                    v-model="item.value"
                    @focus="handleValueFieldFocus"
                    ref="valueField"
                    type="text"
                    class="font-mono text-sm hover:bg-20 focus:bg-white block min-h-input w-full form-control form-input form-input-row py-4"
                    :disabled="disabled"
                    :class="{
                        '!bg-white': disabled,
                        'hover:bg-20 focus:bg-white': !disabled,
                    }"
                />
            </div>
        </div>

        <div v-if="!disabled" class="flex justify-center h-11 w-11 absolute" style="right: -50px">
            <button
                @click="$emit('remove-row', item.id)"
                type="button"
                tabindex="-1"
                class="flex appearance-none cursor-pointer text-70 hover:text-primary"
                title="Delete"
            >
                <icon />
            </button>
        </div>
    </div>
</template>

<script>
import autosize from 'autosize'

export default {
    props: {
        index: Number,
        item: Object,
        disabled: {
            type: Boolean,
            default: false,
        },
    },

    mounted() {
        autosize(this.$refs.keyField)
        autosize(this.$refs.valueField)
    },

    methods: {
        handleKeyFieldFocus() {
            this.$refs.keyField.select()
        },

        handleValueFieldFocus() {
            this.$refs.valueField.select()
        },
    },

    computed: {
        isNotObject() {
            return !(this.item.value instanceof Object)
        },
    },
}
</script>
