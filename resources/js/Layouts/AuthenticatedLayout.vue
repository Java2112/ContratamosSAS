<script setup>
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <div>
        <div class="min-h-screen bg-[#F4F7F6] dark:bg-gray-900">
            <nav
                class="border-b border-brand-dark/10 bg-brand-dark dark:border-gray-700 dark:bg-gray-800"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center justify-center bg-white rounded-lg p-1 mr-4">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto text-brand-primary"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </NavLink>
                                
                                <template v-if="user.system_area === 'seleccion'">
                                    <NavLink :href="route('selection.dashboard')" :active="route().current('selection.dashboard')">
                                        Área Selección
                                    </NavLink>
                                </template>

                                <template v-if="user.role === 'admin' || user.system_area === 'gerencia'">
                                    <NavLink :href="route('admin.users.index')" :active="route().current('admin.users.*')">
                                        Empleados
                                    </NavLink>
                                </template>
                                <NavLink
                                    v-if="user.role === 'admin' || user.system_area === 'gerencia' || user.system_area === 'comercial' || !user.system_area"
                                    :href="route('commercial.clients.index')"
                                    :active="route().current('commercial.clients.*')"
                                >
                                    Comercial (Clientes)
                                </NavLink>
                                <NavLink
                                    v-if="user.role === 'admin' || user.system_area === 'gerencia' || user.system_area === 'comercial' || !user.system_area"
                                    :href="route('commercial.contracts.index')"
                                    :active="route().current('commercial.contracts.*')"
                                >
                                    Contratos Comerciales
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-brand-dark px-3 py-2 text-sm font-bold leading-4 text-white transition duration-150 ease-in-out hover:text-brand-primary focus:outline-none dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-300"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Mi Perfil
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Cerrar Sesión
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-white transition duration-150 ease-in-out hover:bg-brand-primary hover:text-brand-dark focus:bg-brand-primary focus:text-brand-dark focus:outline-none dark:text-gray-500 dark:hover:bg-gray-900 dark:hover:text-gray-400 dark:focus:bg-gray-900 dark:focus:text-gray-400"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden bg-brand-dark bg-opacity-95 backdrop-blur-md absolute w-full z-50 border-b border-brand-dark"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>

                        <template v-if="user.system_area === 'seleccion'">
                            <ResponsiveNavLink :href="route('selection.dashboard')" :active="route().current('selection.dashboard')">
                                Área Selección
                            </ResponsiveNavLink>
                        </template>

                        <template v-if="user.role === 'admin' || user.system_area === 'gerencia'">
                            <ResponsiveNavLink :href="route('admin.users.index')" :active="route().current('admin.users.*')">
                                Empleados
                            </ResponsiveNavLink>
                        </template>
                        <ResponsiveNavLink
                            v-if="user.role === 'admin' || user.system_area === 'gerencia' || user.system_area === 'comercial' || !user.system_area"
                            :href="route('commercial.clients.index')"
                            :active="route().current('commercial.clients.*')"
                        >
                            Clientes
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="user.role === 'admin' || user.system_area === 'gerencia' || user.system_area === 'comercial' || !user.system_area"
                            :href="route('commercial.contracts.index')"
                            :active="route().current('commercial.contracts.*')"
                        >
                            Contratos
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-gray-200 pb-1 pt-4 dark:border-gray-600"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-bold text-white dark:text-gray-200"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-brand-secondary">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')" class="text-white hover:text-brand-primary">
                                Mi Perfil
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="text-white hover:text-brand-primary"
                            >
                                Cerrar Sesión
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow dark:bg-gray-800"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
