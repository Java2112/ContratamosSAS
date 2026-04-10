<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('company.login.store'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <!-- Layout propio para el portal empresa, sin depender del GuestLayout de empleados -->
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-[#021311] via-[#06302B] to-[#021311] p-4 text-gray-100">
        <Head title="Portal Empresas — Iniciar Sesión" />

        <!-- Logo + título portal -->
        <div class="mb-8 flex flex-col items-center gap-3">
            <!-- Ícono empresa -->
            <div class="w-16 h-16 rounded-2xl bg-brand-primary/20 border border-brand-primary/30 flex items-center justify-center shadow-lg shadow-brand-primary/20">
                <svg class="w-8 h-8 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
            </div>
            <div class="text-center">
                <p class="text-brand-primary text-xs font-semibold uppercase tracking-widest mb-1">Portal Empresas</p>
                <h1 class="text-3xl font-extrabold text-white tracking-tight">CONTRATAMOS</h1>
                <p class="text-gray-400 text-sm mt-1">Acceso exclusivo para empresas cliente</p>
            </div>
        </div>

        <!-- Card login -->
        <div class="w-full max-w-md bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl shadow-black/60 px-8 py-10">

            <div v-if="status" class="mb-6 rounded-xl bg-brand-primary/10 border border-brand-primary/20 px-4 py-3 text-sm font-medium text-brand-primary">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">

                <!-- Email -->
                <div>
                    <InputLabel for="email" value="Correo Electrónico"
                        class="text-gray-300 text-sm font-medium" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full rounded-xl bg-white/5 border-white/10 text-white placeholder-gray-500
                               focus:border-brand-primary focus:ring-brand-primary/30"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="correo@empresa.com"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- Contraseña -->
                <div>
                    <InputLabel for="password" value="Contraseña"
                        class="text-gray-300 text-sm font-medium" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full rounded-xl bg-white/5 border-white/10 text-white placeholder-gray-500
                               focus:border-brand-primary focus:ring-brand-primary/30"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <!-- Recordar + Submit -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            v-model="form.remember"
                            class="rounded border-white/20 bg-white/10 text-brand-primary focus:ring-brand-primary/30"
                        />
                        <span class="text-sm text-gray-400">Recordarme</span>
                    </label>
                </div>

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
                    <span v-if="!form.processing">Ingresar al Portal</span>
                    <span v-else class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        Ingresando...
                    </span>
                </button>
            </form>

            <!-- Separador + primer ingreso -->
            <div class="mt-7 pt-6 border-t border-white/10 text-center space-y-2">
                <p class="text-gray-400 text-sm">
                    ¿Primer ingreso?
                    <Link
                        :href="route('company.first-login')"
                        class="text-brand-primary hover:text-brand-secondary font-bold underline underline-offset-2 transition-colors"
                    >
                        Configura tu contraseña aquí
                    </Link>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <p class="mt-8 text-white/30 text-xs text-center">
            Portal exclusivo para empresas cliente &middot; Contratamos S.A.S
        </p>
    </div>
</template>
