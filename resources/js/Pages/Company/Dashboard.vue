<script setup>
import CompanyLayout from '@/Layouts/CompanyLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    activeVacanciesCount: Number,
});
</script>

<template>
    <CompanyLayout title="Dashboard de Empresa">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Panel de Empresa
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <!-- Tarjeta: Vacantes Activas -->
                    <div class="relative overflow-hidden bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-xl border-b-4 border-brand-primary group transition-all duration-300 hover:-translate-y-1">
                        <div class="absolute top-0 right-0 p-4 opacity-10 transition-opacity group-hover:opacity-20">
                            <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm4.59-12.42L10 14.17l-2.59-2.58L6 13l4 4 8-8z"/>
                            </svg>
                        </div>
                        <dt class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em] mb-2">
                            Vacantes Activas
                        </dt>
                        <dd class="text-6xl font-black text-brand-dark dark:text-white transition-colors group-hover:text-brand-primary">
                            {{ activeVacanciesCount }}
                        </dd>
                        <div class="mt-6">
                            <Link :href="route('company.vacancies.create')" class="inline-flex items-center px-6 py-3 bg-brand-primary rounded-2xl font-bold text-sm text-brand-dark hover:brightness-110 transition-all duration-300 shadow-lg shadow-brand-primary/20">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                Crear Nueva Vacante
                            </Link>
                        </div>
                    </div>

                    <!-- Tarjeta: Actualizaciones / Candidatos -->
                    <div class="relative overflow-hidden bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-xl border-b-4 border-brand-secondary group transition-all duration-300 hover:-translate-y-1">
                        <div class="absolute top-0 right-0 p-4 opacity-10 transition-opacity group-hover:opacity-20">
                            <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5s-3 1.34-3 3 1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                            </svg>
                        </div>
                        <dt class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em] mb-2">
                            Candidatos en Proceso
                        </dt>
                        <div class="mt-8 text-sm text-gray-400 font-medium italic leading-relaxed">
                            Estamos procesando las aplicaciones. Pronto verás el listado de candidatos aquí.
                        </div>
                    </div>

                    <!-- Tarjeta: Alertas -->
                    <div class="relative overflow-hidden bg-brand-dark p-8 rounded-3xl shadow-xl transition-all duration-300 hover:scale-[1.01]">
                        <!-- Decorative glow -->
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-brand-primary/10 rounded-full blur-3xl"></div>
                        
                        <dt class="text-xs font-bold text-brand-primary/60 uppercase tracking-[0.2em] mb-2">
                            Alertas del Sistema
                        </dt>
                        <div class="mt-6 space-y-3">
                             <div class="flex items-start gap-3 p-3 bg-white/5 rounded-2xl border border-white/10">
                                <svg class="w-5 h-5 text-brand-primary mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-xs text-gray-300 font-medium">Mantén tu perfil corporativo actualizado.</span>
                             </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </CompanyLayout>
</template>
