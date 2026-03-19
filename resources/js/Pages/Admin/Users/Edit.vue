<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    user: Object,
    SystemArea: Array,
    UserRole: Array,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    system_area: props.user.system_area || '',
    role: props.user.role || '',
    is_active: props.user.is_active ? 1 : 0,
});

const submit = () => {
    form.put(route('admin.users.update', props.user.id));
};
</script>

<template>
    <Head title="Editar Empleado" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Gestión de Empleado: {{ user.name }}</h2>
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

                        <!-- Contraseña Opcional -->
                        <div>
                            <InputLabel for="password" value="Nueva Contraseña (Opcional)" />
                            <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" autocomplete="new-password" placeholder="Dejar en blanco para mantener la actual" />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-100 dark:border-gray-700 pt-4 mt-6">
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

                            <!-- Estado (Activo/Inactivo) -->
                            <div>
                                <InputLabel for="is_active" value="Estado de Conexión" />
                                <select 
                                    id="is_active" 
                                    v-model="form.is_active" 
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-brand-primary dark:focus:border-brand-primary focus:ring-brand-primary dark:focus:ring-brand-primary rounded-md shadow-sm font-semibold"
                                    :class="form.is_active == 1 ? 'text-green-600' : 'text-red-600'"
                                    required
                                >
                                    <option value="1">Cuenta Activa (Permitir Login)</option>
                                    <option value="0">Cuenta Suspendida (Bloqueada)</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Suspendiendo la cuenta previene el acceso sin borrar el historial de auditoría de este empleado.</p>
                                <InputError class="mt-2" :message="form.errors.is_active" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-6 border-t border-gray-100 dark:border-gray-700 mt-8">
                            <Link :href="route('admin.users.index')" class="mr-4 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                                Cancelar
                            </Link>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Guardar Cambios
                            </PrimaryButton>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
