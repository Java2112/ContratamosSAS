<script setup>
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import CompanyLayout from '@/Layouts/CompanyLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import axios from 'axios';

const props = defineProps({
    previousVacancies: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
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
    
    data_treatment_accepted: false
});

const showPreviousModal = ref(false);

const loadTemplate = async (templateId) => {
    try {
        const response = await axios.get(route('company.vacancies.template', templateId));
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
    form.post(route('company.vacancies.store'));
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
    <Head title="Crear Vacante" />

    <CompanyLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Crear Nueva Vacante
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Toolbar for Template -->
                <div v-if="previousVacancies.length > 0" class="flex justify-end mb-4">
                    <SecondaryButton @click="showPreviousModal = true">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Usar vacante anterior
                    </SecondaryButton>
                </div>

                <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 p-8 shadow sm:rounded-lg">
                    
                    <!-- INFORMACIÓN GENERAL -->
                    <h3 class="text-lg font-bold border-b pb-2 mb-4 dark:text-white">Información General</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <InputLabel for="title" value="Nombre del cargo *" />
                            <TextInput id="title" v-model="form.title" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.title" />
                        </div>
                        <div>
                            <InputLabel for="positions_required" value="Número de vacantes" />
                            <TextInput id="positions_required" type="number" v-model="form.positions_required" class="mt-1 block w-full" required min="1" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="flex items-center space-x-3 cursor-pointer p-3 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-100 dark:border-slate-700">
                                <input type="checkbox" v-model="form.anonymous_company" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500" />
                                <div>
                                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-tight">Publicar como Vacante Anónima</span>
                                    <p class="text-xs text-gray-500">Oculta el nombre de la empresa a candidatos externos.</p>
                                </div>
                            </label>
                        </div>

                        <div>
                            <InputLabel for="department" value="Área o departamento" />
                            <TextInput id="department" v-model="form.department" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel for="priority" value="Prioridad" />
                            <select id="priority" v-model="form.priority" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                                <option value="low">Baja</option>
                                <option value="normal">Normal</option>
                                <option value="high">Alta</option>
                                <option value="urgent">Urgente</option>
                            </select>
                        </div>
                    </div>

                    <!-- CONDICIONES LABORALES -->
                    <h3 class="text-lg font-bold border-b pb-2 mb-4 mt-8 dark:text-white">Condiciones Laborales y Salario</h3>
                    
                    <div v-if="form.employer_type === 'directa'" class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg">
                        <p class="text-sm text-blue-800 font-bold">
                            "En caso de contratación, el proceso laboral y contractual se realizará directamente con la empresa cliente y no con CONTRATAMOS."
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <InputLabel for="employer_type" value="Empleador" />
                            <select id="employer_type" v-model="form.employer_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                <option value="contratamos">CONTRATAMOS S.A.S</option>
                                <option value="directa">Empresa Cliente (Directa)</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel value="Tipo de contrato" />
                            <select v-model="form.contract_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                                <option value="">Seleccione...</option>
                                <option value="obra_labor">Obra Labor</option>
                                <option value="termino_indefinido">Término Indefinido</option>
                                <option value="termino_fijo">Término Fijo</option>
                                <option value="prestacion_servicios">Prestación de servicios</option>
                            </select>
                        </div>

                        <div>
                            <InputLabel for="salary_min" value="Salario Mínimo *" />
                            <TextInput id="salary_min" type="number" v-model="form.salary_min" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.salary_min" />
                        </div>
                        <div>
                            <InputLabel for="salary_max" value="Salario Máximo *" />
                            <TextInput id="salary_max" type="number" v-model="form.salary_max" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.salary_max" />
                        </div>
                        <div class="md:col-span-2 text-xs text-emerald-600 font-semibold">
                            Rango: {{ formatCurrency(form.salary_min) }} - {{ formatCurrency(form.salary_max) }}
                        </div>

                        <div class="col-span-full">
                            <label class="flex items-center">
                                <input type="checkbox" v-model="form.has_bonuses" class="rounded border-gray-300 text-emerald-600" />
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">¿Bonos o comisiones adicionales?</span>
                            </label>
                            <div v-if="form.has_bonuses" class="mt-2">
                                <InputLabel for="bonus_average" value="Valor promedio de bonos" />
                                <TextInput id="bonus_average" type="number" v-model="form.bonus_average" class="mt-1 block w-full" />
                            </div>
                        </div>
                    </div>

                    <!-- REQUISITOS -->
                    <h3 class="text-lg font-bold border-b pb-2 mb-4 mt-8 dark:text-white">Requisitos del Candidato</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <InputLabel value="Nivel educativo mínimo" />
                            <select v-model="form.min_education_level" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                                <option value="bachiller">Bachiller</option>
                                <option value="tecnico">Técnico</option>
                                <option value="tecnologo">Tecnólogo</option>
                                <option value="profesional">Profesional</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel value="Experiencia Laboral Requerida *" />
                            <div class="flex mt-1">
                                <TextInput type="number" v-model="form.experience_value" class="block w-1/3 rounded-r-none border-r-0" required />
                                <select v-model="form.experience_unit" class="block w-2/3 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md rounded-l-none">
                                    <option value="meses">Meses</option>
                                    <option value="años">Años</option>
                                </select>
                            </div>
                            <InputError :message="form.errors.experience_value" />
                        </div>
                    </div>

                    <!-- DESCRIPCION -->
                    <h3 class="text-lg font-bold border-b pb-2 mb-4 mt-8 dark:text-white">Descripción del Cargo</h3>
                    <div class="mb-6 space-y-4">
                        <div>
                            <InputLabel for="description" value="Descripción General" />
                            <textarea id="description" v-model="form.description" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"></textarea>
                        </div>
                        <div>
                            <InputLabel for="main_functions" value="Funciones principales" />
                            <textarea id="main_functions" v-model="form.main_functions" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"></textarea>
                        </div>
                    </div>

                    <!-- TRATAMIENTO DE DATOS -->
                    <div class="mt-10 bg-gray-50 dark:bg-gray-900 p-4 rounded-lg text-sm border dark:border-gray-700">
                        <label class="flex items-start">
                            <input type="checkbox" v-model="form.data_treatment_accepted" class="mt-1 rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500" required />
                            <span class="ml-3 text-gray-600 dark:text-gray-400">
                                <strong>Tratamiento de Datos:</strong> Autorizo el tratamiento de mis datos personales para procesos de selección de conformidad con la Ley 1581 de 2012.
                            </span>
                        </label>
                    </div>

                    <!-- ACTIONS -->
                    <div class="flex items-center justify-end mt-8 border-t pt-4">
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing || !form.data_treatment_accepted">
                            Publicar Vacante
                        </PrimaryButton>
                    </div>

                </form>
            </div>
        </div>

        <!-- Modal Plantillas -->
        <div v-if="showPreviousModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
            <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-xl shadow-xl p-6">
                <h3 class="text-lg font-bold mb-4 dark:text-white">Usar Vacante Anterior</h3>
                <div class="max-h-60 overflow-y-auto space-y-2 mb-6">
                    <button 
                        v-for="template in previousVacancies" 
                        :key="template.id"
                        @click="loadTemplate(template.id)"
                        class="w-full text-left p-3 rounded-lg border hover:bg-emerald-50 dark:hover:bg-emerald-900/20 border-slate-200 dark:border-slate-700 dark:text-gray-300"
                    >
                        {{ template.title }} <span class="text-xs text-gray-500">({{ new Date(template.created_at).toLocaleDateString() }})</span>
                    </button>
                </div>
                <div class="flex justify-end">
                    <SecondaryButton @click="showPreviousModal = false">Cerrar</SecondaryButton>
                </div>
            </div>
        </div>
    </CompanyLayout>
</template>
