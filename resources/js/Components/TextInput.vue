<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-xl border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary/20 dark:border-gray-700 dark:bg-gray-900/50 dark:text-gray-300 dark:placeholder-gray-500 dark:focus:border-brand-primary dark:focus:ring-brand-primary/20 transition-all duration-200"
        v-model="model"
        ref="input"
    />
</template>
