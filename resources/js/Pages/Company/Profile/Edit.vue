<script setup>
import { useForm } from '@inertiajs/vue3';
import CompanyLayout from '@/Layouts/CompanyLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    client: Object,
});

const form = useForm({
    document_type: props.client.document_type || 'NIT',
    document_number: props.client.document_number || '',
    business_name: props.client.business_name || '',
    contact_name: props.client.contact_name || '',
    email: props.client.email || '',
    phone: props.client.phone || '',
    address: props.client.address || '',
    industry_sector: props.client.industry_sector || '',
});

const submit = () => {
    form.patch(route('company.profile.update'));
};
</script>

<template>
    <CompanyLayout title="Perfil de Empresa">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Perfil de Empresa
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ $page.props.flash.success }}
                </div>

                <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 p-8 shadow sm:rounded-lg">
                    
                    <h3 class="text-lg font-bold border-b pb-2 mb-4">Información de la Empresa</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <InputLabel for="business_name" value="Razón Social / Nombre" />
                            <TextInput id="business_name" v-model="form.business_name" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.business_name" />
                        </div>
                        <div>
                            <InputLabel for="document_type" value="Tipo de Documento" />
                            <select id="document_type" v-model="form.document_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md">
                                <option value="NIT">NIT</option>
                                <option value="CC">CC</option>
                                <option value="CE">CE</option>
                            </select>
                            <InputError :message="form.errors.document_type" />
                        </div>
                        <div>
                            <InputLabel for="document_number" value="Número de Documento" />
                            <TextInput id="document_number" v-model="form.document_number" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.document_number" />
                        </div>
                        <div>
                            <InputLabel for="industry_sector" value="Sector de la Industria" />
                            <TextInput id="industry_sector" v-model="form.industry_sector" class="mt-1 block w-full" />
                            <InputError :message="form.errors.industry_sector" />
                        </div>
                    </div>

                    <h3 class="text-lg font-bold border-b pb-2 mb-4 mt-8">Información de Contacto</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <InputLabel for="contact_name" value="Nombre del Contacto" />
                            <TextInput id="contact_name" v-model="form.contact_name" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.contact_name" />
                        </div>
                        <div>
                            <InputLabel for="email" value="Correo Electrónico" />
                            <TextInput id="email" type="email" v-model="form.email" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.email" />
                        </div>
                        <div>
                            <InputLabel for="phone" value="Teléfono" />
                            <TextInput id="phone" v-model="form.phone" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.phone" />
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <InputLabel for="address" value="Dirección" />
                            <TextInput id="address" v-model="form.address" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.address" />
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
