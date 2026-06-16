<script setup>
import { ref } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import axios from 'axios';

const props = defineProps({
    clients: Array,
    priorities: Array,
    previousVacancies: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    client_id: '',
    anonymous_company: false,
    title: '',
    positions_required: 1,
    department: '',
    employer_type: 'contratamos',
    priority: 'normal',
    
    contract_type: '',
    payroll_frequency: 'mensual',
    workday_type: 'tiempo_completo',
    schedule: '',
    
    salary_min: '',
    salary_max: '',
    has_bonuses: false,
    bonus_average: '',
    
    work_modality: 'presencial',
    address: '',
    city: '',
    department_name: '',
    
    min_education_level: 'profesional',
    experience_value: '',
    experience_unit: 'años',
    
    description: '',
    main_functions: '',
    optional_features: '',
    estimated_duration_months: '',
    
    data_treatment_accepted: true // Internally we assume it
});

const showPreviousModal = ref(false);

const loadTemplate = async (templateId) => {
    try {
        const response = await axios.get(route('selection.vacancies.template', templateId));
        const data = response.data;
        
        Object.keys(data).forEach(key => {
            if (Object.prototype.hasOwnProperty.call(form, key) && key !== 'id') {
                form[key] = data[key];
            }
        });
        
        showPreviousModal.value = false;
    } catch (error) {
        console.error("Error loading template", error);
    }
};

const submit = () => {
    form.post(route('selection.vacancies.store'));
};

