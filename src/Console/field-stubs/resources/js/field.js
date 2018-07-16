Nova.booting((Vue, router) => {
    Vue.component('index-{{ component }}', require('./components/IndexField'));
    Vue.component('detail-{{ component }}', require('./components/DetailField'));
    Vue.component('form-{{ component }}', require('./components/FormField'));
})
