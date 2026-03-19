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
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Tarjeta: Vacantes Activas -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 flex flex-col items-center justify-center text-center">
                        <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wide">
                            Vacantes Activas
                        </div>
                        <div class="mt-2 text-4xl font-extrabold text-blue-600 dark:text-blue-400">
                            {{ activeVacanciesCount }}
                        </div>
                        <div class="mt-4">
                            <Link :href="route('company.vacancies.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                                Crear Nueva Vacante
                            </Link>
                        </div>
                    </div>

                    <!-- Tarjeta: Actualizaciones / Candidatos -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 text-center">
                        <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wide">
                            Últimas Actualizaciones
                        </div>
                        <div class="mt-6 text-sm text-gray-600 dark:text-gray-300 italic">
                            Pronto verás aquí la información de los candidatos en proceso y seleccionados.
                        </div>
                    </div>

                    <!-- Tarjeta: Alertas -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 text-center border-t-4 border-yellow-400">
                        <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wide">
                            Alertas Importantes
                        </div>
                        <div class="mt-4 flex flex-col space-y-2">
                             <span class="text-xs text-yellow-600 dark:text-yellow-400 bg-yellow-100 dark:bg-yellow-900/30 px-3 py-1 rounded inline-block">
                                Mantén tu perfil de empresa actualizado.
                             </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </CompanyLayout>
</template>
