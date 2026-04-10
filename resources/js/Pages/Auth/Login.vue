<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="mb-6 text-center">
            <h2 class="text-xl font-bold text-brand-dark dark:text-white">Acceso Administrativo</h2>
            <p class="text-sm text-gray-500 mt-1">Ingrese sus credenciales para continuar</p>
        </div>

        <div v-if="status" class="mb-6 rounded-xl bg-green-500/10 border border-green-500/20 px-4 py-3 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="email" value="Correo Electrónico" class="text-gray-700 dark:text-gray-300" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="correo@ejemplo.com"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Contraseña" class="text-gray-700 dark:text-gray-300" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center cursor-pointer group">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400 group-hover:text-brand-dark dark:group-hover:text-white transition-colors">Recordarme</span>
                </label>
            </div>

            <div class="pt-2 flex flex-col gap-4">
                <PrimaryButton
                    class="w-full"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Ingresar al Sistema
                </PrimaryButton>

                <div class="text-center">
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm font-semibold text-brand-dark hover:text-brand-primary dark:text-brand-secondary dark:hover:text-white underline underline-offset-4 decoration-current/30 hover:decoration-current transition-all"
                    >
                        ¿Olvidaste tu contraseña?
                    </Link>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>
