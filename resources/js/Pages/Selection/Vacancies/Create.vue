<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    clients: Array,
    priorities: Array,
});

const form = useForm({
    client_id: '',
    title: '',
    description: '',
    positions_required: 1,
    priority: 'normal',
    expires_at: '',
});

const submit = () => {
    form.post(route('selection.vacancies.store'));
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Nueva Vacante" />

        <template #header>
            <div class="flex items-center space-x-4">
                <Link :href="route('selection.vacancies.index')" class="text-gray-400 hover:text-brand-primary transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </Link>
                <h2 class="text-xl font-bold leading-tight text-white bg-brand-dark px-4 py-2 rounded-lg">
                    Crear Nueva Vacante
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="client_id" value="Cliente" />
                                <select v-model="form.client_id" required class="mt-1 block w-full border-gray-300 focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm">
                                    <option value="" disabled>Seleccione un cliente...</option>
                                    <option v-for="client in clients" :key="client.id" :value="client.id">
                                        {{ client.business_name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.client_id" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="title" value="Título de Cargo" />
                                <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required placeholder="Ej: Desarrollador Backend" />
                                <InputError :message="form.errors.title" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="description" value="Descripción del Perfil / Requerimientos" />
                            <textarea id="description" v-model="form.description" rows="4" required class="mt-1 block w-full border-gray-300 focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm" placeholder="Detalle años de experiencia, habilidades, salario, etc."></textarea>
                            <InputError :message="form.errors.description" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <InputLabel for="positions_required" value="Cupos a Cubrir" />
                                <TextInput id="positions_required" type="number" min="1" class="mt-1 block w-full" v-model="form.positions_required" required />
                                <InputError :message="form.errors.positions_required" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="priority" value="Prioridad" />
                                <select v-model="form.priority" required class="mt-1 block w-full border-gray-300 focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm">
                                    <option v-for="p in priorities" :key="p.value" :value="p.value">
                                        {{ p.label }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.priority" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="expires_at" value="Fecha Límite Esperada" />
                                <TextInput id="expires_at" type="date" class="mt-1 block w-full" v-model="form.expires_at" />
                                <InputError :message="form.errors.expires_at" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4 pt-6 border-t border-gray-100">
                            <SecondaryButton @click="route('selection.vacancies.index')" type="button" class="mr-3">
                                Cancelar
                            </SecondaryButton>
                            <PrimaryButton :disabled="form.processing">
                                Registrar Vacante
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
