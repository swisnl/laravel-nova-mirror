<template>
    <div class="dropdown-menu absolute z-50 select-none" :class="menuClasses">
        <div
            :style="styles"
            class="z-40 overflow-hidden bg-white border border-60 shadow rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>

<script>
export default {
    props: {
        direction: {
            type: String,
            default: 'ltr',
            validator: value => ['ltr', 'rtl'].indexOf(value) != -1,
        },
        width: {
            default: 120,
        },
    },
    computed: {
        menuClasses() {
            return [this.direction == 'ltr' ? 'dropdown-menu-left' : 'dropdown-menu-right']
        },
        arrowClasses() {
            return [this.direction == 'ltr' ? 'dropdown-arrow-left' : 'dropdown-arrow-right']
        },
        styles() {
            return {
                width: `${this.width}px`,
            }
        },
    },
}
</script>
