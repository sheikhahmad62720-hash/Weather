<template>
    <div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-900/50 dark:bg-red-950/30" role="alert">
        <div class="flex items-start gap-3">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-600 dark:text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 8v5" /><path d="M12 16h.01" />
            </svg>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ message }}</p>
            </div>

            <button
                type="button"
                class="shrink-0 rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 transition-colors hover:bg-red-200 dark:bg-red-900/50 dark:text-red-200 dark:hover:bg-red-800/50"
                @click="$emit('retry')"
            >
                Retry
            </button>
        </div>
    </div>
</template>

<script setup>
defineProps({
    message: { type: String, required: true },
});

defineEmits(['retry']);
</script>