<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    users: {
        type: Object,
        required: true
    },
    areas: {
        type: Array,
        required: true
    }
});

const form = useForm({});

const toggleStatus = (user) => {
    if (confirm(`¿Estás seguro de querer ${user.is_active ? 'desactivar' : 'activar'} a este usuario?`)) {
        // En una implementación real más compleja se enviaría un PATCH/PUT al controlador con Axios
        // pero por simplicidad de Inertia, lo enviamos al endpoint destroy o un custom si prefieres.
        // Aquí simulamos desactivarlo vía DELETE que en el backend ajustamos a desactivación lógica.
        form.delete(route('admin.users.destroy', user.id), {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="Directorio de Usuarios" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Módulo Administrador: Usuarios del Sistema</h2>
                <Link
                    :href="route('admin.users.create')"
                    class="bg-brand-primary/90 hover:bg-brand-primary text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300"
                >
                    + Registrar Empleado
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div v-if="$page.props.flash && $page.props.flash.success" class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                    <p class="font-bold">Notificación</p>
                    <p>{{ $page.props.flash.success }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Empleado</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Área Fija</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-brand-primary/20 flex items-center justify-center text-brand-dark font-bold">
                                                        {{ user.name.charAt(0) }}
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ user.name }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ (areas.find(a => a.value === user.system_area)?.label) || 'No Asignada' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span 
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                            >
                                                {{ user.is_active ? 'Activo' : 'Inactivo (Suspendido)' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-3 flex justify-center items-center">
                                            <Link :href="route('admin.users.edit', user.id)" class="text-brand-primary hover:text-brand-dark dark:text-brand-primary dark:hover:text-brand-dark">
                                                Ver / Editar
                                            </Link>
                                            <button @click="toggleStatus(user)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                {{ user.is_active ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="users.data.length === 0">
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">No hay registros de empleados.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination can be added here using users.links -->

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
