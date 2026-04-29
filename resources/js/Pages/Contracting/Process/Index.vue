<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        if (timeoutId) clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const props = defineProps({
    processes: Object,
    filters: Object,
    statuses: Array
});

const search = ref(props.filters.search);
const status = ref(props.filters.status);

watch([search, status], debounce(() => {
    useForm({
        search: search.value,
        status: status.value
    }).get(route('contracting.process.index'), {
        preserveState: true,
        replace: true
    });
}, 300));

const getStatusColor = (processStatus) => {
    const s = props.statuses.find(item => item.value === processStatus);
    return s ? s.color : 'gray';
};

const getStatusLabel = (processStatus) => {
    const s = props.statuses.find(item => item.value === processStatus);
    return s ? s.label : processStatus;
};
</script>

<template>
    <Head title="Contratación - Procesos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white bg-brand-dark px-4 py-2 rounded-lg inline-block">
                Listado de Procesos de Contratación
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Filters Bar -->
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="relative flex-1 max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <TextInput 
                            v-model="search" 
                            type="text" 
                            placeholder="Buscar candidato..." 
                            class="pl-10 w-full"
                        />
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <select 
                            v-model="status" 
                            class="border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm text-sm"
                        >
                            <option :value="null">Todos los Estados</option>
                            <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                        </select>

                        <Link 
                            :href="route('contracting.process.index')"
                            class="p-2 text-gray-400 hover:text-emerald-600 transition-colors"
                            title="Limpiar filtros"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </Link>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                <tr>
                                    <th class="px-6 py-4">Candidato / Cargo</th>
                                    <th class="px-6 py-4">Empresa</th>
                                    <th class="px-6 py-4">Estado</th>
                                    <th class="px-6 py-4">Salario</th>
                                    <th class="px-6 py-4 text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-sm">
                                <tr v-for="process in processes.data" :key="process.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ process.application.candidate.first_name }} {{ process.application.candidate.last_name }}</div>
                                        <div class="text-xs text-gray-400">{{ process.cargo }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ process.application.vacancy.client.business_name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span 
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold"
                                            :class="`bg-${getStatusColor(process.status)}-50 text-${getStatusColor(process.status)}-700`"
                                        >
                                            {{ getStatusLabel(process.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-700">
                                        $ {{ Number(process.agreed_salary).toLocaleString() }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <Link 
                                            :href="route('contracting.process.show', process.id)"
                                            class="inline-flex items-center px-4 py-2 bg-brand-dark text-white rounded-lg text-xs font-bold hover:bg-brand-primary hover:text-brand-dark transition-all"
                                        >
                                            Gestionar
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="processes.data.length === 0">
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">
                                        No se encontraron procesos de contratación.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-50">
                        <Pagination :links="processes.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
