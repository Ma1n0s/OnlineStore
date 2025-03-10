<template>
    <div class="input__wrapper flex items-center border border-gray-300 rounded-lg shadow-sm focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
        <slot name="left" class="pl-3"></slot>
        <input 
            class="text__element flex-grow px-4 py-2 outline-none rounded-lg" 
            v-model="model" 
            type="text" 
            v-bind="$attrs"
        >
        <slot name="right" class="pr-3"></slot>
    </div>
</template>

<script setup lang="ts">
    defineOptions({
        inheritAttrs: false,
    });

    const model = defineModel()
</script>