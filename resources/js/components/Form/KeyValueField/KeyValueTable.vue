<template>
    <div
        class="relative rounded-t-lg rounded-b-lg shadow bg-30 border border-60"
        :class="{ 'mr-11': editMode }"
        style="background-clip: border-box;"
    >
        <slot />
    </div>
</template>

<script>
export default {
    props: {
        editMode: {
            type: Boolean,
            default: true,
        },
    },
}
</script>
