<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    process: Object,
    enums: Object
});

const activeTab = ref('documents');

const docForm = useForm({
    status: '',
    rejection_reason: '',
    file: null
});

const selectedDoc = ref(null);
const showDocModal = ref(false);

const openDocModal = (doc) => {
    selectedDoc.value = doc;
    docForm.status = doc.status;
    docForm.rejection_reason = doc.rejection_reason || '';
    showDocModal.value = true;
};

const submitDocValidation = () => {
    docForm.post(route('contracting.document.validate', selectedDoc.value.id), {
        forceFormData: true,
        onSuccess: () => {
            showDocModal.value = false;
            docForm.reset();
        }
    });
};

const medicalForm = useForm({
    provider_name: props.process.medical_exam?.provider_name || '',
    scheduled_date: props.process.medical_exam?.scheduled_date || '',
    result: props.process.medical_exam?.result || '',
    observations: props.process.medical_exam?.observations || '',
    file: null
});

const submitMedical = () => {
    medicalForm.post(route('contracting.process.medical', props.process.id), {
        forceFormData: true,
        onSuccess: () => activeTab.value = 'contract'
    });
};

const contractForm = useForm({
    file: null,
    start_date: props.process.contract?.start_date || '',
    end_date: props.process.contract?.end_date || ''
});

const uploadContract = () => {
    contractForm.post(route('contracting.process.contract.upload', props.process.id), {
        forceFormData: true
    });
};

const signContract = () => {
    if (confirm('¿Está seguro de que desea firmar este contrato digitalmente?')) {
        useForm({}).post(route('contracting.process.contract.sign', props.process.id), {
            onSuccess: () => activeTab.value = 'affiliations'
        });
    }
};

const affiliationForm = useForm({
    type: '',
    entity_name: '',
    affiliation_number: '',
    affiliation_date: '',
    status: 'PENDIENTE',
    file: null
});

const saveAffiliation = (type) => {
    affiliationForm.type = type;
    const existing = props.process.affiliations.find(a => a.type === type);
    if (existing) {
        affiliationForm.entity_name = existing.entity_name || '';
        affiliationForm.affiliation_number = existing.affiliation_number || '';
        affiliationForm.affiliation_date = existing.affiliation_date || '';
        affiliationForm.status = existing.status || 'PENDIENTE';
    }
    affiliationForm.post(route('contracting.process.affiliation', props.process.id), {
        forceFormData: true,
        onSuccess: () => {
            affiliationForm.file = null;
        }
    });
};

const sendToPayroll = () => {
    if (confirm('¿Confirmar que el proceso de contratación ha terminado y enviar a nómina?')) {
        useForm({}).post(route('contracting.process.send-to-payroll', props.process.id));
    }
};

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'APROBADO': return 'bg-green-100 text-green-800';
        case 'RECHAZADO': return 'bg-red-100 text-red-800';
        case 'CARGADO': return 'bg-blue-100 text-blue-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

</script>

