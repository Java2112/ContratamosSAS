<script setup>
import CompanyLayout from '@/Layouts/CompanyLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    application: Object,
});

const showRejectModal = ref(false);
const rejectForm = useForm({
    reason_rejection: '',
});

const approveForm = useForm({});

const applicationStatus = computed(() => {
    if (typeof props.application.status === 'object' && props.application.status !== null) {
        return props.application.status.value;
    }
    return props.application.status;
});

const isPendingReview = computed(() => applicationStatus.value === 'en_revision_empresa');
const isApproved = computed(() => applicationStatus.value === 'aprobado_empresa');
const isRejected = computed(() => applicationStatus.value === 'rechazado_empresa');

const fullName = computed(() => {
    if (!props.application.candidate) return 'Candidato Desconocido';
    return `${props.application.candidate.first_name} ${props.application.candidate.last_name}`;
});

const submitApprove = () => {
    if (confirm('¿Está seguro de aprobar a este candidato? Esto iniciará el proceso de contratación.')) {
        approveForm.post(route('company.reviews.approve', props.application.id));
    }
};

const submitReject = () => {
    rejectForm.post(route('company.reviews.reject', props.application.id), {
        onSuccess: () => {
            showRejectModal.value = false;
        }
    });
};
</script>

<template>
    <Head :title="`Revisión Candidato - ${fullName}`" />

    <CompanyLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <Link :href="route('company.vacancies.show', application.vacancy_id)" class="text-gray-400 hover:text-brand-primary transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </Link>
                    <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-200">
                        Revisión de Perfil: {{ fullName }}
                    </h2>
                </div>
                
                <div v-if="isPendingReview" class="flex space-x-3">
                    <button @click="showRejectModal = true" class="px-4 py-2 bg-red-50 text-red-600 font-bold rounded-lg hover:bg-red-100 transition border border-red-200">
                        Rechazar Candidato
                    </button>
                    <button @click="submitApprove" class="px-6 py-2 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 transition shadow-lg shadow-emerald-600/20">
                        Aprobar para Contratación
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 space-y-8">
                
                <!-- Status Banner -->
                <div v-if="!isPendingReview" class="p-4 rounded-xl flex items-center space-x-3" :class="isApproved ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'">
                    <svg v-if="isApproved" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-bold uppercase tracking-wide">
                        Este perfil ya ha sido {{ isApproved ? 'Aprobado' : 'Rechazado' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Sidebar: Personal Data -->
                    <div class="md:col-span-1 space-y-6">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6">Datos Personales</h3>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">Documento</p>
                                    <p class="text-gray-900 dark:text-gray-100 font-medium">{{ application.candidate.document_type }} {{ application.candidate.document_number }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">Teléfono</p>
                                    <p class="text-gray-900 dark:text-gray-100 font-medium">{{ application.candidate.phone || 'No proporcionado' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">Email</p>
                                    <p class="text-gray-900 dark:text-gray-100 font-medium truncate">{{ application.candidate.email }}</p>
                                </div>
                            </div>

                            <div class="mt-8 pt-6 border-t border-gray-50 dark:border-gray-700">
                                <a 
                                    v-if="application.candidate.cv_path" 
                                    :href="application.candidate.cv_path" 
                                    target="_blank"
                                    class="w-full flex items-center justify-center px-4 py-3 bg-brand-primary/10 text-brand-dark font-bold rounded-xl hover:bg-brand-primary hover:text-white transition-all border border-brand-primary"
                                >
                                    Ver Hoja de Vida
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content: Profile Details -->
                    <div class="md:col-span-2 space-y-6">
                        <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6">Evaluación de Selección</h3>
                            
                            <div class="prose dark:prose-invert max-w-none">
                                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                    El candidato ha pasado por las etapas de pre-selección, validación de antecedentes y pruebas técnicas realizadas por nuestro equipo de selección.
                                </p>
                                
                                <div class="mt-8 p-6 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800">
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-2">Comentarios del Analista</h4>
                                    <p class="text-sm text-gray-500 italic">
                                        "Perfil con alta capacidad técnica y excelentes habilidades de comunicación. Se ajusta perfectamente a la cultura de la empresa."
                                    </p>
                                </div>
                            </div>

                            <div v-if="application.report_url" class="mt-8">
                                <a :href="application.report_url" target="_blank" class="flex items-center space-x-3 text-brand-primary hover:underline font-bold">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span>Descargar Informe Detallado de Perfilamiento</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Reject Modal -->
        <div v-if="showRejectModal" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900 cursor-pointer bg-opacity-75" aria-hidden="true" @click="showRejectModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="text-center mt-3">
                        <h3 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100 tracking-tight mb-2">
                            Rechazar Candidato
                        </h3>
                        <p class="text-gray-500 text-sm">
                            Indique el motivo por el cual no desea continuar con el perfil de <strong>{{ fullName }}</strong>.
                        </p>
                    </div>
                    
                    <form @submit.prevent="submitReject" class="mt-6 space-y-4">
                        <div>
                            <InputLabel for="reason_rejection" value="Motivo del Rechazo" />
                            <textarea 
                                id="reason_rejection"
                                v-model="rejectForm.reason_rejection" 
                                rows="4" 
                                class="w-full mt-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:ring-brand-primary focus:border-brand-primary" 
                                placeholder="Ej: No cumple con la experiencia técnica requerida en X..."
                                required
                            ></textarea>
                            <InputError :message="rejectForm.errors.reason_rejection" class="mt-2" />
                        </div>
                        
                        <div class="flex justify-end gap-3 mt-6">
                            <SecondaryButton type="button" @click="showRejectModal = false">Cancelar</SecondaryButton>
                            <button type="submit" :disabled="rejectForm.processing" class="px-6 py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition">
                                Confirmar Rechazo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </CompanyLayout>
</template>
