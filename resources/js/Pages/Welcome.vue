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
                <div class="flex items-center space-x-3">
                    <!-- Logo Vector Placeholder -->
                    <svg viewBox="0 0 40 40" class="h-10 w-10 text-brand-primary" fill="currentColor">
                        <path d="M20 0C8.954 0 0 8.954 0 20s8.954 20 20 20 20-8.954 20-20S31.046 0 20 0zm0 35c-8.284 0-15-6.716-15-15S11.716 5 20 5s15 6.716 15 15-6.716 15-15 15zm4.828-21.828L18 19.344l-2.828-2.829-2.829 2.829L18 25l9.657-9.657-2.829-2.171z" />
                    </svg>
                    <span class="text-2xl font-bold tracking-tight">CONTRATAMOS</span>
                </div>
                
                <nav v-if="canLogin" class="flex items-center space-x-6">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
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
                            :href="route('login')"
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
                        :href="route('login')" 
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
                
                <div class="relative z-10 grid grid-cols-2 gap-6 p-12">
                     <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 shadow-2xl border border-white/20 transform -translate-y-8">
                         <div class="h-12 w-12 rounded-full bg-brand-primary mb-4 flex items-center justify-center text-brand-dark font-bold text-xl">1</div>
                         <h3 class="text-white font-bold text-lg mb-2">Selección Ágil</h3>
                         <p class="text-gray-300 text-sm">Encuentra los mejores candidatos en tiempo récord.</p>
                     </div>
                     <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 shadow-2xl border border-white/20 transform translate-y-8">
                         <div class="h-12 w-12 rounded-full bg-brand-secondary mb-4 flex items-center justify-center text-white font-bold text-xl">2</div>
                         <h3 class="text-white font-bold text-lg mb-2">Gestión de Nómina</h3>
                         <p class="text-gray-300 text-sm">Automatización exacta y sin retrasos.</p>
                     </div>
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
