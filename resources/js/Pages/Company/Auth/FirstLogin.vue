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
    <GuestLayout>
        <Head title="Primer Ingreso Empresa" />

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Bienvenido al portal web. Por favor, confime su correo electrónico y el NIT de su empresa para configurar su contraseña y acceder al sistema.
        </div>

        <div v-if="$page.props.status" class="mb-4 text-sm font-medium text-green-600">
            {{ $page.props.status }}
        </div>

        <form @submit.prevent="submit">
            <!-- Correo Electrónico -->
            <div>
                <InputLabel for="email" value="Correo Electrónico" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- NIT de la Empresa -->
            <div class="mt-4">
                <InputLabel for="document_number" value="NIT / Documento de la Empresa" />

                <TextInput
                    id="document_number"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.document_number"
                    required
                />

                <InputError class="mt-2" :message="form.errors.document_number" />
            </div>

            <!-- Contraseña -->
            <div class="mt-4">
                <InputLabel for="password" value="Nueva Contraseña" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Confirmar Contraseña -->
            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirmar Contraseña" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm font-semibold text-brand-dark underline hover:text-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-primary focus:ring-offset-2 dark:text-brand-secondary dark:hover:text-white"
                >
                    Volver a iniciar sesión
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Configurar Contraseña
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
