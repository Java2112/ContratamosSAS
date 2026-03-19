```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    vacancies: Object,
    clients: Array,
    analysts: Array,
});

const userRole = computed(() => usePage().props.auth.user.role);

const assignForm = useForm({
    analyst_id: '',
});

const showAssignModal = ref(false);
const selectedVacancy = ref(null);

const openAssignModal = (vacancy) => {
    selectedVacancy.value = vacancy;
    assignForm.analyst_id = vacancy.analyst_id || '';
    showAssignModal.value = true;
};

const submitAssign = () => {
    assignForm.post(route('selection.vacancies.assign', selectedVacancy.value.id), {
        onSuccess: () => {
            showAssignModal.value = false;
        }
    });
};

const markUrgent = (vacancy) => {
    router.post(route('selection.vacancies.urgent', vacancy.id), {}, { preserveScroll: true });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Gestión de Vacantes" />

        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold leading-tight text-brand-dark">
                    Gestión de Vacantes (Kanban)
                </h2>
                <Link v-if="userRole === 'coordinador' || userRole === 'asistente' || userRole === 'admin'" :href="route('selection.vacancies.create')" class="px-5 py-2.5 bg-brand-primary text-white font-bold rounded-lg hover:bg-brand-dark transition shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nueva Vacante
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Filtros se pueden agregar aquí -->

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-white uppercase bg-brand-dark rounded-t-lg">
                                <tr>
                                    <th scope="col" class="px-6 py-3">ID / Título</th>
                                    <th scope="col" class="px-6 py-3">Cliente</th>
                                    <th scope="col" class="px-6 py-3 text-center">Cupos</th>
                                    <th scope="col" class="px-6 py-3 text-center">Analista</th>
                                    <th scope="col" class="px-6 py-3 text-center">Estado</th>
                                    <th scope="col" class="px-6 py-3 text-center">Prioridad</th>
                                    <th scope="col" class="px-6 py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="vacancy in vacancies.data" :key="vacancy.id" class="border-b transition hover:bg-brand-primary/5">
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        <Link :href="route('selection.vacancies.show', vacancy.id)" class="text-brand-dark hover:text-brand-primary font-bold">
                                            #{{ vacancy.id }} - {{ vacancy.title }}
                                        </Link>
                                    </td>
                                    <td class="px-6 py-4">{{ vacancy.client?.business_name }}</td>
                                    <td class="px-6 py-4 text-center font-bold text-gray-600">{{ vacancy.applications_count }} / {{ vacancy.positions_required }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span v-if="vacancy.analyst_id" class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                            {{ vacancy.analyst?.name }}
                                        </span>
                                        <span v-else class="text-xs text-gray-400 italic">Sin Asignar</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full border border-gray-200">
                                            {{ vacancy.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span v-if="vacancy.priority === 'urgent'" class="px-2 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full border border-red-200">
                                            URGENTE
                                        </span>
                                        <span v-else class="text-gray-500 text-xs">Normal</span>
                                    </td>
                                    <td class="px-6 py-4 text-center space-x-2">
                                        <button v-if="userRole === 'coordinador' || userRole === 'asistente'" @click="openAssignModal(vacancy)" class="text-indigo-600 hover:text-indigo-900" title="Asignar Analista">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                        </button>
                                        <button v-if="(userRole === 'coordinador' || userRole === 'asistente') && vacancy.priority !== 'urgent'" @click="markUrgent(vacancy)" class="text-red-500 hover:text-red-700" title="Marcar Urgente">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </button>
                                        <Link :href="route('selection.vacancies.show', vacancy.id)" class="text-brand-primary hover:text-brand-dark" title="Ver Detalle">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="vacancies.data.length === 0">
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        No hay vacantes registradas en el sistema.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal Asignar Analista -->
        <div v-if="showAssignModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 cursor-pointer bg-opacity-75" aria-hidden="true" @click="showAssignModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                Asignar Analista a Vacante #{{ selectedVacancy?.id }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Selecciona el analista de selección que se hará cargo de buscar candidatos para "{{ selectedVacancy?.title }}".
                                </p>
                            </div>
                        </div>
                    </div>
                    <form @submit.prevent="submitAssign" class="mt-5 sm:mt-6">
                        <select v-model="assignForm.analyst_id" required class="w-full border-gray-300 focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm mb-4">
                            <option value="" disabled>Seleccione un analista...</option>
                            <option v-for="analyst in analysts" :key="analyst.id" :value="analyst.id">
                                {{ analyst.name }}
                            </option>
                        </select>
                        <div class="flex justify-end gap-3">
                            <SecondaryButton @click="showAssignModal = false">Cancelar</SecondaryButton>
                            <PrimaryButton :disabled="assignForm.processing">Asignar y Notificar</PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
