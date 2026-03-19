<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    SystemArea: Array,
    UserRole: Array,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    system_area: '',
    role: '',
});

const submit = () => {
    form.post(route('admin.users.store'));
};
</script>

<template>
    <Head title="Registrar Empleado" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Registrar Nuevo Empleado</h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-8 border border-gray-100 dark:border-gray-700">
                    
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre -->
                            <div>
                                <InputLabel for="name" value="Nombre Completo" />
                                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <!-- Correo -->
                            <div>
                                <InputLabel for="email" value="Correo Corporativo" />
                                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>
                        </div>

                        <!-- Contraseña -->
                        <div>
                            <InputLabel for="password" value="Contraseña de Acceso" />
                            <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="new-password" />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <!-- Área Fija -->
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-4 mt-6">
                            <!-- Área de Sistema -->
                            <div>
                                <InputLabel for="system_area" value="Área de Operación" />
                                <select id="system_area" v-model="form.system_area" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="" disabled>Seleccione un área...</option>
                                    <option v-for="area in SystemArea" :key="area.value" :value="area.value">
                                        {{ area.label }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.system_area" />
                            </div>

                            <!-- Rol del Usuario -->
                            <div>
                                <InputLabel for="role" value="Rol o Cargo (Opcional)" />
                                <select id="role" v-model="form.role" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="" disabled>Seleccione un rol...</option>
                                    <option v-for="role in UserRole" :key="role.value" :value="role.value">
                                        {{ role.label }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.role" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-6 border-t border-gray-100 dark:border-gray-700 mt-8">
                            <Link :href="route('admin.users.index')" class="mr-4 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                                Cancelar
                            </Link>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Registrar y Asignar Área
                            </PrimaryButton>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
