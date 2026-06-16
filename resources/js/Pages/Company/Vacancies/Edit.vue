<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import CompanyLayout from '@/Layouts/CompanyLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    vacancy: Object,
});

const form = useForm({
    id: props.vacancy.id,
    anonymous_company: props.vacancy.anonymous_company ?? false,
    title: props.vacancy.title || '',
    positions_required: props.vacancy.positions_required || 1,
    department: props.vacancy.department || '',
    employer_type: props.vacancy.employer_type || 'contratamos',
    priority: props.vacancy.priority || 'normal',
    
    contract_type: props.vacancy.contract_type || '',
    payroll_frequency: props.vacancy.payroll_frequency || 'mensual',
    workday_type: props.vacancy.workday_type || 'tiempo_completo',
    schedule: props.vacancy.schedule || '',
    
    salary_min: props.vacancy.salary_min || '',
    salary_max: props.vacancy.salary_max || '',
    has_bonuses: props.vacancy.has_bonuses || false,
    bonus_average: props.vacancy.bonus_average || '',
    
    work_modality: props.vacancy.work_modality || 'presencial',
    address: props.vacancy.address || '',
    city: props.vacancy.city || '',
    department_name: props.vacancy.department_name || '',
    
    min_education_level: props.vacancy.min_education_level || 'profesional',
    experience_value: props.vacancy.experience_value || '',
    experience_unit: props.vacancy.experience_unit || 'años',
    
    description: props.vacancy.description || '',
    main_functions: props.vacancy.main_functions || '',
    optional_features: props.vacancy.optional_features || '',
    estimated_duration_months: props.vacancy.estimated_duration_months || '',
});

const submit = () => {
    form.put(route('company.vacancies.update', form.id));
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
    <Head :title="'Editar Vacante: ' + vacancy.title" />

    <CompanyLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Editar Vacante: {{ vacancy.title }}
                </h2>
                <Link :href="route('company.vacancies.index')" class="text-gray-500 hover:text-emerald-600 font-medium">Volver</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 p-8 shadow sm:rounded-lg">
                    
                    <!-- INFORMACIÓN GENERAL -->
                    <h3 class="text-lg font-bold border-b pb-2 mb-4 dark:text-white text-gray-900">Información General</h3>
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
                                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-tight">Vacante Anónima</span>
                                    <p class="text-xs text-gray-500">Oculta el nombre de tu empresa en esta vacante.</p>
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

                    <!-- SALARIO Y CONTRATO -->
                    <h3 class="text-lg font-bold border-b pb-2 mb-4 mt-8 dark:text-white text-gray-900">Condiciones y Salario</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <InputLabel for="employer_type" value="Empleador" />
                            <select id="employer_type" v-model="form.employer_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                                <option value="contratamos">CONTRATAMOS S.A.S</option>
                                <option value="directa">Empresa Cliente (Directa)</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel value="Tipo de contrato" />
                            <select v-model="form.contract_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                                <option value="obra_labor">Obra Labor</option>
                                <option value="termino_indefinido">Término Indefinido</option>
                                <option value="termino_fijo">Término Fijo</option>
                                <option value="prestacion_servicios">Prestación de servicios</option>
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
                    </div>

                    <!-- REQUISITOS -->
                    <h3 class="text-lg font-bold border-b pb-2 mb-4 mt-8 dark:text-white text-gray-900">Requisitos</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <InputLabel value="Nivel educativo" />
                            <select v-model="form.min_education_level" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                                <option value="bachiller">Bachiller</option>
                                <option value="tecnico">Técnico</option>
                                <option value="tecnologo">Tecnólogo</option>
                                <option value="profesional">Profesional</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel value="Experiencia Mínima *" />
                            <div class="flex mt-1">
                                <TextInput type="number" v-model="form.experience_value" class="w-1/3 rounded-r-none border-r-0" required />
                                <select v-model="form.experience_unit" class="w-2/3 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md rounded-l-none">
                                    <option value="meses">Meses</option>
                                    <option value="años">Años</option>
                                </select>
                            </div>
                            <InputError :message="form.errors.experience_value" />
                        </div>
                    </div>

                    <!-- ACTIONS -->
                    <div class="flex items-center justify-end mt-8 border-t pt-4">
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Guardar Cambios
                        </PrimaryButton>
                    </div>

                </form>
            </div>
        </div>
    </CompanyLayout>
</template>
