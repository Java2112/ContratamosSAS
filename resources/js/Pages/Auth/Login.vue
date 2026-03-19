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

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Correo Electrónico" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Contraseña" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400"
                        >Recordar mis credenciales</span
                    >
                </label>
            </div>

            <div class="mt-4 flex flex-col items-center justify-center space-y-4">
                <div class="flex items-center justify-end w-full">
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="rounded-md text-sm font-semibold text-brand-dark underline hover:text-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-primary focus:ring-offset-2 dark:text-brand-secondary dark:hover:text-white"
                    >
                        ¿Olvidaste tu contraseña?
                    </Link>

                    <PrimaryButton
                        class="ms-4"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Ingresar al Sistema
                    </PrimaryButton>
                </div>
                
                <div class="mt-6 border-t border-gray-200 dark:border-gray-700 w-full pt-4 text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        ¿Eres empresa y es tu primer ingreso? 
                        <Link
                            :href="route('company.first-login')"
                            class="font-semibold text-brand-primary hover:text-brand-dark underline dark:text-brand-secondary dark:hover:text-white"
                        >
                            Configura tu contraseña aquí
                        </Link>
                    </p>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>
