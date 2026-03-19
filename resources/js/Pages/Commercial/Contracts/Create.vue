<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    clients: {
        type: Array,
        required: true
    }
});

const form = useForm({
    client_id: '',
    start_date: new Date().toISOString().split('T')[0],
    end_date: '',
    administration_fee_percentage: 8.5,
});

const submit = () => {
    form.post(route('commercial.contracts.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Redactar Contrato" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <Link :href="route('commercial.contracts.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Formalizar Nuevo Contrato</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl p-8 border border-gray-100 dark:border-gray-700">
                    
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Parámetros del Contrato</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Selecciona la empresa cliente y formaliza las tarifas de servicio y vigencia. Esto generará el soporte legal en PDF.</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <!-- Selección del Cliente -->
                        <div>
                            <InputLabel for="client_id" value="Empresa Cliente (Aliado Comercial)" />
                            <select 
                                id="client_id" 
                                v-model="form.client_id" 
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-brand-primary dark:focus:border-brand-primary focus:ring-brand-primary dark:focus:ring-brand-primary rounded-md shadow-sm"
                                required
                            >
                                <option disabled value="">Selecciona un cliente del directorio...</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">
                                    {{ client.business_name }} - {{ client.document_type }} {{ client.document_number }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.client_id" />
                            <div v-if="clients.length === 0" class="mt-2 text-sm text-red-500">No hay clientes creados. Primero debes registrar una empresa en el directorio.</div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Fecha de Inicio -->
                            <div>
                                <InputLabel for="start_date" value="Fecha de Inicio" />
                                <TextInput
                                    id="start_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.start_date"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.start_date" />
                            </div>

                            <!-- Fecha de Fin -->
                            <div>
                                <InputLabel for="end_date" value="Fecha de Terminación (Opcional)" />
                                <TextInput
                                    id="end_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.end_date"
                                    placeholder="Dejar vacío si es indefinido"
                                />
                                <InputError class="mt-2" :message="form.errors.end_date" />
                            </div>
                        </div>

                        <!-- Porcentaje de Administración AIU -->
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                            <InputLabel for="administration_fee_percentage" value="Tarifa de Administración (AIU %)" />
                            <div class="relative mt-1 rounded-md shadow-sm max-w-xs">
                                <TextInput
                                    id="administration_fee_percentage"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    class="block w-full pr-12 text-lg font-bold text-brand-dark dark:text-brand-primary border-brand-primary dark:border-brand-primary focus:ring-brand-primary focus:border-brand-primary"
                                    v-model="form.administration_fee_percentage"
                                    required
                                />
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500 font-bold text-lg">
                                    %
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Porcentaje de cobro sobre la nómina facturada por intermediación.</p>
                            <InputError class="mt-2" :message="form.errors.administration_fee_percentage" />
                        </div>

                        <div class="flex items-center justify-end pt-6 border-t border-gray-100 dark:border-gray-700 mt-8">
                            <PrimaryButton :class="{ 'opacity-25': form.processing || clients.length === 0 }" :disabled="form.processing || clients.length === 0" class="px-8 py-3 bg-blue-600 hover:bg-blue-700">
                                {{ form.processing ? 'Generando Contrato...' : 'Formalizar Contrato y Crear PDF' }}
                            </PrimaryButton>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
