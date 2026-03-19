<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: Object,
    recentApplications: Array,
});

const userRole = computed(() => usePage().props.auth.user.role);
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Dashboard de Selección" />

        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white bg-brand-dark px-4 py-2 rounded-lg inline-block">
                Dashboard Área de Selección
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- STATS CARDS -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    
                    <div v-if="userRole === 'coordinador' || userRole === 'asistente' || userRole === 'admin'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <div class="text-sm font-medium text-gray-500 truncate">Vacantes Históricas</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.total_vacancies }}</div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-brand-primary">
                        <div class="text-sm font-medium text-gray-500 truncate">Vacantes Activas {{ userRole === 'analista' ? '(Asignadas)' : '' }}</div>
                        <div class="mt-1 text-3xl font-semibold text-brand-dark">{{ stats.active_vacancies }}</div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-orange-500">
                        <div class="text-sm font-medium text-gray-500 truncate">Vacantes Urgentes {{ userRole === 'analista' ? '(Asignadas)' : '' }}</div>
                        <div class="mt-1 text-3xl font-semibold text-orange-600">{{ stats.urgent_vacancies }}</div>
                    </div>

                    <div v-if="userRole === 'coordinador' || userRole === 'asistente' || userRole === 'admin'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                        <div class="text-sm font-medium text-gray-500 truncate">Base de Candidatos Global</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.total_candidates }}</div>
                    </div>

                    <div v-if="userRole === 'analista'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                        <div class="text-sm font-medium text-gray-500 truncate">Mis Candidatos Postulados</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.my_candidates }}</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900 border-b-2 border-brand-primary pb-2 inline-block">Acciones Rápidas</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <Link :href="route('selection.vacancies.index')" class="flex items-center justify-center bg-brand-primary/10 hover:bg-brand-primary hover:text-white text-brand-dark px-4 py-8 rounded-lg shadow transition font-bold text-lg">
                            Gestión de Vacantes (Kanban)
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
