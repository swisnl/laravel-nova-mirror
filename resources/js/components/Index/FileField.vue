<template>
    <p>
        <img v-if="field.thumbnailUrl" :src="field.thumbnailUrl" class="rounded-full w-8 h-auto min-w-8" />
        <span v-else>&mdash;</span>
    </p>
</template>

<script>
export default {
    props: ['viaResource', 'viaResourceId', 'resourceName', 'field'],
}
</script>
