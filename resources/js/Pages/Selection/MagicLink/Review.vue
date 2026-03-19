<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const props = defineProps({
    vacancy: Object,
    token: String,
});

const reviewForm = useForm({
    status: '',
    feedback: '',
});

const selectedApp = ref(null);
const showActionModal = ref(false);

const openReview = (app, actionType) => {
    selectedApp.value = app;
    reviewForm.status = actionType;
    reviewForm.feedback = '';
    showActionModal.value = true;
};

const submitReview = () => {
    reviewForm.post(route('magic-link.application.update', { token: props.token, application: selectedApp.value.id }), {
        onSuccess: () => {
            showActionModal.value = false;
        }
    });
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex flex-col pt-10 px-4 sm:px-6 lg:px-8 font-sans">
        <Head :title="`Revisión de Candidatos - ${vacancy.title}`" />

        <div class="max-w-4xl mx-auto w-full mb-8 flex justify-center">
            <ApplicationLogo class="w-48 text-brand-dark" />
        </div>

        <div class="max-w-4xl mx-auto w-full space-y-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-brand-dark tracking-tight">
                    Portal de Revisión de Candidatos
                </h2>
                <p class="mt-2 text-lg text-gray-500">
                    Propuesta de talento para: <span class="font-bold text-brand-primary">{{ vacancy.title }}</span>
                </p>
            </div>

            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                <div class="px-6 py-5 border-b border-gray-100 bg-brand-dark flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white uppercase tracking-wider">Candidatos Presentados</h3>
                    <span class="px-3 py-1 bg-brand-primary/20 text-white font-bold rounded-full text-xs">
                        {{ vacancy.applications.length }} Perfiles
                    </span>
                </div>

                <div class="divide-y divide-gray-100">
                    <div v-for="app in vacancy.applications" :key="app.id" class="p-6 sm:p-8 flex flex-col lg:flex-row lg:justify-between lg:items-center gap-6 hover:bg-gray-50 transition">
                        
                        <div class="flex-1">
                            <h4 class="text-xl font-extrabold text-gray-900 flex items-center gap-3">
                                {{ app.candidate.first_name }} {{ app.candidate.last_name }}
                                <span v-if="app.status === 'aprobado_cliente'" class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full uppercase tracking-widest border border-green-200">Aprobado</span>
                                <span v-else-if="app.status === 'rechazado_cliente'" class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full uppercase tracking-widest border border-red-200">Rechazado</span>
                                <span v-else-if="app.status === 'entrevista_cliente'" class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full uppercase tracking-widest border border-purple-200">Para Entrevista</span>
                                <span v-else class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full uppercase tracking-widest border border-yellow-200">Pendiente Revisión</span>
                            </h4>
                            <p class="mt-2 text-sm text-gray-500">
                                <strong>Perfilamiento:</strong> Evaluado y pre-seleccionado por el Área de Selección de CONTRATAMOS. Cumple con los requisitos solicitados para la vacante.
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 min-w-max">
                            <a v-if="app.candidate.cv_path" :href="app.candidate.cv_path" target="_blank" class="px-4 py-2 border border-brand-primary text-brand-primary hover:bg-brand-primary hover:text-white font-bold rounded-lg text-sm text-center transition flex-1">
                                Ver Hoja de Vida
                            </a>
                            <a v-if="app.report_url" :href="app.report_url" target="_blank" class="px-4 py-2 bg-brand-primary/10 text-brand-dark hover:bg-brand-primary hover:text-white font-bold rounded-lg text-sm text-center transition flex-1 border border-brand-primary">
                                Informe Perfilamiento
                            </a>
                            
                            <!-- Act mode if not decided -->
                            <template v-if="app.status === 'enviado_cliente'">
                                <button @click="openReview(app, 'entrevista_cliente')" class="px-4 py-2 bg-purple-600 text-white hover:bg-purple-700 font-bold rounded-lg text-sm text-center shadow-lg transition flex-1">
                                    Citar a Entrevista
                                </button>
                                <button @click="openReview(app, 'aprobado_cliente')" class="px-4 py-2 bg-green-500 text-white hover:bg-green-600 font-bold rounded-lg text-sm text-center shadow-lg transition flex-1">
                                    Aprobar
                                </button>
                                <button @click="openReview(app, 'rechazado_cliente')" class="px-4 py-2 bg-red-500 text-white hover:bg-red-600 font-bold rounded-lg text-sm text-center shadow-lg transition flex-1">
                                    Descartar
                                </button>
                            </template>
                        </div>

                    </div>
                    
                    <div v-if="vacancy.applications.length === 0" class="p-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Sin Candidatos</h3>
                        <p class="mt-1 text-sm text-gray-500">Aún no hemos enviado candidatos para tu revisión. Pronto te notificaremos.</p>
                    </div>
                </div>
            </div>

            <!-- Footer / Disclaimer -->
            <p class="text-center text-xs text-gray-400 pb-12">
                Enlace seguro autogenerado para el empleador. Este enlace expira automáticamente por razones de seguridad de datos.<br>
                &copy; {{ new Date().getFullYear() }} CONTRATAMOS S.A.S - Todos los derechos reservados.
            </p>
        </div>

        <!-- Acciones Modal -->
        <div v-if="showActionModal" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900 cursor-pointer bg-opacity-75" aria-hidden="true" @click="showActionModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="text-center mt-3">
                        <h3 class="text-2xl font-extrabold text-brand-dark tracking-tight mb-2">
                            {{ reviewForm.status === 'aprobado_cliente' ? 'Aprobar Candidato' : reviewForm.status === 'entrevista_cliente' ? 'Agendar Entrevista' : 'Descartar Candidato' }}
                        </h3>
                        <p class="text-gray-500 text-sm">
                            Confirma tu decisión para el perfil de <strong>{{ selectedApp?.candidate?.first_name }}</strong>.
                        </p>
                    </div>
                    
                    <form @submit.prevent="submitReview" class="mt-6 space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Feedback / Comentarios Adicionales</label>
                            <textarea v-model="reviewForm.feedback" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-brand-primary focus:border-brand-primary" placeholder="Danos tus razones o indícanos tu disponibilidad para la entrevista..."></textarea>
                        </div>
                        
                        <div class="flex justify-end gap-3 mt-6">
                            <SecondaryButton type="button" @click="showActionModal = false">Cancelar</SecondaryButton>
                            <PrimaryButton :disabled="reviewForm.processing">Enviar Respuesta</PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
