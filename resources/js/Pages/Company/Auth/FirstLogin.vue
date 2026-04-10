<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    document_number: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('company.first-login.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-[#021311] via-[#06302B] to-[#021311] p-4 text-gray-100">
        <Head title="Primer Ingreso Empresa — Configurar Contraseña" />

        <!-- Logo + título portal -->
        <div class="mb-8 flex flex-col items-center gap-3">
            <div class="w-16 h-16 rounded-2xl bg-brand-primary/20 border border-brand-primary/30 flex items-center justify-center shadow-lg shadow-brand-primary/20">
                <svg class="w-8 h-8 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
            </div>
            <div class="text-center">
                <p class="text-brand-primary text-xs font-semibold uppercase tracking-widest mb-1">Portal Empresas</p>
                <h1 class="text-3xl font-extrabold text-white tracking-tight">CONTRATAMOS</h1>
                <p class="text-gray-400 text-sm mt-1">Configuración de primer ingreso</p>
            </div>
        </div>

        <div class="w-full max-w-lg bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl shadow-black/60 px-8 py-10">
            <div class="mb-6 text-sm text-gray-300 leading-relaxed">
                Bienvenido al portal web. Por favor, confirme su correo electrónico y el NIT de su empresa para configurar su contraseña y acceder al sistema.
            </div>

            <div v-if="$page.props.status" class="mb-6 rounded-xl bg-brand-primary/10 border border-brand-primary/20 px-4 py-3 text-sm font-medium text-brand-primary">
                {{ $page.props.status }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <!-- Correo Electrónico -->
                <div>
                    <InputLabel for="email" value="Correo Electrónico" class="text-gray-300" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full rounded-xl bg-white/5 border-white/10 text-white focus:border-brand-primary focus:ring-brand-primary/30"
                        v-model="form.email"
                        required
                        autofocus
                        placeholder="ejemplo@empresa.com"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- NIT de la Empresa -->
                <div>
                    <InputLabel for="document_number" value="NIT / Documento de la Empresa" class="text-gray-300" />
                    <TextInput
                        id="document_number"
                        type="text"
                        class="mt-1 block w-full rounded-xl bg-white/5 border-white/10 text-white focus:border-brand-primary focus:ring-brand-primary/30"
                        v-model="form.document_number"
                        required
                        placeholder="Ingrese el NIT sin guiones"
                    />
                    <InputError class="mt-2" :message="form.errors.document_number" />
                </div>

                <!-- Contraseña -->
                <div>
                    <InputLabel for="password" value="Nueva Contraseña" class="text-gray-300" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full rounded-xl bg-white/5 border-white/10 text-white focus:border-brand-primary focus:ring-brand-primary/30"
                        v-model="form.password"
                        required
                        placeholder="Mínimo 8 caracteres"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <InputLabel for="password_confirmation" value="Confirmar Nueva Contraseña" class="text-gray-300" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full rounded-xl bg-white/5 border-white/10 text-white focus:border-brand-primary focus:ring-brand-primary/30"
                        v-model="form.password_confirmation"
                        required
                        placeholder="Repita su contraseña"
                    />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <div class="pt-2 flex flex-col gap-4">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-3 px-6 rounded-xl font-bold text-brand-dark text-sm
                               bg-gradient-to-r from-brand-primary to-brand-secondary
                               hover:brightness-110 active:scale-[0.98]
                               disabled:opacity-50 disabled:cursor-not-allowed
                               transition-all duration-200 shadow-xl shadow-brand-primary/20
                               focus:outline-none focus:ring-2 focus:ring-brand-primary/50"
                    >
                        <span v-if="!form.processing">Configurar Contraseña</span>
                        <span v-else>Procesando...</span>
                    </button>

                    <Link
                        :href="route('company.login')"
                        class="text-center text-sm font-semibold text-brand-primary hover:text-brand-secondary transition-colors"
                    >
                        &larr; Volver al inicio de sesión
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>
