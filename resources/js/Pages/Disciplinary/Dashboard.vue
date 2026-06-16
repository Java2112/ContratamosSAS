<script setup>
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    kpis: Object,
    activeEmployees: Array,
    records: Object,
    filters: Object
});

const search = ref(props.filters.search || '');
const employeeSearchQuery = ref('');
const searchedEmployees = ref([]);

// Watch employee search input to filter locally or query backend
watch(employeeSearchQuery, (value) => {
    if (!value) {
        searchedEmployees.value = [];
        return;
    }
    
    // Quick local filtering of already fetched activeEmployees or query with Inertia reload
    router.reload({
        data: { search: value },
        only: ['activeEmployees'],
        preserveState: true,
        onSuccess: () => {
            searchedEmployees.value = props.activeEmployees;
        }
    });
});

const handleSearch = () => {
    router.get(route('disciplinary.dashboard'), { search: search.value }, { preserveState: true });
};

// Helper for color-coding statuses
const getStatusClasses = (status) => {
    switch (status) {
        case 'BORRADOR':
            return 'bg-gray-100 text-gray-800 border-gray-200';
        case 'EN_PROCESO':
            return 'bg-amber-100 text-amber-800 border-amber-200';
        case 'FINALIZADO':
            return 'bg-blue-100 text-blue-800 border-blue-200';
        case 'PDF_GENERADO':
            return 'bg-purple-100 text-purple-800 border-purple-200';
        case 'CERRADO':
            return 'bg-emerald-100 text-emerald-800 border-emerald-200';
        default:
            return 'bg-gray-100 text-gray-800 border-gray-200';
    }
};
</script>

