<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    document_type: 'NIT',
    document_number: '',
    business_name: '',
    contact_name: '',
    email: '',
    phone: '',
    address: '',
    industry_sector: '',
});

const submit = () => {
    form.post(route('commercial.clients.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Registrar Cliente" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <Link :href="route('commercial.clients.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Registrar Nuevo Cliente Empresarial</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl p-8 border border-gray-100 dark:border-gray-700">
                    
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Información de la Empresa</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Ingresa los datos fiscales y de contacto de la compañía aliada. El sistema creará sus credenciales automáticamente.</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tipo de Documento -->
                            <div>
                                <InputLabel for="document_type" value="Tipo de Documento" />
                                <select 
                                    id="document_type" 
                                    v-model="form.document_type" 
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-brand-primary dark:focus:border-brand-primary focus:ring-brand-primary dark:focus:ring-brand-primary rounded-md shadow-sm"
                                >
                                    <option value="NIT">NIT</option>
                                    <option value="CC">Cédula de Ciudadanía</option>
                                    <option value="CE">Cédula de Extranjería</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.document_type" />
                            </div>

                            <!-- Número de Documento -->
                            <div>
                                <InputLabel for="document_number" value="Número de Documento" />
                                <TextInput
                                    id="document_number"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.document_number"
                                    required
                                    placeholder="Ej: 900123456-1"
                                />
                                <InputError class="mt-2" :message="form.errors.document_number" />
                            </div>

                            <!-- Razón Social -->
                            <div class="md:col-span-2">
                                <InputLabel for="business_name" value="Razón Social / Nombre Comercial" />
                                <TextInput
                                    id="business_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.business_name"
                                    required
                                    placeholder="Ej: Tecnología y Servicios S.A."
                                />
                                <InputError class="mt-2" :message="form.errors.business_name" />
                            </div>

                            <!-- Nombre de Contacto -->
                            <div>
                                <InputLabel for="contact_name" value="Nombre del Contacto Principal" />
                                <TextInput
                                    id="contact_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.contact_name"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.contact_name" />
                            </div>

                            <!-- Sector de la Industria -->
                            <div>
                                <InputLabel for="industry_sector" value="Sector Comercial" />
                                <TextInput
                                    id="industry_sector"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.industry_sector"
                                    required
                                    placeholder="Ej: Retail, Tecnología, Manufactura..."
                                />
                                <InputError class="mt-2" :message="form.errors.industry_sector" />
                            </div>

                            <!-- Email -->
                            <div class="md:col-span-2">
                                <InputLabel for="email" value="Correo Electrónico (Será su Usuario)" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full bg-blue-50 dark:bg-gray-800"
                                    v-model="form.email"
                                    required
                                />
                                <p class="mt-1 text-xs text-blue-600 dark:text-blue-400">Automáticamente le generaremos y enviaremos una contraseña temporal a este correo y al whatsapp.</p>
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <!-- Teléfono -->
                            <div>
                                <InputLabel for="phone" value="Teléfono / Celular (WhatsApp)" />
                                <TextInput
                                    id="phone"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.phone"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.phone" />
                            </div>

                            <!-- Dirección -->
                            <div>
                                <InputLabel for="address" value="Dirección Física" />
                                <TextInput
                                    id="address"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.address"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.address" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-6 border-t border-gray-100 dark:border-gray-700 mt-8">
                            <span v-if="form.recentlySuccessful" class="text-sm text-green-600 mr-4">
                                Cliente creado exitosamente.
                            </span>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="px-8 py-3 bg-blue-600 hover:bg-blue-700">
                                {{ form.processing ? 'Registrando...' : 'Registrar Cliente y Enviar Credenciales' }}
                            </PrimaryButton>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
