<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    record: Object,
    clientName: String
});

const formClose = useForm({
    employee_signed: false,
    employer_signed: false,
    notes: ''
});

const isClosing = ref(false);

const closeProcess = () => {
    isClosing.value = true;
    formClose.post(route('disciplinary.close', props.record.id), {
        onFinish: () => {
            isClosing.value = false;
        }
    });
};

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
    <Head title="Detalle de Descargos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        Expediente Disciplinario: {{ record.record_number }}
                    </h2>
                    <span 
                        class="px-3 py-1 text-xs font-bold rounded-full border mt-1.5 inline-block"
                        :class="getStatusClasses(record.status)"
                    >
                        {{ record.status }}
                    </span>
                </div>
                
                <div class="flex gap-2">
                    <Link 
                        :href="route('disciplinary.dashboard')"
                        class="bg-white border border-gray-200 text-gray-700 font-bold text-xs py-2.5 px-4 rounded-xl hover:bg-gray-50 transition"
                    >
                        Volver al Dashboard
                    </Link>
                    
                    <Link 
                        v-if="['BORRADOR', 'EN_PROCESO'].includes(record.status)"
                        :href="route('disciplinary.form', record.id)"
                        class="bg-brand-dark hover:bg-brand-primary text-white font-bold text-xs py-2.5 px-4 rounded-xl transition duration-200"
                    >
                        Ingresar a Diligencia en Vivo
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12 bg-gray-50/50 min-h-[calc(100vh-80px)]">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- LEFT COLUMN: MAIN META DETAILS -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- Case Info Summary Card -->
                        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-6">
                            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3">Resumen de la Citación</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                                <div>
                                    <div class="text-xs text-gray-400 font-bold uppercase">Trabajador:</div>
                                    <div class="font-bold text-gray-800 mt-0.5">{{ record.employee.first_name }} {{ record.employee.last_name }}</div>
                                    <div class="text-xs text-gray-500">C.C. {{ record.employee.document_number }} • {{ record.employee.cargo }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-400 font-bold uppercase">Empresa Cliente:</div>
                                    <div class="font-bold text-brand-primary mt-0.5">{{ clientName }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-400 font-bold uppercase">Programado Para:</div>
                                    <div class="font-semibold text-gray-800 mt-0.5">
                                        {{ new Date(record.scheduled_date).toLocaleDateString('es-ES') }} a las {{ record.scheduled_time }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-400 font-bold uppercase">Representante de Citación:</div>
                                    <div class="font-semibold text-gray-800 mt-0.5">{{ record.representative_name }}</div>
                                    <div class="text-xs text-gray-500">{{ record.representative_role }}</div>
                                </div>
                                <div class="md:col-span-2 pt-4 border-t border-gray-50">
                                    <div class="text-xs text-gray-400 font-bold uppercase">Falta / Hecho Citado:</div>
                                    <div class="text-gray-700 text-xs bg-gray-50 rounded-2xl p-4 mt-2 leading-relaxed whitespace-pre-line border border-gray-100">
                                        {{ record.reason }}
                                    </div>
                                </div>
                                <div v-if="record.rules_violated" class="md:col-span-2">
                                    <div class="text-xs text-gray-400 font-bold uppercase">Artículos / Normas Incumplidos:</div>
                                    <div class="text-gray-800 font-medium text-xs mt-1">{{ record.rules_violated }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- LIVE QA CAPTURED LOG VIEW (Only if there are questions) -->
                        <div v-if="record.questions.length > 0" class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-6">
                            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3">Cuestionario Capturado</h3>
                            
                            <div class="space-y-6">
                                <div v-for="q in record.questions" :key="q.id" class="space-y-2">
                                    <div class="text-sm font-bold text-gray-800 flex gap-2">
                                        <span class="text-brand-primary">Pregunta {{ q.sort_order }}:</span>
                                        {{ q.question_text }}
                                    </div>
                                    <div class="p-4 bg-gray-50/50 border border-gray-100 rounded-2xl text-xs text-gray-600 leading-relaxed whitespace-pre-line">
                                        {{ q.answer ? q.answer.answer_text : 'SIN RESPUESTA CAPTURADA' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN: ACTIONS, PDF DOWNLOADS, SIGNATURES & TIMELINE -->
                    <div class="lg:col-span-1 space-y-6">
                        
                        <!-- PDF GENERATION & INDEPENDENT DOWNLOADS CARD -->
                        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-4">
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 pb-3">Documento Acta PDF</h3>
                            
                            <div class="space-y-3">
                                <template v-if="['FINALIZADO', 'PDF_GENERADO', 'CERRADO'].includes(record.status)">
                                    <a 
                                        :href="route('disciplinary.pdf', record.id)"
                                        class="w-full text-center bg-brand-primary hover:bg-brand-dark text-white font-bold text-xs py-3.5 px-4 rounded-xl transition duration-200 flex items-center justify-center gap-2"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                        </svg>
                                        Descargar Acta PDF
                                    </a>
                                    
                                    <p class="text-xs text-gray-500 text-center">Permite descargar el acta numerada directamente en el computador.</p>
                                </template>
                                <template v-else>
                                    <div class="text-center py-6 text-xs text-gray-400 bg-gray-50 border border-dashed border-gray-200 rounded-2xl">
                                        El PDF se habilitará una vez finalizada la diligencia disciplinaria.
                                    </div>
                                </template>
                                
                                <!-- File version history list -->
                                <div v-if="record.files.length > 0" class="pt-3 border-t border-gray-50">
                                    <div class="text-xs font-bold text-gray-400 uppercase mb-2">Historial de Generaciones:</div>
                                    <div class="space-y-2">
                                        <div 
                                            v-for="file in record.files" 
                                            :key="file.id"
                                            class="flex justify-between items-center text-xs p-2 bg-gray-50 rounded-xl"
                                        >
                                            <div class="font-semibold text-gray-700">Versión {{ file.version }}</div>
                                            <a 
                                                :href="route('disciplinary.pdf', record.id)" 
                                                class="text-brand-primary hover:text-brand-dark font-black"
                                            >
                                                Descargar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SIGN & CLOSE ACTION (Only if PDF generated or finalized) -->
                        <div v-if="['FINALIZADO', 'PDF_GENERADO'].includes(record.status)" class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-4">
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 pb-3">Firmas del Acta y Cierre</h3>
                            
                            <form @submit.prevent="closeProcess" class="space-y-4">
                                <p class="text-xs text-gray-500">Marque las firmas registradas físicas del acta antes de archivar definitivamente.</p>
                                
                                <div class="space-y-2 text-xs font-bold text-gray-700">
                                    <label class="flex items-center gap-2 p-3 bg-gray-50 rounded-xl hover:bg-gray-100/50 cursor-pointer">
                                        <input 
                                            v-model="formClose.employee_signed"
                                            type="checkbox"
                                            class="rounded text-brand-primary border-gray-300 focus:ring-brand-primary/20"
                                            required
                                        />
                                        Firma del Trabajador Registrada
                                    </label>

                                    <label class="flex items-center gap-2 p-3 bg-gray-50 rounded-xl hover:bg-gray-100/50 cursor-pointer">
                                        <input 
                                            v-model="formClose.employer_signed"
                                            type="checkbox"
                                            class="rounded text-brand-primary border-gray-300 focus:ring-brand-primary/20"
                                            required
                                        />
                                        Firma del Representante Empleador
                                    </label>
                                </div>

                                <div class="text-xs">
                                    <label class="font-bold text-gray-400 uppercase">Notas o Decisión de Cierre:</label>
                                    <textarea 
                                        v-model="formClose.notes"
                                        rows="2"
                                        placeholder="Ej: Se archiva sin sanción / Se suspende 3 días / Notificado..."
                                        class="w-full bg-gray-50 border-gray-200 rounded-xl text-xs py-2 px-3 focus:border-brand-primary mt-1 focus:ring-brand-primary/10"
                                    ></textarea>
                                </div>

                                <button 
                                    type="submit"
                                    :disabled="isClosing || !formClose.employee_signed || !formClose.employer_signed"
                                    class="w-full text-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-3.5 px-4 rounded-xl transition duration-200 disabled:opacity-50"
                                >
                                    {{ isClosing ? 'Cerrando...' : 'Cerrar y Archivar Proceso' }}
                                </button>
                            </form>
                        </div>

                        <!-- TIMELINE AUDIT TRAIL -->
                        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-4">
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 pb-3">Trazabilidad de Auditoría</h3>
                            
                            <div class="space-y-4 max-h-[300px] overflow-y-auto pr-1">
                                <div 
                                    v-for="state in record.states" 
                                    :key="state.id" 
                                    class="flex gap-3 text-xs"
                                >
                                    <div class="flex flex-col items-center">
                                        <span class="w-3 h-3 rounded-full bg-brand-primary shrink-0"></span>
                                        <span class="w-0.5 h-full bg-gray-100 my-1"></span>
                                    </div>
                                    <div class="flex-1 pb-4">
                                        <div class="font-bold text-gray-800 uppercase">{{ state.state }}</div>
                                        <div class="text-gray-500 text-[10px] mt-0.5">
                                            {{ new Date(state.created_at).toLocaleString('es-ES') }} • Por {{ state.user.name }}
                                        </div>
                                        <div v-if="state.notes" class="text-gray-400 text-[10px] mt-1 bg-gray-50 p-2 rounded-lg leading-relaxed">
                                            {{ state.notes }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
