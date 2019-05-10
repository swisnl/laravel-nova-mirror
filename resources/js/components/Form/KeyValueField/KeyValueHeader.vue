<template>
    <div class="flex border-b border-50">
        <div class="w-48 uppercase font-bold text-xs text-80 tracking-wide px-3 py-3">
            {{ keyLabel }}
        </div>

        <div
            class="flex-grow uppercase font-bold text-xs text-80 tracking-wide px-3 py-3 border-l border-50"
        >
            {{ valueLabel }}
        </div>
    </div>
</template>

<script>
export default {
    props: {
        keyLabel: {
            type: String,
        },
        valueLabel: {
            type: String,
        },
    },
}
</script>
