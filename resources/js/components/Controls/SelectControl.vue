<template>
    <select v-on="$listeners" v-bind="$attrs">
        <slot/>
        <template v-for="(options, group) in groupedOptions">
            <optgroup :label="group" v-if="group">
                <option
                        v-for="option in groupedOptions"
                        v-bind="extraAttrs(option)"
                >{{ labelFor(option) }}</option>
            </optgroup>
            <template v-else>
                <option
                        v-for="option in options"
                        v-bind="extraAttrs(option)"
                >{{ labelFor(option) }}</option>
            </template>
        </template>
    </select>
</template>

<script>
    export default {
        props: {
            options: {
                default: []
            },
            selected: {},
            label: {
                default: 'label'
            },
            value: {
                default: 'value'
            }
        },
        computed: {
            groupedOptions() {
                return _.groupBy(this.options, option => option.group || '')
            }
        },
        methods: {
            labelFor(option) {
                return this.label instanceof Function ? this.label(option) : option[this.label]
            },

            valueFor(option) {
                return this.value instanceof Function ? this.value(option) : option[this.value]
            },

            extraAttrs(option) {
                let attrs = option.extraAttrs || {};
                const value = this.valueFor(option);
                attrs.value = value;

                if (this.selected !== void 0) {
                    attrs['selected'] = this.selected == value;
                }

                return attrs
            }
        }
    }
</script>