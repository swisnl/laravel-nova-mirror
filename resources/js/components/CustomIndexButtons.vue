<template>
    <div class="flex items-center">
        <dropdown
            direction="rtl"
            width="290"
            class="dropdown-alt mr-3"
            data-testid="filter-selector"
            :dark="false"
        >
            <a class="cursor-pointer dim block py-3 no-underline text-primary text-sm">
                Options
            </a>

            <div slot="menu">
                <ul class="list-reset">
                    <li class="border-b border-50">
                        <a href="#" class="block py-3 px-4 no-underline dim text-primary text-sm">
                            Dropdown Option #1
                        </a>
                    </li>
                    <li class="border-b border-50">
                        <a href="#" class="block py-3 px-4 no-underline dim text-primary text-sm">
                            Dropdown Option #1
                        </a>
                    </li>
                </ul>
            </div>
        </dropdown>

        <a href="#" class="btn btn-default btn-primary">
            Import Users
        </a>

        <a href="#" class="btn btn-default btn-primary ml-3">
            Export Users
        </a>
    </div>


 </template>