<template>
    <Head title="Detalle de Contratación" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <Link :href="route('contracting.process.index')" class="text-gray-400 hover:text-brand-primary transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </Link>
                <h2 class="text-xl font-bold leading-tight text-white bg-brand-dark px-4 py-2 rounded-lg">
                    Expediente: {{ process.application.candidate.first_name }} {{ process.application.candidate.last_name }}
                </h2>
                <span class="px-3 py-1 bg-brand-primary/20 text-brand-dark font-bold rounded-full text-sm border border-brand-primary">
                    {{ process.status }}
                </span>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                
                <!-- Quick Info Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 flex-1">
                            <div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase">Candidato</h4>
                                <p class="mt-1 font-semibold text-gray-900">{{ process.application.candidate.first_name }} {{ process.application.candidate.last_name }}</p>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase">Cargo / Vacante</h4>
                                <p class="mt-1 font-semibold text-gray-900">{{ process.cargo }}</p>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase">Cliente</h4>
                                <p class="mt-1 font-semibold text-gray-900">{{ process.application.vacancy.client.business_name }}</p>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase">Salario</h4>
                                <p class="mt-1 font-semibold text-emerald-600">$ {{ Number(process.agreed_salary).toLocaleString() }}</p>
                            </div>
                        </div>
                        <div>
                            <PrimaryButton 
                                v-if="process.status === 'EMPLEADO_ACTIVO'"
                                @click="sendToPayroll"
                                class="!bg-emerald-600 hover:!bg-emerald-700"
                            >
                                Enviar a Nómina
                            </PrimaryButton>
                        </div>
                    </div>
                </div>

                <!-- Main Content with Sidebar Tabs -->
                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- Tabs -->
                    <div class="w-full lg:w-64 space-y-2">
                        <button 
                            v-for="(label, tab) in { 
                                documents: 'Documentación', 
                                medical: 'Examen Médico', 
                                contract: 'Contrato Laboral', 
                                affiliations: 'Afiliaciones' 
                            }" 
                            :key="tab"
                            @click="activeTab = tab"
                            :class="[
                                activeTab === tab 
                                    ? 'bg-brand-dark text-white shadow-md' 
                                    : 'bg-white text-gray-600 hover:bg-gray-50'
                            ]"
                            class="w-full text-left px-4 py-3 rounded-xl font-bold text-sm transition-all border border-gray-100"
                        >
                            {{ label }}
                        </button>
                    </div>

                    <!-- Content Card -->
                    <div class="flex-1 bg-white shadow-sm sm:rounded-lg border border-gray-100 min-h-[500px]">
                        
                        <!-- Documents -->
                        <div v-if="activeTab === 'documents'" class="p-6">
                            <h3 class="text-lg font-bold text-brand-dark mb-6 border-b pb-2">Validación de Documentos</h3>
                            <div class="space-y-4">
                                <div v-for="doc in process.documents" :key="doc.id" 
                                    class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100"
                                >
                                    <div class="flex items-center space-x-4">
                                        <div class="p-2 bg-white rounded-lg border">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 text-sm">{{ doc.document_type.name }}</p>
                                            <p class="text-[10px] text-gray-400 uppercase font-black">{{ doc.document_type.is_required ? 'Obligatorio' : 'Opcional' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span :class="['px-2.5 py-0.5 rounded-full text-xs font-bold border', getStatusBadgeClass(doc.status)]">
                                            {{ doc.status }}
                                        </span>
                                        <button @click="openDocModal(doc)" class="text-xs font-bold text-emerald-600 hover:underline">
                                            Gestionar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Medical Exam -->
                        <div v-if="activeTab === 'medical'" class="p-8 max-w-2xl mx-auto">
                            <h3 class="text-lg font-bold text-brand-dark mb-6 text-center">Salud Ocupacional</h3>
                            <form @submit.prevent="submitMedical" class="space-y-4 bg-gray-50 p-6 rounded-2xl border">
                                <div>
                                    <InputLabel value="IPS / Centro Médico" />
                                    <TextInput v-model="medicalForm.provider_name" class="w-full mt-1" />
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel value="Fecha" />
                                        <TextInput type="date" v-model="medicalForm.scheduled_date" class="w-full mt-1" />
                                    </div>
                                    <div>
                                        <InputLabel value="Resultado" />
                                        <select v-model="medicalForm.result" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                            <option value="">Seleccione...</option>
                                            <option v-for="res in enums.medical_results" :key="res.value" :value="res.value">{{ res.label }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <InputLabel value="Observaciones" />
                                    <textarea v-model="medicalForm.observations" class="w-full mt-1 border-gray-300 rounded-md shadow-sm h-24"></textarea>
                                </div>
                                <div>
                                    <InputLabel value="Cargar Resultado (PDF/Imagen)" />
                                    <div class="mt-1 flex items-center space-x-4">
                                        <input type="file" @input="medicalForm.file = $event.target.files[0]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-primary file:text-brand-dark hover:file:bg-brand-primary/80" />
                                        <a v-if="process.medical_exam?.file_path" :href="`/storage/${process.medical_exam.file_path}`" target="_blank" class="text-xs font-bold text-emerald-600 hover:underline shrink-0">Ver Actual</a>
                                    </div>
                                </div>
                                <div class="flex justify-center">
                                    <PrimaryButton :disabled="medicalForm.processing">Guardar Resultado</PrimaryButton>
                                </div>
                            </form>
                        </div>

                        <!-- Contract -->
                        <div v-if="activeTab === 'contract'" class="p-8 max-w-2xl mx-auto flex flex-col items-center">
                            <h3 class="text-lg font-bold text-brand-dark mb-6">Contrato Laboral</h3>
                            
                            <div v-if="process.contract" class="w-full bg-brand-dark rounded-2xl p-6 text-white text-center">
                                <p class="text-xs font-bold text-brand-primary uppercase mb-2">Estado del Contrato</p>
                                <h4 class="text-2xl font-black mb-6">{{ process.contract.status }}</h4>
                                <div class="flex justify-center gap-4">
                                    <a :href="`/storage/${process.contract.file_path}`" target="_blank" class="px-4 py-2 bg-white text-brand-dark rounded-lg font-bold text-xs hover:bg-gray-100 transition">Ver PDF</a>
                                    <button v-if="process.contract.status !== 'FIRMADO'" @click="signContract" class="px-4 py-2 bg-brand-primary text-brand-dark rounded-lg font-bold text-xs hover:bg-brand-primary/90 transition">Firmar Digitalmente</button>
                                </div>
                            </div>

                            <form @submit.prevent="uploadContract" class="w-full space-y-4 bg-gray-50 p-6 rounded-2xl border" v-else>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel value="Fecha Inicio" />
                                        <TextInput type="date" v-model="contractForm.start_date" class="w-full mt-1" />
                                    </div>
                                    <div>
                                        <InputLabel value="Fecha Fin" />
                                        <TextInput type="date" v-model="contractForm.end_date" class="w-full mt-1" />
                                    </div>
                                </div>
                                <div>
                                    <InputLabel value="Cargar PDF" />
                                    <input type="file" @input="contractForm.file = $event.target.files[0]" class="mt-1 block w-full text-sm text-gray-500" />
                                </div>
                                <div class="flex justify-center">
                                    <PrimaryButton :disabled="contractForm.processing">Cargar Contrato</PrimaryButton>
                                </div>
                            </form>
                        </div>

                        <!-- Affiliations -->
                        <div v-if="activeTab === 'affiliations'" class="p-6">
                            <h3 class="text-lg font-bold text-brand-dark mb-6 border-b pb-2">Seguridad Social</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div v-for="type in ['EPS', 'ARL', 'PENSIÓN', 'CESANTÍAS']" :key="type" class="p-4 bg-gray-50 rounded-xl border">
                                    <h5 class="font-bold text-sm text-gray-900 mb-4">{{ type }}</h5>
                                    <div class="space-y-4">
                                        <TextInput v-model="affiliationForm.entity_name" placeholder="Nombre entidad..." class="w-full text-sm" />
                                        
                                        <div class="flex flex-col space-y-1">
                                            <InputLabel value="Soporte (Archivo)" class="text-[10px]" />
                                            <div class="flex items-center space-x-2">
                                                <input type="file" @input="affiliationForm.file = $event.target.files[0]" class="block w-full text-[10px] text-gray-500" />
                                                <a v-if="process.affiliations.find(a => a.type === type)?.file_path" 
                                                    :href="`/storage/${process.affiliations.find(a => a.type === type).file_path}`" 
                                                    target="_blank" 
                                                    class="text-[10px] font-bold text-emerald-600 hover:underline shrink-0"
                                                >Ver</a>
                                            </div>
                                        </div>

                                        <div class="flex gap-2">
                                            <select v-model="affiliationForm.status" class="flex-1 text-sm border-gray-300 rounded-md">
                                                <option value="PENDIENTE">PENDIENTE</option>
                                                <option value="ACTIVA">ACTIVA</option>
                                            </select>
                                            <button @click="saveAffiliation(type)" class="px-4 py-2 bg-brand-dark text-white rounded-lg text-xs font-bold">OK</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Document Modal -->
        <Modal :show="showDocModal" @close="showDocModal = false">
            <div class="p-6">
                <h3 class="text-lg font-bold text-brand-dark mb-4">Validar: {{ selectedDoc?.document_type.name }}</h3>
                <div class="mb-6 p-4 bg-gray-50 rounded-xl border flex justify-between items-center">
                    <span class="text-sm font-bold text-gray-600">Archivo Adjunto:</span>
                    <a v-if="selectedDoc?.file_path" :href="`/storage/${selectedDoc.file_path}`" target="_blank" class="text-emerald-600 font-bold hover:underline">Ver Documento</a>
                    <span v-else class="text-red-500 font-bold">Sin archivo</span>
                </div>
                <form @submit.prevent="submitDocValidation" class="space-y-4">
                    <div>
                        <InputLabel value="Resultado" />
                        <select v-model="docForm.status" class="w-full mt-1 border-gray-300 rounded-md">
                            <option value="PENDIENTE">PENDIENTE</option>
                            <option value="APROBADO">APROBAR</option>
                            <option value="RECHAZADO">RECHAZAR</option>
                        </select>
                    </div>
                    <div>
                        <InputLabel value="Actualizar/Cargar Archivo" />
                        <input type="file" @input="docForm.file = $event.target.files[0]" class="mt-1 block w-full text-sm text-gray-500" />
                    </div>
                    <div v-if="docForm.status === 'RECHAZADO'">
                        <InputLabel value="Motivo Rechazo" />
                        <textarea v-model="docForm.rejection_reason" class="w-full mt-1 border-gray-300 rounded-md h-20"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <SecondaryButton @click="showDocModal = false">Cerrar</SecondaryButton>
                        <PrimaryButton :disabled="docForm.processing">Guardar</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>
