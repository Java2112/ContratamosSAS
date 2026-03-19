<script setup>
import CompanyLayout from '@/Layouts/CompanyLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    vacancies: Object,
});
</script>

<template>
    <CompanyLayout title="Mis Vacantes">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Mis Vacantes
                </h2>
                <Link :href="route('company.vacancies.create')" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    Crear Vacante
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vacantes</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Área</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="vacancy in vacancies.data" :key="vacancy.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ vacancy.title }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ vacancy.positions_required }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                          :class="vacancy.status === 'activa' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                        {{ vacancy.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ vacancy.department || 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <Link :href="route('company.vacancies.show', vacancy.id)" class="text-blue-600 hover:text-blue-900">Ver</Link>
                                    <Link v-if="['nueva', 'en_proceso', 'en_busqueda', 'en_validacion'].includes(vacancy.status)" :href="route('company.vacancies.edit', vacancy.id)" class="text-yellow-600 hover:text-yellow-900">Editar</Link>
                                </td>
                            </tr>
                            <tr v-if="!vacancies.data.length">
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No tienes vacantes registradas.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <!-- Paginación -->
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700" v-if="vacancies.links?.length > 3">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link v-if="vacancies.prev_page_url" :href="vacancies.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-gray-500">
                                    Anterior
                                </Link>
                                <Link v-if="vacancies.next_page_url" :href="vacancies.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-gray-500">
                                    Siguiente
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CompanyLayout>
</template>
