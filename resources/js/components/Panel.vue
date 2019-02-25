<template>
    <div v-if="panel.fields.length > 0">
        <slot>
            <h4 class="text-90 font-normal text-2xl mb-3">{{ panel.name }}</h4>
        </slot>

        <card class="mb-6 py-3 px-6">
            <slot name="panel" :fields="panel.fields"></slot>
        </card>
    </div>
</template>

<script>
import { BehavesAsPanel } from 'laravel-nova'

export default {
    mixins: [BehavesAsPanel],
}
</script>
