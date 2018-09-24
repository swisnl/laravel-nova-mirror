<template>
    <panel-item :field="field">
        <template slot="value">
            <excerpt :content="field.value" :always-show="field.alwaysShow" />
        </template>
    </panel-item>
</template>

<script>
export default {
    props: ['resource', 'resourceName', 'resourceId', 'field'],
}
</script>
