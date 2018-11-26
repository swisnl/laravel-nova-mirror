<template>
    <button class="btn btn-default btn-primary inline-flex items-center" type="button">
        <span>
            <slot />
        </span>

        <span v-if="processing" class="ml-4">
            <loader class="text-white" width="32" />
        </span>
    </button>
</template>

<script>
export default {
    props: {
        processing: {
            type: Boolean,
            default: false,
        },
    },
}
</script>
