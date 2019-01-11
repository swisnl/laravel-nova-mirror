<template>
    <div class="flex justify-center items-centers">
        <div class="w-full max-w-xl">
            <heading class="flex mb-3">Get Started</heading>
            <p class="text-90 leading-tight mb-8">
                Welcome to Nova! Get familiar with Nova and explore it's features in the
                documentation:
            </p>

            <card>
                <table class="w-full" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="align-top w-1/2 border-r border-b border-50">
                            <a :href="resources" class="no-underline dim flex p-6">
                                <div class="flex justify-center w-11 flex-no-shrink mr-6">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="40"
                                        height="40"
                                        viewBox="0 0 40 40"
                                    >
                                        <g id="Page-1" fill="none" fill-rule="evenodd">
                                            <g
                                                id="icon-resources"
                                                fill="var(--primary)"
                                                fill-rule="nonzero"
                                                transform="translate(0 -1)"
                                            >
                                                <path
                                                    id="Shape"
                                                    d="M31.51 26.86l7.32 7.31c1.0110617 1.0110616 1.4059262 2.4847161 1.035852 3.865852-.3700742 1.3811359-1.4488641 2.4599258-2.83 2.83-1.3811359.3700742-2.8547904-.0247903-3.865852-1.035852l-7.31-7.32c-7.3497931 4.4833975-16.89094893 2.7645226-22.21403734-4.0019419-5.3230884-6.7664645-4.74742381-16.4441086 1.34028151-22.53181393C11.0739495-.11146115 20.7515936-.68712574 27.5180581 4.63596266 34.2845226 9.95905107 36.0033975 19.5002069 31.52 26.85l-.01.01zm-3.99 4.5l7.07 7.05c.7935206.6795536 1.9763883.6338645 2.7151264-.1048736.7387381-.7387381.7844272-1.9216058.1048736-2.7151264l-7.06-7.07c-.8293081 1.0508547-1.7791453 2.0006919-2.83 2.83v.01zM17 33c8.2842712 0 15-6.7157288 15-15 0-8.28427125-6.7157288-15-15-15C8.71572875 3 2 9.71572875 2 18c0 8.2842712 6.71572875 15 15 15zm0-2C9.82029825 31 4 25.1797017 4 18S9.82029825 5 17 5c7.1797017 0 13 5.8202983 13 13s-5.8202983 13-13 13zm0-2c6.0751322 0 11-4.9248678 11-11S23.0751322 7 17 7 6 11.9248678 6 18s4.9248678 11 11 11z"
                                                />
                                            </g>
                                        </g>
                                    </svg>
                                </div>

                                <div>
                                    <heading :level="3" class="mb-3">Resources</heading>
                                    <p class="text-90 leading-normal">
                                        Nova's resource manager allows you to quickly view and
                                        manage your Eloquent model records directly from Nova's
                                        intuitive interface.
                                    </p>
                                </div>
                            </a>
                        </td>

                        <td class="align-top w-1/2 border-b border-50">
                            <a :href="actions" class="no-underline dim flex p-6">
                                <div class="flex justify-center w-11 flex-no-shrink mr-6">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="44"
                                        height="44"
                                        viewBox="0 0 44 44"
                                    >
                                        <g id="Page-1" fill="none" fill-rule="evenodd">
                                            <g
                                                id="icon-actions"
                                                fill="var(--primary)"
                                                fill-rule="nonzero"
                                            >
                                                <path
                                                    id="Shape"
                                                    d="M22 44C9.8497355 44 0 34.1502645 0 22S9.8497355 0 22 0s22 9.8497355 22 22-9.8497355 22-22 22zm0-2c11.045695 0 20-8.954305 20-20S33.045695 2 22 2 2 10.954305 2 22s8.954305 20 20 20zm3-24h5c.3638839-.0007291.6994429.1962627.8761609.5143551.176718.3180924.1666987.707072-.0261609 1.0156449l-10 16C20.32 36.38 19 36 19 35v-9h-5c-.3638839.0007291-.6994429-.1962627-.8761609-.5143551-.176718-.3180924-.1666987-.707072.0261609-1.0156449l10-16C23.68 7.62 25 8 25 9v9zm3.2 2H24c-.5522847 0-1-.4477153-1-1v-6.51L15.8 24H20c.5522847 0 1 .4477153 1 1v6.51L28.2 20z"
                                                />
                                            </g>
                                        </g>
                                    </svg>
                                </div>

                                <div>
                                    <heading :level="3" class="mb-3">Actions</heading>
                                    <p class="text-90 leading-normal">
                                        Actions perform tasks on a single record or an entire batch
                                        of records. Have an action that takes a while? No problem.
                                        Nova can queue them using Laravel's powerful queue system.
                                    </p>
                                </div>
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td class="align-top w-1/2 border-r border-b border-50">
                            <a :href="filters" class="no-underline dim flex p-6">
                                <div class="flex justify-center w-11 flex-no-shrink mr-6">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="38"
                                        height="38"
                                        viewBox="0 0 38 38"
                                    >
                                        <g id="Page-1" fill="none" fill-rule="evenodd">
                                            <g
                                                id="icon-filters"
                                                fill="var(--primary)"
                                                fill-rule="nonzero"
                                            >
                                                <path
                                                    id="Shape"
                                                    d="M36 4V2H2v6.59l13.7 13.7c.1884143.1846305.296243.4362307.3.7v11.6l6-6v-5.6c.003757-.2637693.1115857-.5153695.3-.7L36 8.6V6H19c-.5522847 0-1-.44771525-1-1s.4477153-1 1-1h17zM.3 9.7C.11158574 9.51536954.00375705 9.26376927 0 9V1c0-.55228475.44771525-1 1-1h36c.5522847 0 1 .44771525 1 1v8c-.003757.26376927-.1115857.51536954-.3.7L24 23.42V29c-.003757.2637693-.1115857.5153695-.3.7l-8 8c-.2857003.2801197-.7108712.3629755-1.0808485.210632C14.2491743 37.7582884 14.0056201 37.4000752 14 37V23.4L.3 9.71V9.7z"
                                                />
                                            </g>
                                        </g>
                                    </svg>
                                </div>

                                <div>
                                    <heading :level="3" class="mb-3">Filters</heading>
                                    <p class="text-90 leading-normal">
                                        Write custom filters for your resource indexes to offer your
                                        users quick glances at different segments of your data.
                                    </p>
                                </div>
                            </a>
                        </td>

                        <td class="align-top w-1/2 border-b border-50">
                            <a :href="lenses" class="no-underline dim flex p-6">
                                <div class="flex justify-center w-11 flex-no-shrink mr-6">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="36"
                                        height="36"
                                        viewBox="0 0 36 36"
                                    >
                                        <g id="Page-1" fill="none" fill-rule="evenodd">
                                            <g
                                                id="icon-lenses"
                                                fill="var(--primary)"
                                                fill-rule="nonzero"
                                            >
                                                <path
                                                    id="Shape"
                                                    d="M4 8C1.790861 8 0 6.209139 0 4s1.790861-4 4-4 4 1.790861 4 4-1.790861 4-4 4zm0-2c1.1045695 0 2-.8954305 2-2s-.8954305-2-2-2-2 .8954305-2 2 .8954305 2 2 2zm0 16c-2.209139 0-4-1.790861-4-4s1.790861-4 4-4 4 1.790861 4 4-1.790861 4-4 4zm0-2c1.1045695 0 2-.8954305 2-2s-.8954305-2-2-2-2 .8954305-2 2 .8954305 2 2 2zm0 16c-2.209139 0-4-1.790861-4-4s1.790861-4 4-4 4 1.790861 4 4-1.790861 4-4 4zm0-2c1.1045695 0 2-.8954305 2-2s-.8954305-2-2-2-2 .8954305-2 2 .8954305 2 2 2zm9-31h22c.5522847 0 1 .44771525 1 1s-.4477153 1-1 1H13c-.5522847 0-1-.44771525-1-1s.4477153-1 1-1zm0 14h22c.5522847 0 1 .4477153 1 1s-.4477153 1-1 1H13c-.5522847 0-1-.4477153-1-1s.4477153-1 1-1zm0 14h22c.5522847 0 1 .4477153 1 1s-.4477153 1-1 1H13c-.5522847 0-1-.4477153-1-1s.4477153-1 1-1z"
                                                />
                                            </g>
                                        </g>
                                    </svg>
                                </div>

                                <div>
                                    <heading :level="3" class="mb-3">Lenses</heading>
                                    <p class="text-90 leading-normal">
                                        Need to customize a resource list a little more than a
                                        filter can provider? No problem. Add lenses to your resource
                                        to take full control over the entire Eloquent query.
                                    </p>
                                </div>
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td class="align-top w-1/2 border-r border-b border-50">
                            <a :href="metrics" class="no-underline dim flex p-6">
                                <div class="flex justify-center w-11 flex-no-shrink mr-6">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="37"
                                        height="36"
                                        viewBox="0 0 37 36"
                                    >
                                        <g id="Page-1" fill="none" fill-rule="evenodd">
                                            <g
                                                id="icon-metrics"
                                                fill="var(--primary)"
                                                fill-rule="nonzero"
                                            >
                                                <path
                                                    id="Shape"
                                                    d="M2 27h3c1.1045695 0 2 .8954305 2 2v5c0 1.1045695-.8954305 2-2 2H2c-1.1045695 0-2-.8954305-2-2v-5c0-1.1.9-2 2-2zm0 2v5h3v-5H2zm10-11h3c1.1045695 0 2 .8954305 2 2v14c0 1.1045695-.8954305 2-2 2h-3c-1.1045695 0-2-.8954305-2-2V20c0-1.1.9-2 2-2zm0 2v14h3V20h-3zM22 9h3c1.1045695 0 2 .8954305 2 2v23c0 1.1045695-.8954305 2-2 2h-3c-1.1045695 0-2-.8954305-2-2V11c0-1.1.9-2 2-2zm0 2v23h3V11h-3zM32 0h3c1.1045695 0 2 .8954305 2 2v32c0 1.1045695-.8954305 2-2 2h-3c-1.1045695 0-2-.8954305-2-2V2c0-1.1.9-2 2-2zm0 2v32h3V2h-3z"
                                                />
                                            </g>
                                        </g>
                                    </svg>
                                </div>

                                <div>
                                    <heading :level="3" class="mb-3">Metrics</heading>
                                    <p class="text-90 leading-normal">
                                        Nova makes it painless to quickly display custom metrics for
                                        your application. To put the cherry on top, we’ve included
                                        query helpers to make it all easy as pie.
                                    </p>
                                </div>
                            </a>
                        </td>

                        <td class="align-top w-1/2 border-b border-50">
                            <a :href="cards" class="no-underline dim flex p-6">
                                <div class="flex justify-center w-11 flex-no-shrink mr-6">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="36"
                                        height="36"
                                        viewBox="0 0 36 36"
                                    >
                                        <g id="Page-1" fill="none" fill-rule="evenodd">
                                            <g
                                                id="icon-cards"
                                                fill="var(--primary)"
                                                fill-rule="nonzero"
                                            >
                                                <path
                                                    id="Shape"
                                                    d="M29 7h5c.5522847 0 1 .44771525 1 1s-.4477153 1-1 1h-5v5c0 .5522847-.4477153 1-1 1s-1-.4477153-1-1V9h-5c-.5522847 0-1-.44771525-1-1s.4477153-1 1-1h5V2c0-.55228475.4477153-1 1-1s1 .44771525 1 1v5zM4 0h8c2.209139 0 4 1.790861 4 4v8c0 2.209139-1.790861 4-4 4H4c-2.209139 0-4-1.790861-4-4V4c0-2.209139 1.790861-4 4-4zm0 2c-1.1045695 0-2 .8954305-2 2v8c0 1.1.9 2 2 2h8c1.1045695 0 2-.8954305 2-2V4c0-1.1045695-.8954305-2-2-2H4zm20 18h8c2.209139 0 4 1.790861 4 4v8c0 2.209139-1.790861 4-4 4h-8c-2.209139 0-4-1.790861-4-4v-8c0-2.209139 1.790861-4 4-4zm0 2c-1.1045695 0-2 .8954305-2 2v8c0 1.1.9 2 2 2h8c1.1045695 0 2-.8954305 2-2v-8c0-1.1045695-.8954305-2-2-2h-8zM4 20h8c2.209139 0 4 1.790861 4 4v8c0 2.209139-1.790861 4-4 4H4c-2.209139 0-4-1.790861-4-4v-8c0-2.209139 1.790861-4 4-4zm0 2c-1.1045695 0-2 .8954305-2 2v8c0 1.1.9 2 2 2h8c1.1045695 0 2-.8954305 2-2v-8c0-1.1045695-.8954305-2-2-2H4z"
                                                />
                                            </g>
                                        </g>
                                    </svg>
                                </div>

                                <div>
                                    <heading :level="3" class="mb-3">Cards</heading>
                                    <p class="text-90 leading-normal">
                                        Nova offers CLI generators for scaffolding your own custom
                                        cards. We’ll give you a Vue component and infinite
                                        possibilities.
                                    </p>
                                </div>
                            </a>
                        </td>
                    </tr>
                </table>
            </card>
        </div>
    </div>
</template>

<script>
function link(path) {
    return `https://nova.laravel.com/docs/1.0/${path}`
}

export default {
    name: 'Help',

    props: {
        card: Object,
    },

    computed: {
        resources: () => link('resources'),
        actions: () => link('actions/defining-actions.html'),
        filters: () => link('filters/defining-filters.html'),
        lenses: () => link('lenses/defining-lenses.html'),
        metrics: () => link('metrics/defining-metrics.html'),
        cards: () => link('customization/cards.html'),
    },
}
</script>
