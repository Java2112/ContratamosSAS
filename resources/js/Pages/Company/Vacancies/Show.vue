<script setup>
import CompanyLayout from '@/Layouts/CompanyLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    vacancy: Object,
});

const form = useForm({
    status: props.vacancy.status
});

const changeStatus = (newStatus) => {
    form.status = newStatus;
    form.put(route('company.vacancies.update', props.vacancy.id));
};
</script>

<template>
    <CompanyLayout :title="'Detalle Vacante: ' + vacancy.title">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ vacancy.title }} - Detalles
                </h2>
                <Link :href="route('company.vacancies.index')" class="text-gray-500 hover:text-gray-700">Volver a mis vacantes</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Status Management -->
                <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Estado de la Vacante</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Actual: <span class="font-bold uppercase">{{ vacancy.status }}</span></p>
                    </div>
                    <div v-if="vacancy.status !== 'cancelada' && vacancy.status !== 'cerrada'" class="flex space-x-2">
                        <button @click="if (confirm('¿Estás seguro de que deseas cancelar esta vacante?')) changeStatus('cancelada')" class="px-4 py-2 bg-red-100 text-red-800 rounded hover:bg-red-200 font-medium">Cancelar Vacante</button>
                    </div>
                </div>

                <!-- Candidates Overview -->
                <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Proceso de Selección (Candidatos)</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-4 py-2">Nombre Oculto (ID)</th>
                                    <th class="px-4 py-2 text-center">Estado de Postulación</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr v-for="app in vacancy.applications" :key="app.id">
                                    <td class="px-4 py-3 font-medium text-gray-600">
                                        Candidato #{{ String(app.candidate_id).padStart(4, '0') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold uppercase">{{ app.status.replace('_', ' ') }}</span>
                                    </td>
                                </tr>
                                <tr v-if="!vacancy.applications || vacancy.applications.length === 0">
                                    <td colspan="2" class="px-4 py-8 text-center text-gray-500">
                                        Aún no hay candidatos procesados para esta vacante.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </CompanyLayout>
</template>
