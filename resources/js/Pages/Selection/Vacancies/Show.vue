<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    vacancy: Object,
    magicLinkUrl: String,
});

const userRole = computed(() => usePage().props.auth.user?.role);

// Modals State
const showStatusModal = ref(false);
const showCandidateModal = ref(false);
const selectedApplication = ref(null);

// Form for App Status
const statusForm = useForm({
    status: '',
    notes: '',
});

// Form for New Candidate
const candidateForm = useForm({
    document_type: 'CC',
    document_number: '',
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    source: 'Oficina',
    cv_file: null,
});

const availableStatuses = [
    { value: 'preseleccionado', label: 'Pre-seleccionado' },
    { value: 'antecedentes', label: 'Revisión Antecedentes' },
    { value: 'entrevista', label: 'En Entrevista' },
    { value: 'pruebas', label: 'En Pruebas' },
    { value: 'enviado_cliente', label: 'Enviado al Cliente' },
    { value: 'rechazado_interno', label: 'Descartado Internamente' },
];

const openStatusModal = (application) => {
    selectedApplication.value = application;
    statusForm.status = application.status || '';
    statusForm.notes = '';
    showStatusModal.value = true;
};

const submitStatus = () => {
    statusForm.post(route('selection.applications.status', selectedApplication.value.id), {
        onSuccess: () => {
            showStatusModal.value = false;
        }
    });
};

const submitCandidate = () => {
    candidateForm.post(route('selection.vacancies.candidates.store', props.vacancy.id), {
        onSuccess: () => {
            showCandidateModal.value = false;
            candidateForm.reset();
        }
    });
};

const handleFileChange = (e) => {
    candidateForm.cv_file = e.target.files[0];
};

