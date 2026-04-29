<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: Array
});
</script>

<template>
    <div v-if="links.length > 3">
        <div class="flex flex-wrap -mb-1">
            <template v-for="(link, key) in links" :key="key">
                <div v-if="link.url === null" 
                     class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded-xl font-bold" 
                     v-html="link.label" 
                />
                <Link v-else 
                      class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded-xl hover:bg-white focus:border-emerald-500 focus:text-emerald-500 font-bold transition-all" 
                      :class="{ 'bg-emerald-600 text-white border-emerald-600 shadow-md': link.active, 'bg-white text-gray-700': !link.active }" 
                      :href="link.url" 
                      v-html="link.label" 
                />
            </template>
        </div>
    </div>
</template>
