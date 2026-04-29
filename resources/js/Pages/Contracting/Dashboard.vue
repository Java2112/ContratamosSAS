<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    processes: Object,
    statuses: Array
});

const getStatusColor = (status) => {
    const s = props.statuses.find(item => item.value === status);
    return s ? s.color : 'gray';
};

const getStatusLabel = (status) => {
    const s = props.statuses.find(item => item.value === status);
    return s ? s.label : status;
};
</script>

<template>
    <Head title="Contratación - Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white bg-brand-dark px-4 py-2 rounded-lg inline-block">
                Dashboard Área de Contratación
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
                
                <!-- Stats Summary -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
                        <div class="p-3 bg-blue-50 rounded-xl">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">En Proceso</p>
                            <p class="text-2xl font-black text-gray-900">{{ stats.total_active }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
                        <div class="p-3 bg-cyan-50 rounded-xl">
                            <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Nuevos Ingresos</p>
                            <p class="text-2xl font-black text-gray-900">{{ stats.pending_new }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
                        <div class="p-3 bg-orange-50 rounded-xl">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Pend. Docs</p>
                            <p class="text-2xl font-black text-gray-900">{{ stats.pending_docs }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
                        <div class="p-3 bg-indigo-50 rounded-xl">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.691.346a6 6 0 01-3.86.517l-2.388-.477a2 2 0 00-1.022.547l-1.16 1.16a2 2 0 001.414 3.414h15.428a2 2 0 001.414-3.414l-1.16-1.16zM6 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0zM12 6a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Pend. Médico</p>
                            <p class="text-2xl font-black text-gray-900">{{ stats.pending_medical }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
                        <div class="p-3 bg-teal-50 rounded-xl">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Pend. Contrato</p>
                            <p class="text-2xl font-black text-gray-900">{{ stats.pending_contract }}</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Processes Table -->
                <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Procesos Recientes</h3>
                        <Link 
                            :href="route('contracting.process.index')" 
                            class="text-emerald-600 font-bold text-xs hover:underline"
                        >
                            Ver todos
                        </Link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    <th class="px-6 py-3">Candidato</th>
                                    <th class="px-6 py-3">Cargo</th>
                                    <th class="px-6 py-3">Estado</th>
                                    <th class="px-6 py-3 text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="process in processes.data" :key="process.id" class="hover:bg-gray-50 transition-colors text-sm">
                                    <td class="px-6 py-4 font-bold text-gray-900">
                                        {{ process.application.candidate.first_name }} {{ process.application.candidate.last_name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ process.cargo }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold" 
                                              :class="`bg-${getStatusColor(process.status)}-50 text-${getStatusColor(process.status)}-700`"
                                        >
                                            {{ getStatusLabel(process.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <Link :href="route('contracting.process.show', process.id)" 
                                              class="text-emerald-600 hover:text-emerald-700 font-bold"
                                        >
                                            Gestionar
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