const getStatusColor = (status) => {
    const colors = {
        'preseleccionado': 'bg-gray-100 text-gray-800',
        'antecedentes': 'bg-orange-100 text-orange-800',
        'entrevista': 'bg-blue-100 text-blue-800',
        'pruebas': 'bg-indigo-100 text-indigo-800',
        'enviado_cliente': 'bg-yellow-100 text-yellow-800',
        'aprobado_cliente': 'bg-green-100 text-green-800',
        'rechazado_cliente': 'bg-red-100 text-red-800',
        'rechazado_interno': 'bg-red-100 text-red-800',
        'contratado': 'bg-emerald-100 text-emerald-800',
        'entrevista_cliente': 'bg-purple-100 text-purple-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const generateMagicLink = () => {
    if (confirm('¿Generar nuevo Enlace Mágico? (El anterior expirará)')) {
        router.post(route('selection.vacancies.magic-link', props.vacancy.id), {}, {
            preserveScroll: true
        });
    }
};

const copyMagicLink = () => {
    navigator.clipboard.writeText(props.magicLinkUrl).then(() => {
        alert('¡Enlace Mágico copiado al portapapeles!');
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`Vacante #${vacancy.id} - ${vacancy.title}`" />

        <template #header>
            <div class="flex items-center space-x-4">
                <Link :href="route('selection.vacancies.index')" class="text-gray-400 hover:text-brand-primary transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </Link>
                <h2 class="text-xl font-bold leading-tight text-white bg-brand-dark px-4 py-2 rounded-lg">
                    Gestión de Vacante: {{ vacancy.title }}
                </h2>
                <span class="px-3 py-1 bg-brand-primary/20 text-brand-dark font-bold rounded-full text-sm border border-brand-primary">
                    {{ vacancy.status }}
                </span>
                <span v-if="vacancy.priority === 'urgent'" class="px-3 py-1 bg-red-100 text-red-800 font-bold rounded-full text-sm border border-red-200">
                    URGENTE
                </span>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                
                <!-- Detalles de la Vacante -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 border-b pb-6 mb-6">
                        <div>
                            <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider">Cliente / Empresa</h4>
                            <p class="mt-1 text-lg font-semibold text-brand-dark">{{ vacancy.client?.business_name || 'N/A' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider">Cupos Requeridos</h4>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ vacancy.positions_required }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider">Analista Asignado</h4>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ vacancy.analyst?.name || 'Sin Asignar' }}</p>
                        </div>
                        <div v-if="userRole === 'coordinador' || userRole === 'asistente'">
                            <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider">Acceso Cliente (Magic Link)</h4>
                            <div class="mt-2 flex items-center space-x-2">
                                <template v-if="magicLinkUrl">
                                    <input type="text" readonly :value="magicLinkUrl" class="w-full text-xs text-gray-500 bg-gray-50 border-gray-200 rounded-md focus:ring-0 cursor-text" @click="$event.target.select()" />
                                    <button @click="copyMagicLink" class="px-3 py-2 bg-brand-primary text-white text-xs font-bold rounded-md hover:bg-brand-dark transition">
                                        Copiar
                                    </button>
                                </template>
                                <template v-else>
                                    <SecondaryButton @click="generateMagicLink" class="text-xs">
                                        Generar Enlace
                                    </SecondaryButton>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Columna 1 -->
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-bold text-gray-400 uppercase">Información General</h4>
                                <ul class="mt-2 space-y-2 text-sm text-gray-700">
                                    <li><strong>Área/Depto:</strong> {{ vacancy.department || 'N/A' }}</li>
                                    <li><strong>Empleador:</strong> <span class="capitalize">{{ vacancy.employer_type?.replace('_', ' ') || 'N/A' }}</span></li>
                                    <li><strong>Contrato:</strong> <span class="capitalize">{{ vacancy.contract_type?.replace('_', ' ') || 'N/A' }}</span></li>
                                    <li><strong>Jornada:</strong> <span class="capitalize">{{ vacancy.workday_type?.replace('_', ' ') || 'N/A' }}</span></li>
                                    <li><strong>Horario:</strong> {{ vacancy.schedule || 'N/A' }}</li>
                                    <li><strong>Duración (Meses):</strong> {{ vacancy.estimated_duration_months || 'N/A' }}</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-400 uppercase hover:text-gray-600 mt-6">Modalidad y Ubicación</h4>
                                <ul class="mt-2 space-y-2 text-sm text-gray-700">
                                    <li><strong>Modalidad:</strong> <span class="capitalize">{{ vacancy.work_modality || 'N/A' }}</span></li>
                                    <li><strong>Ciudad:</strong> {{ vacancy.city || 'N/A' }}</li>
                                    <li><strong>Departamento:</strong> {{ vacancy.department_name || 'N/A' }}</li>
                                    <li><strong>Dirección:</strong> {{ vacancy.address || 'N/A' }}</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Columna 2 -->
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-bold text-gray-400 uppercase">Oferta Salarial</h4>
                                <ul class="mt-2 space-y-2 text-sm text-gray-700">
                                    <li><strong>Tipo:</strong> <span class="capitalize">{{ vacancy.salary_type || 'N/A' }}</span></li>
                                    <li v-if="vacancy.salary_type === 'rango'">
                                        <strong>Rango:</strong> ${{ vacancy.min_salary }} - ${{ vacancy.max_salary }}
                                    </li>
                                    <li><strong>Frecuencia Pago:</strong> <span class="capitalize">{{ vacancy.payroll_frequency || 'N/A' }}</span></li>
                                    <li><strong>Comisiones/Bonos:</strong> {{ vacancy.has_bonuses ? 'Sí ($' + vacancy.bonus_average + ' prom)' : 'No' }}</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-400 uppercase mt-6">Requisitos del Candidato</h4>
                                <ul class="mt-2 space-y-2 text-sm text-gray-700">
                                    <li><strong>Nivel Educativo:</strong> <span class="capitalize">{{ vacancy.min_education_level || 'N/A' }}</span></li>
                                    <li><strong>Experiencia Mínima:</strong> {{ vacancy.min_experience_years !== null ? vacancy.min_experience_years + ' años' : 'N/A' }}</li>
                                    <li v-if="vacancy.languages?.length > 0"><strong>Idiomas:</strong> {{ vacancy.languages.join(', ') }}</li>
                                    <li v-if="vacancy.hard_skills?.length > 0"><strong>Conocimientos:</strong> {{ vacancy.hard_skills.join(', ') }}</li>
                                    <li v-if="vacancy.soft_skills?.length > 0"><strong>Habilidades Blandas:</strong> {{ vacancy.soft_skills.join(', ') }}</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Columna 3 -->
                        <div>
                            <h4 class="text-sm font-bold text-gray-400 uppercase">Descripción / Funciones</h4>
                            <div class="mt-2 text-sm text-gray-700 whitespace-pre-line bg-gray-50 p-3 rounded border">
                                {{ vacancy.main_functions || vacancy.description || 'Sin descripción de funciones proporcionadas.' }}
                            </div>

                            <h4 class="text-sm font-bold text-gray-400 uppercase mt-6">Características Opcionales</h4>
                            <div class="mt-2 text-sm text-gray-700 whitespace-pre-line bg-gray-50 p-3 rounded border">
                                {{ vacancy.optional_features || 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Candidatos Postulados -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                        <h3 class="text-lg font-bold text-brand-dark">Candidatos en Proceso ({{ vacancy.applications.length }})</h3>
                        <PrimaryButton v-if="userRole === 'coordinador' || userRole === 'asistente' || userRole === 'analista'" @click="showCandidateModal = true">
                            + Añadir Candidato
                        </PrimaryButton>
                    </div>
                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-600 uppercase bg-gray-100 border-b">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Candidato</th>
                                    <th scope="col" class="px-6 py-3">Documento</th>
                                    <th scope="col" class="px-6 py-3">Contacto</th>
                                    <th scope="col" class="px-6 py-3 text-center">Estado Actual</th>
                                    <th scope="col" class="px-6 py-3 text-center">Trazabilidad</th>
                                    <th scope="col" class="px-6 py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="app in vacancy.applications" :key="app.id" class="border-b transition hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ app.candidate?.first_name }} {{ app.candidate?.last_name }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ app.candidate?.document_type }} {{ app.candidate?.document_number }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ app.candidate?.email }} <br>
                                        {{ app.candidate?.phone }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span :class="['px-3 py-1 text-xs font-bold rounded-full', getStatusColor(app.status)]">
                                            {{ app.status.replace('_', ' ').toUpperCase() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-xs text-gray-500">
                                        {{ app.stages?.length || 0 }} movimientos
                                    </td>
                                    <td class="px-6 py-4 text-center space-x-2">
                                        <button v-if="userRole === 'coordinador' || userRole === 'asistente' || userRole === 'analista'" @click="openStatusModal(app)" class="text-brand-primary hover:text-brand-dark font-bold underline text-xs uppercase tracking-wide">
                                            Avanzar Etapa
                                        </button>
                                        <a v-if="app.candidate?.cv_path" :href="app.candidate.cv_path" target="_blank" class="text-blue-500 hover:text-blue-700 ml-2 font-bold underline text-xs uppercase tracking-wide">
                                            Ver CV
                                        </a>
                                        <a v-if="app.report_url" :href="app.report_url" target="_blank" class="text-green-600 hover:text-green-800 ml-2 font-bold underline text-xs uppercase tracking-wide">
                                            Info. Perfil
                                        </a>
                                    </td>
                                </tr>
                                <tr v-if="vacancy.applications.length === 0">
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        No hay candidatos asignados a esta vacante aún.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal Cambiar Estado Candidato -->
        <div v-if="showStatusModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900 cursor-pointer bg-opacity-75" aria-hidden="true" @click="showStatusModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 border border-gray-100">
                    <div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg font-extrabold leading-6 text-brand-dark" id="modal-title">
                                Actualizar Etapa del Candidato
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Mueva al candidato <strong>{{ selectedApplication?.candidate?.first_name }} {{ selectedApplication?.candidate?.last_name }}</strong> a la siguiente etapa del embudo de selección.
                                </p>
                            </div>
                        </div>
                    </div>
                    <form @submit.prevent="submitStatus" class="mt-5 sm:mt-6">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nuevo Estado</label>
                            <select v-model="statusForm.status" required class="w-full border-gray-300 focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm">
                                <option value="" disabled>Seleccione etapa...</option>
                                <option v-for="status in availableStatuses" :key="status.value" :value="status.value">
                                    {{ status.label }}
                                </option>
                            </select>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notas / Observaciones (Opcional)</label>
                            <textarea v-model="statusForm.notes" rows="3" class="w-full border-gray-300 focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm" placeholder="Ej: Pasó la entrevista técnica con 90/100..."></textarea>
                        </div>
                        <div class="flex justify-end gap-3">
                            <SecondaryButton @click="showStatusModal = false">Cancelar</SecondaryButton>
                            <PrimaryButton :disabled="statusForm.processing">Guardar Etapa</PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Añadir Candidato -->
        <div v-if="showCandidateModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-candidate" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900 cursor-pointer bg-opacity-75" aria-hidden="true" @click="showCandidateModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6 border border-gray-100">
                    <div>
                        <div class="mt-3 sm:mt-5 mb-4 border-b pb-4">
                            <h3 class="text-xl font-extrabold leading-6 text-brand-dark" id="modal-candidate">
                                Registrar Candidato
                            </h3>
                            <p class="text-sm text-gray-500 mt-2">
                                Ingrese los datos básicos del candidato y adjunte su Hoja de Vida (CV) para vincularlo a <strong>{{ vacancy.title }}</strong>.
                            </p>
                        </div>
                    </div>
                    <form @submit.prevent="submitCandidate" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="first_name" value="Nombres" />
                                <TextInput id="first_name" type="text" class="mt-1 block w-full" v-model="candidateForm.first_name" required />
                                <InputError :message="candidateForm.errors.first_name" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="last_name" value="Apellidos" />
                                <TextInput id="last_name" type="text" class="mt-1 block w-full" v-model="candidateForm.last_name" required />
                                <InputError :message="candidateForm.errors.last_name" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <InputLabel for="document_type" value="Tipo Doc." />
                                <select v-model="candidateForm.document_type" required class="mt-1 block w-full border-gray-300 focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm">
                                    <option value="CC">CC</option>
                                    <option value="CE">CE</option>
                                    <option value="PAS">Pasaporte</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <InputLabel for="document_number" value="Número de Documento" />
                                <TextInput id="document_number" type="text" class="mt-1 block w-full" v-model="candidateForm.document_number" required />
                                <InputError :message="candidateForm.errors.document_number" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="email" value="Correo Electrónico" />
                                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="candidateForm.email" required />
                                <InputError :message="candidateForm.errors.email" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="phone" value="Teléfono / Celular" />
                                <TextInput id="phone" type="text" class="mt-1 block w-full" v-model="candidateForm.phone" />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="cv_file" value="Hoja de Vida (PDF, DOCX) - Max 5MB" />
                            <input id="cv_file" type="file" @change="handleFileChange" accept=".pdf,.doc,.docx" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-bold file:bg-brand-primary/10 file:text-brand-dark hover:file:bg-brand-primary/20" />
                            <InputError :message="candidateForm.errors.cv_file" class="mt-2" />
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <SecondaryButton @click="showCandidateModal = false">Cancelar</SecondaryButton>
                            <PrimaryButton :disabled="candidateForm.processing">Vincular Candidato</PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
