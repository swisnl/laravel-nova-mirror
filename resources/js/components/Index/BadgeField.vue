<template>
    <div>
        <badge :label="field.label" :extra-classes="field.typeClass" />
    </div>
</template>

<script>
import Badge from '../badge'

export default {
    components: {
        Badge,
    },

    props: ['resourceName', 'viaResource', 'viaResourceId', 'field'],
}
</script>