<template>
    <Head title="Dashboard de Descargos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Módulo de Descargos Disciplinarios
            </h2>
        </template>

        <div class="py-12 bg-gray-50/50 min-h-[calc(100vh-80px)]">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <!-- KPI METRICS -->
                <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm transition hover:-translate-y-1 hover:shadow-md duration-300">
                        <div class="text-gray-400 text-sm font-semibold uppercase tracking-wider">Total Casos</div>
                        <div class="text-3xl font-black text-brand-dark mt-2">{{ kpis.total }}</div>
                    </div>
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm transition hover:-translate-y-1 hover:shadow-md duration-300">
                        <div class="text-gray-400 text-sm font-semibold uppercase tracking-wider">Borradores</div>
                        <div class="text-3xl font-black text-gray-600 mt-2">{{ kpis.borrador }}</div>
                    </div>
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm transition hover:-translate-y-1 hover:shadow-md duration-300">
                        <div class="text-gray-400 text-sm font-semibold uppercase tracking-wider">En Proceso</div>
                        <div class="text-3xl font-black text-amber-500 mt-2">{{ kpis.en_proceso }}</div>
                    </div>
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm transition hover:-translate-y-1 hover:shadow-md duration-300">
                        <div class="text-gray-400 text-sm font-semibold uppercase tracking-wider">Finalizados</div>
                        <div class="text-3xl font-black text-blue-600 mt-2">{{ kpis.finalizado }}</div>
                    </div>
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm transition hover:-translate-y-1 hover:shadow-md duration-300">
                        <div class="text-gray-400 text-sm font-semibold uppercase tracking-wider">Cerrados</div>
                        <div class="text-3xl font-black text-emerald-600 mt-2">{{ kpis.cerrado }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- SEARCH ACTIVE EMPLOYEES BOX -->
                    <div class="lg:col-span-1 bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Iniciar Nuevo Proceso</h3>
                            <p class="text-sm text-gray-500 mt-1">Busque un empleado activo para aperturar una diligencia de descargos.</p>
                        </div>
                        
                        <div class="relative">
                            <input 
                                v-model="employeeSearchQuery"
                                type="text"
                                placeholder="Buscar por Nombre o Cédula..."
                                class="w-full bg-gray-50 border-gray-200 rounded-2xl focus:border-brand-primary focus:ring focus:ring-brand-primary/20 text-sm py-3 px-4"
                            />
                            <div class="absolute right-4 top-3.5 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.637 10.637Z" />
                                </svg>
                            </div>
                        </div>

                        <!-- SEARCH RESULTS -->
                        <div class="space-y-3 max-h-[350px] overflow-y-auto pr-1">
                            <template v-if="searchedEmployees.length > 0">
                                <div 
                                    v-for="emp in searchedEmployees" 
                                    :key="emp.id"
                                    class="p-4 bg-gray-50 hover:bg-brand-primary/5 border border-transparent hover:border-brand-primary/20 rounded-2xl transition duration-200 flex flex-col justify-between"
                                >
                                    <div>
                                        <div class="font-bold text-gray-800 text-sm">{{ emp.name }}</div>
                                        <div class="text-xs text-gray-500 mt-1">C.C. {{ emp.document_number }} • {{ emp.cargo }}</div>
                                        <div class="text-xs text-brand-primary font-semibold mt-1">{{ emp.client_name }}</div>
                                    </div>
                                    <div class="mt-3 flex gap-2">
                                        <Link 
                                            :href="route('disciplinary.create', emp.id)"
                                            class="flex-1 text-center bg-brand-dark hover:bg-brand-primary text-white text-xs font-bold py-2 px-3 rounded-xl transition duration-200"
                                        >
                                            Iniciar Descargos
                                        </Link>
                                    </div>
                                </div>
                            </template>
                            <template v-else-if="employeeSearchQuery">
                                <div class="text-center py-6 text-gray-400 text-sm">
                                    No se encontraron empleados activos.
                                </div>
                            </template>
                            <template v-else>
                                <div class="text-center py-10 text-gray-400 text-xs border border-dashed border-gray-200 rounded-2xl bg-gray-50/50">
                                    Escriba en el buscador para ver resultados
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- RECORDS LIST TABLE -->
                    <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Diligencias Disciplinarias</h3>
                                <p class="text-sm text-gray-500 mt-1">Historial general de procesos disciplinarios registrados.</p>
                            </div>
                            
                            <div class="flex w-full sm:w-auto gap-2">
                                <input 
                                    v-model="search"
                                    @keyup.enter="handleSearch"
                                    type="text"
                                    placeholder="N° Caso..."
                                    class="bg-gray-50 border-gray-200 rounded-xl focus:border-brand-primary focus:ring focus:ring-brand-primary/20 text-xs py-2 px-3"
                                />
                                <button 
                                    @click="handleSearch"
                                    class="bg-brand-dark text-white font-bold text-xs py-2 px-4 rounded-xl hover:bg-brand-primary transition"
                                >
                                    Filtrar
                                </button>
                            </div>
                        </div>

                        <!-- DATA TABLE -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase tracking-wider font-semibold">
                                        <th class="py-3 px-4">N° Caso</th>
                                        <th class="py-3 px-4">Empleado</th>
                                        <th class="py-3 px-4 text-center">Fecha</th>
                                        <th class="py-3 px-4 text-center">Estado</th>
                                        <th class="py-3 px-4 text-right">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr 
                                        v-for="record in records.data" 
                                        :key="record.id" 
                                        class="border-b border-gray-50 hover:bg-gray-50/50 transition duration-150 text-sm"
                                    >
                                        <td class="py-4 px-4 font-bold text-brand-dark">{{ record.record_number }}</td>
                                        <td class="py-4 px-4">
                                            <div class="font-semibold text-gray-800">{{ record.employee.first_name }} {{ record.employee.last_name }}</div>
                                            <div class="text-xs text-gray-400">{{ record.employee.cargo }}</div>
                                        </td>
                                        <td class="py-4 px-4 text-center text-gray-600 text-xs">
                                            {{ new Date(record.scheduled_date).toLocaleDateString('es-ES') }}
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <span 
                                                class="px-2.5 py-1 text-xs font-bold rounded-full border"
                                                :class="getStatusClasses(record.status)"
                                            >
                                                {{ record.status }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-right">
                                            <Link 
                                                :href="route('disciplinary.show', record.id)"
                                                class="inline-flex bg-brand-primary/10 text-brand-dark font-bold text-xs py-2 px-3 rounded-lg hover:bg-brand-primary hover:text-white transition duration-200"
                                            >
                                                Ver Detalle
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr v-if="records.data.length === 0">
                                        <td colspan="5" class="py-10 text-center text-gray-400 text-sm">
                                            No se han registrado procesos disciplinarios.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- PAGINATION -->
                        <div v-if="records.links.length > 3" class="flex justify-center mt-6">
                            <nav class="inline-flex -space-x-px rounded-md shadow-sm">
                                <Link 
                                    v-for="(link, index) in records.links" 
                                    :key="index"
                                    :href="link.url || '#'"
                                    class="relative inline-flex items-center px-3 py-2 text-xs font-bold border border-gray-200"
                                    :class="[
                                        link.active ? 'bg-brand-primary text-white border-brand-primary' : 'bg-white text-gray-600 hover:bg-gray-50',
                                        index === 0 ? 'rounded-l-xl' : '',
                                        index === records.links.length - 1 ? 'rounded-r-xl' : ''
                                    ]"
                                    v-html="link.label"
                                />
                            </nav>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
