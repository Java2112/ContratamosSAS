<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    candidate: Object,
});

const fullName = computed(() => `${props.candidate.first_name} ${props.candidate.last_name}`);

const getStatusColor = (status) => {
    const colors = {
        'preseleccionado': 'bg-gray-100 text-gray-800',
        'antecedentes': 'bg-orange-100 text-orange-800',
        'entrevista': 'bg-blue-100 text-blue-800',
        'pruebas': 'bg-indigo-100 text-indigo-800',
        'en_revision_empresa': 'bg-yellow-100 text-yellow-800',
        'aprobado_empresa': 'bg-green-100 text-green-800',
        'rechazado_empresa': 'bg-red-100 text-red-800',
        'rechazado_interno': 'bg-red-100 text-red-800',
        'contratado': 'bg-emerald-100 text-emerald-800',
        'pendiente_contratacion': 'bg-cyan-100 text-cyan-800',
        'en_contratacion': 'bg-blue-100 text-blue-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head :title="`Candidato - ${fullName}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <Link :href="route('selection.dashboard')" class="text-gray-400 hover:text-brand-primary transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </Link>
                <h2 class="text-xl font-bold leading-tight text-white bg-brand-dark px-4 py-2 rounded-lg">
                    Expediente del Candidato: {{ fullName }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Basic Info Card -->
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white p-6 shadow-sm rounded-2xl border border-gray-100">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-24 h-24 bg-brand-primary/10 rounded-full flex items-center justify-center text-brand-dark text-3xl font-bold mb-4">
                                    {{ candidate.first_name[0] }}{{ candidate.last_name[0] }}
                                </div>
                                <h3 class="text-xl font-bold text-gray-900">{{ fullName }}</h3>
                                <p class="text-sm text-gray-500">{{ candidate.document_type }} {{ candidate.document_number }}</p>
                            </div>
                            
                            <div class="mt-8 space-y-4">
                                <div class="flex items-center space-x-3 text-sm">
                                    <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <span class="text-gray-600">{{ candidate.email }}</span>
                                </div>
                                <div class="flex items-center space-x-3 text-sm">
                                    <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    </div>
                                    <span class="text-gray-600">{{ candidate.phone || 'Sin teléfono' }}</span>
                                </div>
                                <div class="flex items-center space-x-3 text-sm">
                                    <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </div>
                                    <span class="text-gray-600">Fuente: {{ candidate.source || 'N/A' }}</span>
                                </div>
                            </div>
                            
                            <div class="mt-8">
                                <a 
                                    v-if="candidate.cv_path" 
                                    :href="`/secure-download/cv/${candidate.id}`" 
                                    target="_blank"
                                    class="w-full flex items-center justify-center px-4 py-3 bg-brand-primary text-white font-bold rounded-xl hover:bg-brand-dark transition-all shadow-lg shadow-brand-primary/20"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Ver Hoja de Vida
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Applications History -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                                <h3 class="text-lg font-bold text-brand-dark uppercase tracking-wider">Historial de Postulaciones</h3>
                            </div>
                            <div class="divide-y divide-gray-100">
                                <div v-for="app in candidate.applications" :key="app.id" class="p-6 hover:bg-gray-50 transition-colors">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h4 class="text-lg font-bold text-gray-900">{{ app.vacancy.title }}</h4>
                                            <p class="text-sm text-gray-500">{{ app.vacancy.client.business_name }}</p>
                                        </div>
                                        <span :class="['px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider', getStatusColor(app.status)]">
                                            {{ app.status.replace('_', ' ') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between mt-4 text-sm">
                                        <span class="text-gray-400 italic">Registrada el {{ new Date(app.created_at).toLocaleDateString() }}</span>
                                        <Link :href="route('selection.vacancies.show', app.vacancy_id)" class="text-brand-primary font-bold hover:underline">
                                            Ver Vacante →
                                        </Link>
                                    </div>
                                </div>
                                <div v-if="candidate.applications.length === 0" class="p-12 text-center text-gray-400">
                                    Este candidato no tiene postulaciones activas.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
