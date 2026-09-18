<template>
    <div aria-busy="true" class="animate-pulse">
        <div class="border-b border-stone-200 pb-8 dark:border-stone-800">
            <div class="h-8 w-56 rounded bg-stone-200 dark:bg-stone-800" />
            <div class="mt-2 h-4 w-72 rounded bg-stone-200 dark:bg-stone-800" />
            <div class="mt-8 flex flex-wrap items-center gap-8">
                <div class="h-20 w-44 rounded bg-stone-200 dark:bg-stone-800" />
                <div class="space-y-2">
                    <div class="h-4 w-24 rounded bg-stone-200 dark:bg-stone-800" />
                    <div class="h-4 w-16 rounded bg-stone-200 dark:bg-stone-800" />
                </div>
            </div>
        </div>

        <div class="border-b border-stone-200 py-8 dark:border-stone-800">
            <div class="h-3 w-24 rounded bg-stone-200 dark:bg-stone-800" />
            <div class="mt-5 grid grid-cols-2 gap-px overflow-hidden rounded-lg border border-stone-200 bg-stone-200 sm:grid-cols-3 dark:border-stone-800 dark:bg-stone-800">
                <div v-for="index in 9" :key="index" class="flex h-20 flex-col justify-between bg-white px-4 py-4 dark:bg-stone-900">
                    <div class="h-3 w-16 rounded bg-stone-200 dark:bg-stone-800" />
                    <div class="h-4 w-12 rounded bg-stone-200 dark:bg-stone-800" />
                </div>
            </div>
        </div>

        <div class="border-b border-stone-200 py-8 dark:border-stone-800">
            <div class="h-3 w-24 rounded bg-stone-200 dark:bg-stone-800" />
            <div class="mt-5 flex gap-4 overflow-hidden">
                <div v-for="index in 6" :key="index" class="flex h-24 w-20 shrink-0 flex-col items-center justify-between rounded-md bg-stone-200 py-3 dark:bg-stone-800" />
            </div>
        </div>

        <div class="py-8">
            <div class="h-3 w-24 rounded bg-stone-200 dark:bg-stone-800" />
            <div class="mt-2 divide-y divide-stone-100 dark:divide-stone-800">
                <div v-for="index in 5" :key="index" class="flex items-center gap-3 py-3">
                    <div class="h-4 w-16 rounded bg-stone-200 dark:bg-stone-800" />
                    <div class="h-6 w-6 rounded bg-stone-200 dark:bg-stone-800" />
                    <div class="ml-auto h-4 w-20 rounded bg-stone-200 dark:bg-stone-800" />
                </div>
            </div>
        </div>
    </div>
</template>