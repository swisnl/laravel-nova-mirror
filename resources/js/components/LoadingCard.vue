<template>
    <card class="relative">
        <div v-if="loading" class="rounded-lg flex items-center justify-center absolute pin bg-white z-50">
            <loader class="text-60" />
        </div>

        <slot />
    </card>
</template>

<script>
export default {
    props: {
        loading: {
            type: Boolean,
            default: true,
        },
    },
}
</script>
