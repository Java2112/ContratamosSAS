<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import CompanyLayout from '@/Layouts/CompanyLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    vacancy: Object,
});

const form = useForm({
    title: props.vacancy.title || '',
    positions_required: props.vacancy.positions_required || 1,
    department: props.vacancy.department || '',
    employer_type: props.vacancy.employer_type || 'directa',
    
    contract_type: props.vacancy.contract_type || 'indefinido',
    payroll_frequency: props.vacancy.payroll_frequency || 'mensual',
    workday_type: props.vacancy.workday_type || 'tiempo_completo',
    schedule: props.vacancy.schedule || '',
    
    salary_type: props.vacancy.salary_type || 'rango',
    min_salary: props.vacancy.min_salary || '',
    max_salary: props.vacancy.max_salary || '',
    has_bonuses: props.vacancy.has_bonuses || false,
    bonus_average: props.vacancy.bonus_average || '',
    
    work_modality: props.vacancy.work_modality || 'presencial',
    address: props.vacancy.address || '',
    city: props.vacancy.city || '',
    department_name: props.vacancy.department_name || '',
    
    min_education_level: props.vacancy.min_education_level || 'profesional',
    min_experience_years: props.vacancy.min_experience_years !== null ? props.vacancy.min_experience_years : 1,
    languages: (props.vacancy.languages || []).join(', '),
    soft_skills: (props.vacancy.soft_skills || []).join(', '),
    hard_skills: (props.vacancy.hard_skills || []).join(', '),
    
    main_functions: props.vacancy.main_functions || '',
    optional_features: props.vacancy.optional_features || '',
    estimated_duration_months: props.vacancy.estimated_duration_months || '',
});

const submit = () => {
    form.put(route('company.vacancies.update', props.vacancy.id));
};
</script>

