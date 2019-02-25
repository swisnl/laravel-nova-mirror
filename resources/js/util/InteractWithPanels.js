export default {
    data: () => ({
        panels: [],
    }),

    methods: {
        /**
         * Create a new panel for the given field.
         */
        createPanelForField(field) {
            return _.tap(_.find(this.panels, panel => panel.name == field.panel), panel => {
                panel.fields = [field]
            })
        },

        /**
         * Create a new panel for the given relationship field.
         */
        createPanelForRelationship(field) {
            return {
                component: 'relationship-panel',
                prefixComponent: true,
                name: field.name,
                fields: [field],
            }
        },
    },

    computed: {
        /**
         * Get the available field panels.
         */
        availablePanels() {
            let panels = {}

            _.toArray(this.fields).forEach(field => {
                if (field.listable) {
                    return (panels[field.name] = this.createPanelForRelationship(field))
                } else if (panels[field.panel]) {
                    return panels[field.panel].fields.push(field)
                }

                panels[field.panel] = this.createPanelForField(field)
            })

            return _.toArray(panels)
        },
    },
}
