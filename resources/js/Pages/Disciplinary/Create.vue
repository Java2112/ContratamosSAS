<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    employee: Object,
    defaultIntroductoryText: String,
    defaultQuestions: Array,
    representativeName: String,
    representativeRole: String
});

// Initialize dynamic questions list
const questionsList = ref(props.defaultQuestions.map((qText, index) => ({
    text: qText,
    sort_order: index + 1
})));

const form = useForm({
    employee_id: props.employee.id,
    witness_name: '',
    representative_name: props.representativeName,
    representative_role: props.representativeRole,
    scheduled_date: new Date().toISOString().split('T')[0],
    scheduled_time: '08:00',
    reason: '',
    rules_violated: '',
    introductory_text: props.defaultIntroductoryText,
    questions: []
});

// Helper functions for dynamic questions
const addQuestion = () => {
    questionsList.value.push({
        text: '',
        sort_order: questionsList.value.length + 1
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

// Form submit handler
const submitForm = () => {
    // Map questions to form array
    form.questions = questionsList.value.map(q => q.text).filter(t => t.trim() !== '');
    
    form.post(route('disciplinary.store'));
};
</script>

<template>
    <Head title="Apertura Diligencia" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    Apertura de Diligencia de Descargos
                </h2>
                <Link 
                    :href="route('disciplinary.dashboard')"
                    class="bg-gray-100 text-gray-600 font-bold text-xs py-2.5 px-4 rounded-xl hover:bg-gray-200 transition"
                >
                    Volver al Dashboard
                </Link>
            </div>
        </template>

        <div class="py-12 bg-gray-50/50 min-h-[calc(100vh-80px)]">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <form @submit.prevent="submitForm" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- EMPLOYEE DETAILS & META FORM -->
                    <div class="lg:col-span-1 space-y-6">
                        
                        <!-- Employee Info Card -->
                        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-4">
                            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3">Información del Trabajador</h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <div class="text-xs text-gray-400 font-semibold uppercase">Nombre Completo:</div>
                                    <div class="text-sm font-bold text-gray-800 mt-0.5">{{ employee.name }}</div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <div class="text-xs text-gray-400 font-semibold uppercase">Identificación:</div>
                                        <div class="text-sm font-bold text-gray-800 mt-0.5">{{ employee.document_number }}</div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-400 font-semibold uppercase">Cargo:</div>
                                        <div class="text-sm font-bold text-gray-800 mt-0.5">{{ employee.cargo }}</div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <div class="text-xs text-gray-400 font-semibold uppercase">Fecha Ingreso:</div>
                                        <div class="text-sm font-bold text-gray-800 mt-0.5">{{ employee.hired_at }}</div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-400 font-semibold uppercase">Empresa Cliente:</div>
                                        <div class="text-sm font-bold text-brand-primary mt-0.5">{{ employee.client_name }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule & Witnesses Card -->
                        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-4">
                            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3">Programación y Asistentes</h3>
                            
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-400 uppercase">Fecha:</label>
                                        <input 
                                            v-model="form.scheduled_date"
                                            type="date"
                                            class="w-full bg-gray-50 border-gray-200 rounded-xl text-xs py-2 px-3 focus:border-brand-primary mt-1"
                                            required
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-400 uppercase">Hora:</label>
                                        <input 
                                            v-model="form.scheduled_time"
                                            type="time"
                                            class="w-full bg-gray-50 border-gray-200 rounded-xl text-xs py-2 px-3 focus:border-brand-primary mt-1"
                                            required
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase">Representante de la Empresa:</label>
                                    <input 
                                        v-model="form.representative_name"
                                        type="text"
                                        class="w-full bg-gray-50 border-gray-200 rounded-xl text-xs py-2.5 px-3 focus:border-brand-primary mt-1 font-semibold"
                                        placeholder="Nombre de quien realiza el descargo"
                                        required
                                    />
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase">Cargo del Representante:</label>
                                    <input 
                                        v-model="form.representative_role"
                                        type="text"
                                        class="w-full bg-gray-50 border-gray-200 rounded-xl text-xs py-2.5 px-3 focus:border-brand-primary mt-1"
                                        placeholder="Cargo del representante legal/jefe"
                                        required
                                    />
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase">Testigo de la Diligencia (Opcional):</label>
                                    <input 
                                        v-model="form.witness_name"
                                        type="text"
                                        class="w-full bg-gray-50 border-gray-200 rounded-xl text-xs py-2.5 px-3 focus:border-brand-primary mt-1"
                                        placeholder="Nombre completo del testigo"
                                    />
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- MOTIVES, RULES & QUESTIONNAIRE MASTER BUILDER -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- Reason, Rules and Intro Box -->
                        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-4">
                            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3">Detalle Legal e Imputaciones</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase">Motivo / Hechos que originan el descargo:</label>
                                    <textarea 
                                        v-model="form.reason"
                                        rows="3"
                                        placeholder="Describa detalladamente los hechos o faltas que se imputan..."
                                        class="w-full bg-gray-50 border-gray-200 rounded-xl text-xs py-2.5 px-3 focus:border-brand-primary mt-1 focus:ring-brand-primary/10"
                                        required
                                    ></textarea>
                                    <span v-if="form.errors.reason" class="text-xs text-red-500 font-semibold mt-1 block">{{ form.errors.reason }}</span>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase">Normativa o Artículos Incumplidos (Opcional):</label>
                                    <input 
                                        v-model="form.rules_violated"
                                        type="text"
                                        placeholder="Ej: Artículo 15 del Reglamento Interno de Trabajo / Cláusula 3 del Contrato Laboral"
                                        class="w-full bg-gray-50 border-gray-200 rounded-xl text-xs py-2.5 px-3 focus:border-brand-primary mt-1 focus:ring-brand-primary/10"
                                    />
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase">Texto Formal Introductorio (Editable):</label>
                                    <textarea 
                                        v-model="form.introductory_text"
                                        rows="4"
                                        class="w-full bg-gray-50 border-gray-200 rounded-xl text-xs py-2.5 px-3 focus:border-brand-primary mt-1 font-mono leading-relaxed focus:ring-brand-primary/10"
                                        required
                                    ></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Questions Constructor -->
                        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-6">
                            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Cuestionario Dinámico de Descargos</h3>
                                    <p class="text-xs text-gray-500 mt-1">Configure las preguntas iniciales. Podrá reordenar, añadir nuevas y borrarlas.</p>
                                </div>
                                <button 
                                    type="button" 
                                    @click="addQuestion"
                                    class="bg-brand-primary/10 text-brand-dark hover:bg-brand-primary hover:text-white font-bold text-xs py-2 px-4 rounded-xl transition duration-200"
                                >
                                    + Agregar Pregunta
                                </button>
                            </div>

                            <!-- List of Questions -->
                            <div class="space-y-4">
                                <div 
                                    v-for="(q, index) in questionsList" 
                                    :key="index"
                                    class="flex items-start gap-3 p-4 bg-gray-50/50 rounded-2xl border border-gray-100 hover:bg-gray-50 transition duration-150"
                                >
                                    <!-- Order marker -->
                                    <span class="w-6 h-6 rounded-full bg-brand-dark/10 text-brand-dark flex items-center justify-center text-xs font-black shrink-0 mt-2">
                                        {{ index + 1 }}
                                    </span>
                                    
                                    <!-- Input block -->
                                    <div class="flex-1">
                                        <input 
                                            v-model="q.text"
                                            type="text"
                                            placeholder="Redacte la pregunta..."
                                            class="w-full bg-white border-gray-200 rounded-xl text-xs py-2 px-3 focus:border-brand-primary focus:ring-brand-primary/10 font-medium"
                                            required
                                        />
                                    </div>

                                    <!-- Action buttons -->
                                    <div class="flex gap-1 shrink-0 mt-1">
                                        <button 
                                            type="button"
                                            @click="moveQuestionUp(index)"
                                            :disabled="index === 0"
                                            class="p-1.5 rounded-lg hover:bg-gray-200 text-gray-400 disabled:opacity-30 disabled:hover:bg-transparent"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                                            </svg>
                                        </button>
                                        <button 
                                            type="button"
                                            @click="moveQuestionDown(index)"
                                            :disabled="index === questionsList.length - 1"
                                            class="p-1.5 rounded-lg hover:bg-gray-200 text-gray-400 disabled:opacity-30 disabled:hover:bg-transparent"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </button>
                                        <button 
                                            type="button"
                                            @click="removeQuestion(index)"
                                            class="p-1.5 rounded-lg hover:bg-red-50 text-red-500 hover:border-red-100"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div v-if="questionsList.length === 0" class="text-center py-6 text-gray-400 text-xs border border-dashed border-gray-200 rounded-2xl bg-gray-50/50">
                                    Debe configurar al menos una pregunta para poder crear la diligencia.
                                </div>
                            </div>

                            <!-- Submit action -->
                            <div class="flex justify-end gap-3 border-t border-gray-100 pt-6">
                                <button 
                                    type="submit"
                                    :disabled="form.processing || questionsList.length === 0"
                                    class="bg-brand-dark hover:bg-brand-primary text-white font-bold text-xs py-3.5 px-6 rounded-xl transition duration-200 disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Registrando...' : 'Aperturar Caso (Crear Borrador)' }}
                                </button>
                            </div>
                        </div>

                    </div>

                </form>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