<template>
    <CompanyLayout :title="'Editar Vacante: ' + vacancy.title">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Editar Vacante: {{ vacancy.title }}
                </h2>
                <Link :href="route('company.vacancies.index')" class="text-gray-500 hover:text-gray-700">Cancelar</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 p-8 shadow sm:rounded-lg">
                    
                    <!-- INFORMACIÓN GENERAL -->
                    <h3 class="text-lg font-bold border-b pb-2 mb-4 text-gray-900 dark:text-gray-100">Información General</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <InputLabel for="title" value="Nombre del cargo" />
                            <TextInput id="title" v-model="form.title" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.title" />
                        </div>
                        <div>
                            <InputLabel for="positions_required" value="Número de vacantes" />
                            <TextInput id="positions_required" type="number" v-model="form.positions_required" class="mt-1 block w-full" required min="1" />
                        </div>
                        <div>
                            <InputLabel for="department" value="Área o departamento" />
                            <TextInput id="department" v-model="form.department" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel for="employer_type" value="Empleador" />
                            <select id="employer_type" v-model="form.employer_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                <option value="directa">Directo con el cliente</option>
                                <option value="contratamos">Con Contratamos</option>
                            </select>
                        </div>
                    </div>

                    <!-- CONDICIONES LABORALES -->
                    <h3 class="text-lg font-bold border-b pb-2 mb-4 mt-8 text-gray-900 dark:text-gray-100">Condiciones Laborales</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <InputLabel value="Tipo de contrato" />
                            <select v-model="form.contract_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                                <option value="obra_labor">Obra Labor</option>
                                <option value="indefinido">Indefinido</option>
                                <option value="fijo">Fijo</option>
                                <option value="prestacion_servicios">Prestación de servicios</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel value="Frecuencia de pago" />
                            <select v-model="form.payroll_frequency" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                                <option value="mensual">Mensual</option>
                                <option value="quincenal">Quincenal</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel value="Jornada laboral" />
                            <select v-model="form.workday_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                                <option value="tiempo_completo">Tiempo Completo</option>
                                <option value="medio_tiempo">Medio Tiempo</option>
                                <option value="flexible">Flexible</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel for="schedule" value="Horario laboral" />
                            <TextInput id="schedule" v-model="form.schedule" placeholder="L-V 8am a 5pm" class="mt-1 block w-full" />
                        </div>
                    </div>

                    <!-- SALARIO -->
                    <h3 class="text-lg font-bold border-b pb-2 mb-4 mt-8 text-gray-900 dark:text-gray-100">Salario</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <InputLabel value="Oferta salarial" />
                            <select v-model="form.salary_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                                <option value="rango">Rango salarial definido</option>
                                <option value="convenir">A convenir</option>
                            </select>
                        </div>
                        <template v-if="form.salary_type === 'rango'">
                            <div>
                                <InputLabel for="min_salary" value="Valor mínimo" />
                                <TextInput id="min_salary" type="number" v-model="form.min_salary" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <InputLabel for="max_salary" value="Valor máximo" />
                                <TextInput id="max_salary" type="number" v-model="form.max_salary" class="mt-1 block w-full" />
                            </div>
                        </template>
                        <div class="col-span-full">
                            <label class="flex items-center">
                                <input type="checkbox" v-model="form.has_bonuses" class="rounded border-gray-300" />
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">¿Bonos o comisiones adicionales?</span>
                            </label>
                            <div v-if="form.has_bonuses" class="mt-2">
                                <InputLabel for="bonus_average" value="Valor promedio de bonos" />
                                <TextInput id="bonus_average" type="number" v-model="form.bonus_average" class="mt-1 block w-full" />
                            </div>
                        </div>
                    </div>

                    <!-- MODALIDAD Y UBICACIÓN -->
                    <h3 class="text-lg font-bold border-b pb-2 mb-4 mt-8 text-gray-900 dark:text-gray-100">Modalidad y Ubicación</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <InputLabel value="Modalidad de trabajo" />
                            <select v-model="form.work_modality" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                                <option value="presencial">Presencial</option>
                                <option value="hibrido">Híbrido</option>
                                <option value="remoto">Remoto</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel for="address" value="Dirección" />
                            <TextInput id="address" v-model="form.address" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel for="city" value="Ciudad" />
                            <TextInput id="city" v-model="form.city" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel for="department_name" value="Departamento (Región)" />
                            <TextInput id="department_name" v-model="form.department_name" class="mt-1 block w-full" />
                        </div>
                    </div>

                    <!-- REQUISITOS DEL CANDIDATO -->
                    <h3 class="text-lg font-bold border-b pb-2 mb-4 mt-8 text-gray-900 dark:text-gray-100">Requisitos del Candidato</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <InputLabel value="Nivel educativo mínimo" />
                            <select v-model="form.min_education_level" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                                <option value="ninguno">Ninguno</option>
                                <option value="primaria">Primaria</option>
                                <option value="bachiller">Bachiller</option>
                                <option value="tecnico">Técnico</option>
                                <option value="tecnologo">Tecnólogo</option>
                                <option value="profesional">Profesional</option>
                                <option value="posgrado">Posgrado</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel for="min_experience_years" value="Experiencia mínima (Años)" />
                            <TextInput id="min_experience_years" type="number" v-model="form.min_experience_years" class="mt-1 block w-full" min="0" />
                        </div>
                        <div class="col-span-full">
                            <InputLabel value="Idiomas (Separados por coma)" />
                            <TextInput v-model="form.languages" placeholder="Inglés, Portugués..." class="mt-1 block w-full" />
                            <small class="text-gray-500">Ej: Inglés (B2), Francés</small>
                        </div>
                        <div class="col-span-full">
                            <InputLabel value="Competencias Blancas" />
                            <TextInput v-model="form.soft_skills" placeholder="Resolución de problemas, Liderazgo..." class="mt-1 block w-full" />
                        </div>
                        <div class="col-span-full">
                            <InputLabel value="Competencias Técnicas" />
                            <TextInput v-model="form.hard_skills" placeholder="React, Node.js, Excel avanzado..." class="mt-1 block w-full" />
                        </div>
                    </div>

                    <!-- DESCRIPCION DEL CARGO -->
                    <h3 class="text-lg font-bold border-b pb-2 mb-4 mt-8 text-gray-900 dark:text-gray-100">Descripción del Cargo</h3>
                    <div class="mb-6 space-y-4">
                        <div>
                            <InputLabel for="main_functions" value="Funciones principales" />
                            <textarea id="main_functions" v-model="form.main_functions" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md dark:placeholder-gray-500"></textarea>
                        </div>
                        <div>
                            <InputLabel for="optional_features" value="Otras características opcionales" />
                            <textarea id="optional_features" v-model="form.optional_features" rows="2" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md dark:placeholder-gray-500"></textarea>
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
