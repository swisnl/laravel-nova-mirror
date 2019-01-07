<template>
    <modal
        data-testid="confirm-action-modal"
        tabindex="-1"
        role="dialog"
        @modal-close="handleClose"
    >
        <form
            autocomplete="off"
            @keydown="handleKeydown"
            @submit.prevent.stop="handleConfirm"
            class="bg-white rounded-lg shadow-lg overflow-hidden"
            :class="{
                'w-action-fields': selectedAction.fields.length > 0,
                'w-action': selectedAction.fields.length == 0,
            }"
        >
            <div>
                <heading :level="2" class="pt-8 px-8">{{ selectedAction.name }}</heading>

                <p v-if="selectedAction.fields.length == 0" class="text-80 px-8 my-8">
                    {{ __('Are you sure you want to run this action?') }}
                </p>

                <div v-else>
                    <!-- Validation Errors -->
                    <validation-errors :errors="errors" />

                    <!-- Action Fields -->
                    <div
                        class="action"
                        v-for="field in selectedAction.fields"
                        :key="field.attribute"
                    >
                        <component
                            :is="'form-' + field.component"
                            :errors="errors"
                            :resource-name="resourceName"
                            :field="field"
                        />
                    </div>
                </div>
            </div>

            <div class="bg-30 px-6 py-3 flex">
                <div class="flex items-center ml-auto">
                    <button
                        dusk="cancel-action-button"
                        type="button"
                        @click.prevent="handleClose"
                        class="btn text-80 font-normal h-9 px-3 mr-3 btn-link"
                    >
                        {{ __('Cancel') }}
                    </button>

                    <button
                        ref="runButton"
                        dusk="confirm-action-button"
                        :disabled="working"
                        type="submit"
                        class="btn btn-default"
                        :class="{
                            'btn-primary': !selectedAction.destructive,
                            'btn-danger': selectedAction.destructive,
                        }"
                    >
                        <loader v-if="working" width="30"></loader>
                        <span v-else>{{ __('Run Action') }}</span>
                    </button>
                </div>
            </div>
        </form>
    </modal>
</template>

<script>
import { Errors } from 'laravel-nova'
import composedPath from '@/polyfills/composedPath'

export default {
    props: {
        working: Boolean,
        resourceName: {},
        selectedAction: {},
        errors: { required: true },
    },

    /**
     * Mount the component.
     */
    mounted() {
        // If the modal has inputs, let's highlight the first one, otherwise
        // let's highlight the submit button
        if (document.querySelectorAll('.modal input').length) {
            document.querySelectorAll('.modal input')[0].focus()
        } else {
            this.$refs.runButton.focus()
        }
    },

    methods: {
        /**
         * Stop propogation of input events unless it's for an escape or enter keypress
         */
        handleKeydown(e) {
            if (['Escape', 'Enter'].indexOf(e.key) !== -1) {
                return
            }

            e.stopPropagation()
        },

        /**
         * Execute the selected action.
         */
        handleConfirm(e) {
            this.$emit('confirm')
        },

        /**
         * Close the modal.
         */
        handleClose(e) {
            let classArray = ['flatpickr-calendar']

            if (_.filter(classArray, className => pathIncludesClass(e, className)).length > 0) {
                return
            }

            this.$emit('close')
        },
    },
}

function pathIncludesClass(event, className) {
    return composedPath(event)
        .filter(el => el !== document && el !== window)
        .reduce((acc, e) => acc.concat([...e.classList]), [])
        .includes(className)
}
</script>
