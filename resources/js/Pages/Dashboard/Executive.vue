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
    <Head title="Dashboard Ejecutivo" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Panel de Control: Gerencia</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- KPIs Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- KPI 1 -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border-l-4 border-brand-primary p-6 flex flex-col items-center justify-center transform transition duration-500 hover:scale-105">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate uppercase tracking-widest">
                            Usuarios del Sistema
                        </dt>
                        <dd class="mt-2 text-4xl font-extrabold text-brand-primary dark:text-brand-primary">
                            {{ metrics.total_users }}
                        </dd>
                    </div>
                    
                    <!-- KPI 2 -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border-l-4 border-green-500 p-6 flex flex-col items-center justify-center transform transition duration-500 hover:scale-105">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate uppercase tracking-widest">
                            Empresas Clientes
                        </dt>
                        <dd class="mt-2 text-4xl font-extrabold text-green-600 dark:text-green-400">
                            {{ metrics.active_clients }}
                        </dd>
                    </div>

                    <!-- KPI 3 -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border-l-4 border-yellow-500 p-6 flex flex-col items-center justify-center transform transition duration-500 hover:scale-105">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate uppercase tracking-widest">
                            Contratos Vigentes
                        </dt>
                        <dd class="mt-2 text-4xl font-extrabold text-yellow-600 dark:text-yellow-400">
                            {{ metrics.active_contracts }}
                        </dd>
                    </div>
                </div>

                <!-- Shortcuts Component -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4">Accesos Rápidos</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                            <Link :href="route('admin.users.index')" class="flex items-center justify-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition border border-gray-200 dark:border-gray-600">
                                <span class="font-semibold text-center text-brand-dark dark:text-brand-primary">Gestión Empleados</span>
                            </Link>
                            <Link :href="route('commercial.clients.index')" class="flex items-center justify-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition border border-gray-200 dark:border-gray-600">
                                <span class="font-semibold text-center text-brand-dark dark:text-brand-primary">Red Comercial</span>
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
