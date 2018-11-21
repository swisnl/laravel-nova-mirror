<template>
    <label
        class="flex items-center select-none"
        ref="label"
        @keydown.prevent.enter.space="$refs.label.click()"
    >
        <fake-checkbox :checked="checked" class="mr-2" />
        <input
            @change="$emit('change', $event)"
            type="checkbox"
            :disabled="disabled"
            :tabindex="disabled ? false : 0"
            :aria-checked="checked"
        />
        <slot />
    </label>
</template>

<script>
export default {
    props: {
        checked: Boolean,
        disabled: {
            type: Boolean,
            default: false,
        },
    },
}
</script>

<style>
input[type='checkbox'] {
    position: absolute;
    left: 0;
    opacity: 0;
    outline: none;
    z-index: -1;
}
</style>
