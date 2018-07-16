<template>
    <panel-item :field="field">
        <template slot="value">
            <excerpt :content="field.value" />
        </template>
    </panel-item>
</template>

<script>
import Excerpt from '../Excerpt'

export default {
    props: ['resource', 'resourceName', 'resourceId', 'field'],

    components: { Excerpt },
}
</script>
