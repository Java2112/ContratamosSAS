<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    metrics: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <Head title="Dashboard Comercial" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Panel Táctico: Comercial</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- KPI 1: Clientes -->
                    <div class="relative overflow-hidden bg-brand-dark rounded-3xl p-10 shadow-2xl flex justify-between items-center text-white transition-transform duration-300 hover:scale-[1.01]">
                        <!-- Decorative glow -->
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-brand-primary/20 rounded-full blur-3xl"></div>
                        
                        <div class="relative z-10">
                            <dt class="text-xs font-bold opacity-60 uppercase tracking-[0.2em] mb-2">
                                Empresas Prospectadas / Clientes
                            </dt>
                            <dd class="text-6xl font-black text-brand-primary">
                                {{ metrics.total_clients }}
                            </dd>
                        </div>
                        <div class="relative z-10 bg-white/5 p-4 rounded-2xl border border-white/10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Recent Contracts -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-3xl border border-gray-100 dark:border-gray-700">
                    <div class="p-8">
                        <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-6 mb-6">
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-gray-100">Últimos Contratos</h3>
                                <p class="text-sm text-gray-400">Actividad reciente de la red comercial</p>
                            </div>
                            <Link :href="route('commercial.contracts.index')" class="flex items-center gap-1 text-sm font-bold text-brand-primary hover:text-brand-secondary transition-colors">
                                <span>Ver historial</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                            </Link>
                        </div>
                        <ul class="space-y-4">
                            <li v-for="contract in metrics.recent_contracts" :key="contract.id" class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl flex justify-between items-center border border-transparent hover:border-brand-primary/20 transition-all duration-300">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 bg-white dark:bg-gray-800 rounded-xl flex items-center justify-center text-brand-dark dark:text-brand-primary shadow-sm">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-base font-bold text-gray-900 dark:text-white">{{ contract.client.business_name }}</p>
                                        <p class="text-xs text-gray-500 font-medium tracking-wide">CONTRATO: {{ contract.contract_number }} · AIU: {{ contract.administration_fee_percentage }}%</p>
                                    </div>
                                </div>
                                <div class="hidden sm:block">
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        Vigente
                                    </span>
                                </div>
                            </li>
                            <li v-if="metrics.recent_contracts.length === 0" class="py-12 text-center">
                                <p class="text-gray-400 text-sm italic">Aún no existen contratos firmados para mostrar.</p>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
