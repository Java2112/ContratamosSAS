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
                    <!-- KPI 1: Usuarios -->
                    <div class="relative overflow-hidden bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-xl border-b-4 border-brand-primary group transition-all duration-300 hover:-translate-y-1">
                        <div class="absolute top-0 right-0 p-4 opacity-10 transition-opacity group-hover:opacity-20">
                            <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                        <dt class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em] mb-2">
                            Usuarios del Sistema
                        </dt>
                        <dd class="text-5xl font-black text-brand-dark dark:text-white transition-colors group-hover:text-brand-primary">
                            {{ metrics.total_users }}
                        </dd>
                        <div class="mt-4 flex items-center text-xs text-brand-primary font-bold uppercase">
                            <span>Gestionar usuarios</span>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                    
                    <!-- KPI 2: Clientes -->
                    <div class="relative overflow-hidden bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-xl border-b-4 border-brand-secondary group transition-all duration-300 hover:-translate-y-1">
                        <div class="absolute top-0 right-0 p-4 opacity-10 transition-opacity group-hover:opacity-20">
                            <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>
                            </svg>
                        </div>
                        <dt class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em] mb-2">
                            Empresas Clientes
                        </dt>
                        <dd class="text-5xl font-black text-brand-dark dark:text-white transition-colors group-hover:text-brand-secondary">
                            {{ metrics.active_clients }}
                        </dd>
                        <div class="mt-4 flex items-center text-xs text-brand-secondary font-bold uppercase">
                            <span>Ver red comercial</span>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>

                    <!-- KPI 3: Contratos -->
                    <div class="relative overflow-hidden bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-xl border-b-4 border-emerald-600 group transition-all duration-300 hover:-translate-y-1">
                        <div class="absolute top-0 right-0 p-4 opacity-10 transition-opacity group-hover:opacity-20">
                            <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                            </svg>
                        </div>
                        <dt class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em] mb-2">
                            Contratos Vigentes
                        </dt>
                        <dd class="text-5xl font-black text-brand-dark dark:text-white transition-colors group-hover:text-emerald-600">
                            {{ metrics.active_contracts }}
                        </dd>
                        <div class="mt-4 flex items-center text-xs text-emerald-600 font-bold uppercase">
                            <span>Revisar contratos</span>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Shortcuts Section -->
                <div class="bg-brand-dark rounded-3xl p-8 shadow-2xl overflow-hidden relative">
                    <!-- Decorative shapes -->
                    <div class="absolute -top-24 -right-24 w-64 h-64 bg-brand-primary/10 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-brand-secondary/10 rounded-full blur-3xl"></div>

                    <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                        <div class="text-center md:text-left">
                            <h3 class="text-2xl font-black text-white mb-2">Accesos Rápidos</h3>
                            <p class="text-gray-400 text-sm">Gestiona los módulos principales del sistema</p>
                        </div>
                        <div class="flex flex-wrap justify-center gap-4">
                            <Link :href="route('admin.users.index')" class="px-6 py-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/10 text-white font-bold hover:bg-white hover:text-brand-dark transition-all duration-300">
                                Gestión Empleados
                            </Link>
                            <Link :href="route('commercial.clients.index')" class="px-6 py-3 bg-brand-primary rounded-2xl text-brand-dark font-bold hover:brightness-110 transition-all duration-300 shadow-lg shadow-brand-primary/20">
                                Red Comercial
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
