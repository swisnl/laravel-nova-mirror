import Vue from 'vue'
import router from '@/router'
import axios from '@/util/axios'
import Loading from '@/components/Loading'

export default class Nova {
    constructor(config) {
        this.bus = new Vue()
        this.bootingCallbacks = []
        this.config = config
    }

    /**
     * Register a callback to be called before Nova starts. This is used to bootstrap
     * addons, tools, custom fields, or anything else Nova needs
     */
    booting(callback) {
        this.bootingCallbacks.push(callback)
    }

    /**
     * Execute all of the booting callbacks.
     */
    boot() {
        this.bootingCallbacks.forEach(callback => callback(Vue, router))

        this.bootingCallbacks = []
    }

    /**
     * Start the Nova app by calling each of the tool's callbacks and then creating
     * the underlying Vue instance.
     */
    liftOff() {
        let _this = this

        this.boot()

        this.app = new Vue({
            el: '#nova',
            router,
            components: { Loading },
            mounted: function() {
                this.$loading = this.$refs.loading

                _this.$on('error', message => {
                    this.$toasted.show(message, { type: 'error' })
                })
            },
        })
    }

    /**
     * Return an axios instance configured to make requests to Nova's API
     * and handle certain response codes.
     */
    request(options) {
        if (options !== undefined) {
            return axios(options)
        }

        return axios
    }

    /**
     * Register a listener on Nova's built-in event bus
     */
    $on(...args) {
        this.bus.$on(...args)
    }

    /**
     * Register a one-time listener on the event bus
     */
    $once(...args) {
        this.bus.$once(...args)
    }

    /**
     * De-register a listener on the event bus
     */
    $off(...args) {
        this.bus.$off(...args)
    }

    /**
     * Emit an event on the event bus
     */
    $emit(...args) {
        this.bus.$emit(...args)
    }
}
