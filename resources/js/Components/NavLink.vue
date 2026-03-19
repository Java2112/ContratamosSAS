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
        ? 'inline-flex items-center border-b-2 border-brand-primary px-1 pt-1 text-sm font-bold text-white transition duration-150 ease-in-out focus:border-brand-secondary focus:outline-none'
        : 'inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-300 transition duration-150 ease-in-out hover:border-gray-500 hover:text-white focus:border-gray-500 focus:text-white focus:outline-none',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
