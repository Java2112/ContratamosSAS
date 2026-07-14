<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="SISTEMA CONTRATAMOS" />

    <div class="bg-gray-50 text-gray-800 selection:bg-brand-primary selection:text-white dark:bg-gray-900 dark:text-gray-100 min-h-screen font-sans antialiased">
        <header class="bg-brand-dark text-white relative shadow-lg">
            <div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
                <div class="flex items-center">
                    <img src="/images/logos/logo-header-white.png" alt="Contratamos" class="h-14 w-auto" />
                </div>
                
                <nav v-if="canLogin" class="flex items-center space-x-6">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="$page.props.auth.user.role === 'empresa' ? route('company.dashboard') : route('dashboard')"
                        class="text-sm font-semibold hover:text-brand-primary transition duration-300"
                    >
                        Ir al Panel
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="text-sm font-medium border-b-2 border-transparent hover:border-brand-primary pb-1 transition duration-300"
                        >
                            Acceso Empleados
                        </Link>

                        <Link
                            :href="route('company.login')"
                            class="text-sm font-medium bg-brand-primary text-brand-dark px-5 py-2 rounded-full hover:bg-white transition duration-300 shadow-md"
                        >
                            Portal Cliente
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main class="grid lg:grid-cols-2 min-h-[calc(100vh-80px)]">
            <!-- Left Side / Text Content -->
            <div class="flex flex-col justify-center px-8 lg:px-24">
                <h1 class="text-5xl lg:text-7xl font-extrabold text-brand-dark dark:text-white leading-tight mb-6">
                    El talento que <span class="bg-gradient-to-r from-brand-primary to-brand-secondary bg-clip-text text-transparent">impulsa</span> tu empresa
                </h1>
                <p class="text-lg lg:text-xl text-gray-600 dark:text-gray-300 max-w-xl mb-10 leading-relaxed">
                    SISTEMA CONTRATAMOS S.A.S. optimiza la gestión de recursos humanos, reclutamiento y provisión de talento en misión para las mejores empresas del país.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <Link 
                        v-if="!$page.props.auth.user"
                        :href="route('login')" 
                        class="px-8 py-4 bg-brand-dark text-white text-center text-lg font-bold rounded-full hover:bg-gray-800 transition duration-300 shadow-xl"
                    >
                        Acceso Empleados
                    </Link>
                    <Link 
                        v-if="!$page.props.auth.user"
                        :href="route('company.login')" 
                        class="px-8 py-4 bg-brand-primary text-brand-dark text-center text-lg font-bold rounded-full hover:bg-white border border-brand-primary transition duration-300 shadow-xl"
                    >
                        Portal Cliente
                    </Link>
                </div>
            </div>

            <!-- Right Side / Decorative Graphic -->
            <div class="relative bg-brand-dark hidden lg:flex items-center justify-center overflow-hidden">
                <!-- Abstract blobs using new brand colors -->
                <div class="absolute w-96 h-96 bg-brand-primary rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
                <div class="absolute w-96 h-96 bg-brand-secondary rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000 mt-32 ml-32"></div>
                <div class="absolute w-96 h-96 bg-gray-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000 -ml-32 mt-16"></div>
                
                <div class="relative z-10 flex items-center justify-center p-12">
                     <img src="/images/logos/logo-full-bg.jpg" alt="Contratamos" class="w-full max-w-md rounded-3xl shadow-2xl border border-white/10 opacity-90" />
                </div>
            </div>
        </main>
    </div>
</template>

<style>
@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0px, 0px) scale(1); }
}
.animate-blob {
  animation: blob 7s infinite;
}
.animation-delay-2000 {
  animation-delay: 2s;
}
.animation-delay-4000 {
  animation-delay: 4s;
}
</style>
