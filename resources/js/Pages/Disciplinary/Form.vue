<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    record: Object,
    clientName: String
});

// Setup dynamic questions list from DB record
const questionsList = ref(props.record.questions.map(q => ({
    id: q.id,
    question_text: q.question_text,
    sort_order: q.sort_order,
    is_from_template: q.is_from_template,
    answer: {
        answer_text: q.answer ? q.answer.answer_text : ''
    }
})));

const form = useForm({
    witness_name: props.record.witness_name || '',
    representative_name: props.record.representative_name,
    representative_role: props.record.representative_role,
    reason: props.record.reason,
    rules_violated: props.record.rules_violated || '',
    introductory_text: props.record.introductory_text || '',
    initial_observations: props.record.initial_observations || '',
    final_observations: props.record.final_observations || '',
    questions: []
});

const isFinalizing = ref(false);
const showFinalizeModal = ref(false);

// Dynamic actions
const addQuestion = () => {
    questionsList.value.push({
        id: null,
        question_text: '',
        sort_order: questionsList.value.length + 1,
        is_from_template: false,
        answer: {
            answer_text: ''
        }
    });
};

const removeQuestion = (index) => {
    questionsList.value.splice(index, 1);
    reorderQuestions();
};

const moveQuestionUp = (index) => {
    if (index === 0) return;
    const temp = questionsList.value[index];
    questionsList.value[index] = questionsList.value[index - 1];
    questionsList.value[index - 1] = temp;
    reorderQuestions();
};

const moveQuestionDown = (index) => {
    if (index === questionsList.value.length - 1) return;
    const temp = questionsList.value[index];
    questionsList.value[index] = questionsList.value[index + 1];
    questionsList.value[index + 1] = temp;
    reorderQuestions();
};

const reorderQuestions = () => {
    questionsList.value.forEach((q, idx) => {
        q.sort_order = idx + 1;
    });
};

// Actions
const saveProgress = () => {
    form.questions = questionsList.value;
    form.post(route('disciplinary.form.save', props.record.id), {
        preserveScroll: true
    });
};

const triggerFinalize = () => {
    // Save first, then finalize
    form.questions = questionsList.value;
    form.post(route('disciplinary.form.save', props.record.id), {
        preserveScroll: true,
        onSuccess: () => {
            showFinalizeModal.value = true;
        }
    });
};

const confirmFinalize = () => {
    isFinalizing.value = true;
    router.post(route('disciplinary.finalize', props.record.id), {}, {
        onFinish: () => {
            isFinalizing.value = false;
            showFinalizeModal.value = false;
        }
    });
};
</script>

