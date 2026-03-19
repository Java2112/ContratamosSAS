<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full border-l-4 border-brand-primary bg-brand-primary/10 py-2 pe-4 ps-3 text-start text-base font-bold text-brand-primary transition duration-150 ease-in-out focus:border-brand-primary focus:bg-brand-primary/20 focus:text-brand-primary focus:outline-none'
        : 'block w-full border-l-4 border-transparent py-2 pe-4 ps-3 text-start text-base font-medium text-gray-300 transition duration-150 ease-in-out hover:border-gray-500 hover:bg-gray-800 hover:text-white focus:border-gray-500 focus:bg-gray-800 focus:text-white focus:outline-none',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
