<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-gradient-to-br from-[#f8fafc] via-white to-brand-primary/10 p-4 dark:from-gray-950 dark:via-brand-dark dark:to-gray-950"
    >
        <div class="mb-8">
            <Link href="/" class="flex flex-col items-center gap-3">
                <div class="p-4 bg-brand-dark rounded-3xl shadow-xl border border-brand-dark/50">
                    <img src="/images/logos/logo-icon-white.png" alt="Contratamos" class="h-12 w-auto" />
                </div>
                <span class="text-3xl font-extrabold text-brand-dark dark:text-white tracking-tight">CONTRATAMOS</span>
            </Link>
        </div>

        <div
            class="w-full overflow-hidden bg-white/70 backdrop-blur-md px-8 py-10 shadow-2xl shadow-brand-dark/5 sm:max-w-md sm:rounded-3xl border border-white dark:bg-gray-800/80 dark:border-gray-700"
        >
            <slot />
        </div>
    </div>
</template>
