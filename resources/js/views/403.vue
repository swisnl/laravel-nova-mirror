<template>
    <div class="absolute pin bg-white z-50 flex items-center justify-center">
        <div>
            <heading class="text-center text-4xl mb-3">403</heading>
            <p class="text-center text-2xl mb-6">Forbidden.</p>

            <router-link :to="{ name: 'dashboard'}" class="btn btn-default btn-primary">&larr; Head back</router-link>
        </div>
    </div>
</template>