const formatCurrency = (value) => {
    if (!value) return '$0';
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0
    }).format(value);
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Nueva Vacante" />

        <template #header>
            <div class="flex items-center space-x-4">
                <Link :href="route('selection.vacancies.index')" class="text-gray-400 hover:text-emerald-500 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </Link>
                <h2 class="text-xl font-bold leading-tight text-white bg-slate-800 px-4 py-2 rounded-lg">
                    Crear Nueva Vacante para Cliente
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                
                <!-- Toolbar for Template -->
                <div v-if="previousVacancies.length > 0" class="flex justify-end mb-4">
                    <SecondaryButton @click="showPreviousModal = true">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Usar vacante anterior
                    </SecondaryButton>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                    <form @submit.prevent="submit" class="space-y-8">
                        
                        <!-- CLIENTE Y GENERAL -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-bold border-b pb-2 text-slate-800">Información del Cliente y Cargo</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <InputLabel for="client_id" value="Empresa Cliente *" />
                                    <select v-model="form.client_id" required class="mt-1 block w-full border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm">
                                        <option value="" disabled>Seleccione un cliente...</option>
                                        <option v-for="client in clients" :key="client.id" :value="client.id">
                                            {{ client.business_name }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.client_id" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="title" value="Título de Cargo *" />
                                    <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required />
                                    <InputError :message="form.errors.title" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="positions_required" value="Cupos a Cubrir" />
                                    <TextInput id="positions_required" type="number" min="1" class="mt-1 block w-full" v-model="form.positions_required" required />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="flex items-center space-x-3 cursor-pointer p-3 bg-slate-50 rounded-lg border border-slate-200">
                                        <input type="checkbox" v-model="form.anonymous_company" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500" />
                                        <div>
                                            <span class="text-sm font-bold text-slate-900 uppercase tracking-tight">Publicar como Vacante Anónima</span>
                                            <p class="text-xs text-slate-500">El nombre del cliente no será visible públicamente.</p>
                                        </div>
                                    </label>
                                </div>

                                <div>
                                    <InputLabel for="priority" value="Prioridad" />
                                    <select v-model="form.priority" required class="mt-1 block w-full border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm">
                                        <option v-for="p in priorities" :key="p.value" :value="p.value">
                                            {{ p.label }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- SALARIO Y CONTRATO -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-bold border-b pb-2 text-slate-800">Condiciones y Salario</h3>
                            
                            <div v-if="form.employer_type === 'directa'" class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg">
                                <p class="text-sm text-blue-800 font-bold">Aviso: Contratación directa con el cliente.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="employer_type" value="Contratado por" />
                                    <select v-model="form.employer_type" class="mt-1 block w-full border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm">
                                        <option value="contratamos">Contratamos S.A.S</option>
                                        <option value="directa">Cliente Directo</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel value="Tipo de Contrato" />
                                    <select v-model="form.contract_type" class="mt-1 block w-full border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm">
                                        <option value="termino_indefinido">Indefinido</option>
                                        <option value="termino_fijo">Término Fijo</option>
                                        <option value="obra_labor">Obra o Labor</option>
                                        <option value="prestacion_servicios">Servicios</option>
                                    </select>
                                </div>

                                <div>
                                    <InputLabel value="Salario Mínimo *" />
                                    <TextInput type="number" v-model="form.salary_min" class="mt-1 block w-full" required />
                                    <InputError :message="form.errors.salary_min" />
                                </div>
                                <div>
                                    <InputLabel value="Salario Máximo *" />
                                    <TextInput type="number" v-model="form.salary_max" class="mt-1 block w-full" required />
                                    <InputError :message="form.errors.salary_max" />
                                </div>
                                <div class="md:col-span-2 text-xs text-emerald-600 font-bold italic">
                                    Valor Visualizado: {{ formatCurrency(form.salary_min) }} - {{ formatCurrency(form.salary_max) }}
                                </div>
                            </div>
                        </div>

                        <!-- REQUISITOS -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-bold border-b pb-2 text-slate-800">Requisitos</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel value="Nivel Educativo" />
                                    <select v-model="form.min_education_level" class="mt-1 block w-full border-gray-300 rounded-md">
                                        <option value="bachiller">Bachiller</option>
                                        <option value="tecnico">Técnico</option>
                                        <option value="tecnologo">Tecnólogo</option>
                                        <option value="profesional">Profesional</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel value="Experiencia Mínima *" />
                                    <div class="flex mt-1">
                                        <TextInput type="number" v-model="form.experience_value" class="w-1/3 rounded-r-none" required />
                                        <select v-model="form.experience_unit" class="w-2/3 border-gray-300 rounded-md rounded-l-none">
                                            <option value="meses">Meses</option>
                                            <option value="años">Años</option>
                                        </select>
                                    </div>
                                    <InputError :message="form.errors.experience_value" />
                                </div>
                            </div>
                        </div>

                        <!-- DESCRIPCIÓN -->
                        <div class="space-y-4">
                            <InputLabel for="description" value="Perfil / Funciones / Otros" />
                            <textarea id="description" v-model="form.description" rows="6" class="w-full border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm" placeholder="Detalle aquí las responsabilidades y beneficios..."></textarea>
                            <InputError :message="form.errors.description" />
                        </div>

                        <div class="flex items-center justify-end mt-4 pt-6 border-t border-gray-100">
                            <Link :href="route('selection.vacancies.index')" class="mr-6 text-sm text-gray-600 hover:text-gray-900 font-medium">
                                Cancelar
                            </Link>
                            <PrimaryButton :disabled="form.processing">
                                Registrar Vacante
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Plantillas -->
        <div v-if="showPreviousModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
            <div class="bg-white w-full max-w-lg rounded-xl shadow-xl p-6">
                <h3 class="text-lg font-bold mb-4">Seleccionar de Historial</h3>
                <div class="max-h-60 overflow-y-auto space-y-2 mb-6">
                    <button 
                        v-for="template in previousVacancies" 
                        :key="template.id"
                        @click="loadTemplate(template.id)"
                        class="w-full text-left p-3 rounded-lg border hover:bg-emerald-50 border-slate-200 transition-colors"
                    >
                        {{ template.title }} <span class="text-xs text-gray-500">({{ new Date(template.created_at).toLocaleDateString() }})</span>
                    </button>
                </div>
                <div class="flex justify-end">
                    <SecondaryButton @click="showPreviousModal = false">Cerrar</SecondaryButton>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