<template>
    <Head title="Toma de Descargos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        Toma de Descargos Disciplinarios
                    </h2>
                    <span class="text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 rounded-full px-3 py-1 mt-1.5 inline-block">
                        Estado: {{ record.status }} (En captura de descargos)
                    </span>
                </div>
                <div class="flex gap-2">
                    <button 
                        @click="saveProgress"
                        :disabled="form.processing"
                        class="bg-white border border-gray-200 text-gray-700 font-bold text-xs py-2.5 px-4 rounded-xl hover:bg-gray-50 transition"
                    >
                        {{ form.processing ? 'Guardando...' : 'Guardar Progreso' }}
                    </button>
                    <Link 
                        :href="route('disciplinary.show', record.id)"
                        class="bg-gray-100 text-gray-600 font-bold text-xs py-2.5 px-4 rounded-xl hover:bg-gray-200 transition"
                    >
                        Salir de Sesión
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12 bg-gray-50/50 min-h-[calc(100vh-80px)]">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- LEFT COLUMN: CONTEXT & EDITABLE DILIGENCE PREFERENCES -->
                    <div class="lg:col-span-1 space-y-6">
                        
                        <!-- Worker Summary -->
                        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-3">
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider">Datos de la Citación</h3>
                            <div>
                                <div class="text-lg font-black text-gray-800">{{ record.employee.first_name }} {{ record.employee.last_name }}</div>
                                <div class="text-xs text-gray-500">C.C. {{ record.employee.document_number }} • {{ record.employee.cargo }}</div>
                                <div class="text-xs text-brand-primary font-bold mt-1">{{ clientName }}</div>
                            </div>
                            <div class="pt-3 border-t border-gray-100 text-xs text-gray-500 space-y-1">
                                <div><strong>N° Caso:</strong> {{ record.record_number }}</div>
                                <div><strong>Fecha programada:</strong> {{ new Date(record.scheduled_date).toLocaleDateString('es-ES') }}</div>
                                <div><strong>Hora programada:</strong> {{ record.scheduled_time }}</div>
                            </div>
                        </div>

                        <!-- Editable meta fields during live session -->
                        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-4">
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 pb-3">Detalle y Asistentes</h3>
                            
                            <div class="space-y-3 text-xs">
                                <div>
                                    <label class="font-bold text-gray-400">REPRESENTANTE EMPLEADOR</label>
                                    <input 
                                        v-model="form.representative_name"
                                        type="text"
                                        class="w-full bg-gray-50 border-gray-200 rounded-xl text-xs py-2 px-3 focus:border-brand-primary mt-1"
                                    />
                                </div>
                                <div>
                                    <label class="font-bold text-gray-400">CARGO REPRESENTANTE</label>
                                    <input 
                                        v-model="form.representative_role"
                                        type="text"
                                        class="w-full bg-gray-50 border-gray-200 rounded-xl text-xs py-2 px-3 focus:border-brand-primary mt-1"
                                    />
                                </div>
                                <div>
                                    <label class="font-bold text-gray-400">TESTIGO ASISTENTE</label>
                                    <input 
                                        v-model="form.witness_name"
                                        type="text"
                                        class="w-full bg-gray-50 border-gray-200 rounded-xl text-xs py-2 px-3 focus:border-brand-primary mt-1"
                                    />
                                </div>
                                <div>
                                    <label class="font-bold text-gray-400">FALTAS IMPUTADAS / HECHOS</label>
                                    <textarea 
                                        v-model="form.reason"
                                        rows="2"
                                        class="w-full bg-gray-50 border-gray-200 rounded-xl text-xs py-2 px-3 focus:border-brand-primary mt-1"
                                    ></textarea>
                                </div>
                                <div>
                                    <label class="font-bold text-gray-400">NORMAS VULNERADAS</label>
                                    <input 
                                        v-model="form.rules_violated"
                                        type="text"
                                        class="w-full bg-gray-50 border-gray-200 rounded-xl text-xs py-2 px-3 focus:border-brand-primary mt-1"
                                    />
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN: QUESTION & ANSWER CAPTURING ENGINE -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- Capture Live QA Card -->
                        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-6">
                            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Cuestionario en Ejecución</h3>
                                    <p class="text-xs text-gray-500 mt-1">Diligencie las respuestas otorgadas por el trabajador en tiempo real.</p>
                                </div>
                                <button 
                                    @click="addQuestion"
                                    class="bg-brand-primary/10 text-brand-dark hover:bg-brand-primary hover:text-white font-bold text-xs py-2 px-4 rounded-xl transition duration-200"
                                >
                                    + Agregar Pregunta
                                </button>
                            </div>

                            <!-- Live QA Items -->
                            <div class="space-y-6">
                                <div 
                                    v-for="(q, index) in questionsList" 
                                    :key="index"
                                    class="p-5 bg-gray-50/50 hover:bg-gray-50/80 rounded-2xl border border-gray-100 space-y-3 transition duration-150 relative"
                                >
                                    <!-- Header question -->
                                    <div class="flex justify-between items-center gap-3">
                                        <div class="flex items-center gap-2 flex-1">
                                            <span class="w-6 h-6 rounded-full bg-brand-dark text-white flex items-center justify-center text-xs font-black shrink-0">
                                                {{ index + 1 }}
                                            </span>
                                            <input 
                                                v-model="q.question_text"
                                                type="text"
                                                placeholder="Redacte la pregunta..."
                                                class="flex-1 bg-white border-gray-200 rounded-xl text-xs py-1.5 px-3 focus:border-brand-primary font-bold"
                                            />
                                        </div>
                                        
                                        <!-- Actions reorder/delete -->
                                        <div class="flex gap-1 shrink-0">
                                            <button 
                                                @click="moveQuestionUp(index)"
                                                :disabled="index === 0"
                                                class="p-1 rounded-lg hover:bg-gray-200 text-gray-400 disabled:opacity-30"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                                                </svg>
                                            </button>
                                            <button 
                                                @click="moveQuestionDown(index)"
                                                :disabled="index === questionsList.length - 1"
                                                class="p-1 rounded-lg hover:bg-gray-200 text-gray-400 disabled:opacity-30"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                            <button 
                                                @click="removeQuestion(index)"
                                                class="p-1 rounded-lg hover:bg-red-50 text-red-500"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Large answer text area -->
                                    <div>
                                        <textarea 
                                            v-model="q.answer.answer_text"
                                            rows="3"
                                            placeholder="Escriba la respuesta del trabajador aquí..."
                                            class="w-full bg-white border-gray-200 rounded-xl text-xs py-2.5 px-4 focus:border-brand-primary focus:ring-brand-primary/10 leading-relaxed"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Initial and Final observations -->
                            <div class="border-t border-gray-100 pt-6 space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase">Anotaciones u Observaciones Iniciales del Empleador:</label>
                                    <textarea 
                                        v-model="form.initial_observations"
                                        rows="2"
                                        placeholder="Ingrese observaciones sobre el comportamiento, puntualidad o documentos adicionales presentados al inicio..."
                                        class="w-full bg-gray-50 border-gray-200 rounded-xl text-xs py-2 px-3 focus:border-brand-primary mt-1 focus:ring-brand-primary/10"
                                    ></textarea>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase">Observaciones y Aclaraciones Finales (Cierre del Trabajador):</label>
                                    <textarea 
                                        v-model="form.final_observations"
                                        rows="3"
                                        placeholder="Ingrese declaraciones de descargo, justificaciones generales o cierre brindado por el empleado..."
                                        class="w-full bg-gray-50 border-gray-200 rounded-xl text-xs py-2 px-3 focus:border-brand-primary mt-1 focus:ring-brand-primary/10"
                                    ></textarea>
                                </div>
                            </div>

                            <!-- Actions save and finalize -->
                            <div class="flex justify-end gap-3 border-t border-gray-100 pt-6">
                                <button 
                                    @click="saveProgress"
                                    :disabled="form.processing"
                                    class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 font-bold text-xs py-3.5 px-6 rounded-xl transition duration-200 disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Guardando...' : 'Guardar Progreso' }}
                                </button>
                                <button 
                                    @click="triggerFinalize"
                                    :disabled="form.processing || questionsList.length === 0"
                                    class="bg-brand-dark hover:bg-brand-primary text-white font-bold text-xs py-3.5 px-6 rounded-xl transition duration-200"
                                >
                                    Finalizar y Cerrar Diligencia
                                </button>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>

        <!-- FINALIZE CONFIRMATION MODAL -->
        <div v-if="showFinalizeModal" class="fixed inset-0 z-[200] overflow-y-auto bg-brand-dark/40 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 border border-gray-100 shadow-2xl space-y-6 transform scale-100 transition duration-300">
                <div class="flex gap-4">
                    <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">¿Finalizar y Bloquear Acta?</h3>
                        <p class="text-sm text-gray-500 mt-2">
                            Al finalizar la diligencia, el sistema bloqueará definitivamente el cuestionario de preguntas y respuestas. No se podrán realizar modificaciones adicionales ad-hoc. Procederá a generarse el documento PDF.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-gray-100 pt-4">
                    <button 
                        @click="showFinalizeModal = false"
                        :disabled="isFinalizing"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs py-2.5 px-4 rounded-xl transition"
                    >
                        Cancelar
                    </button>
                    <button 
                        @click="confirmFinalize"
                        :disabled="isFinalizing"
                        class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs py-2.5 px-5 rounded-xl transition duration-200"
                    >
                        {{ isFinalizing ? 'Finalizando...' : 'Sí, Finalizar Diligencia' }}
                    </button>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
