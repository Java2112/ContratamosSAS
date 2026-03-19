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
                    <!-- KPI 1 -->
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 overflow-hidden shadow-lg sm:rounded-xl p-8 flex justify-between items-center text-white">
                        <div>
                            <dt class="text-sm font-medium opacity-80 uppercase tracking-widest">
                                Empresas Prospectadas / Clientes
                            </dt>
                            <dd class="mt-1 text-5xl font-extrabold">
                                {{ metrics.total_clients }}
                            </dd>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Recent Contracts -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6">
                        <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Últimos Contratos (Top 5)</h3>
                            <Link :href="route('commercial.contracts.index')" class="text-sm text-brand-primary hover:text-brand-dark dark:text-brand-primary">Ver todos &rarr;</Link>
                        </div>
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            <li v-for="contract in metrics.recent_contracts" :key="contract.id" class="py-4 flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ contract.client.business_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Contrato: {{ contract.contract_number }} | AIU: {{ contract.administration_fee_percentage }}%</p>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Vigente
                                    </span>
                                </div>
                            </li>
                            <li v-if="metrics.recent_contracts.length === 0" class="py-4 text-sm text-gray-500 italic text-center">
                                Aún no existen contratos firmados.
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
