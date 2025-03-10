<template>
    <component
        :is="props.to ? RouterLink : 'button'"
        :disabled="props.disabled"
        :to="props.disabled ? '#' : props.to"
        :class="buttonClasses"
    >
        <slot></slot>
    </component>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router';
import { computed } from 'vue';

const { variant = 'primary', ...props } = defineProps<{
    variant?: 'primary' | 'secondary' | 'transparent' | 'warning';
    disabled?: boolean;
    to?: string;
}>();

const variantClasses = {
    primary: 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded', // Голубой 1
    secondary: 'bg-blue-300 hover:bg-blue-400 text-white font-bold py-2 px-4 rounded', // Голубой 2
    transparent: 'bg-transparent hover:bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded border border-gray-500', // Прозрачный
    warning: 'bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded', // Красный
};

const buttonClasses = computed(() => [
    'btn', 
    variantClasses[variant], 
    props.disabled ? 'opacity-50 cursor-not-allowed' : '', 
]);
</script>

<!-- <Button variant="primary" class="custom-class">Button</Button>
<Button variant="secondary" class="custom-class">Button</Button>
<Button variant="transparent" class="custom-class">Button</Button>
<Button variant="warning" class="custom-class">Button</Button>
<Button variant="primary" :style="{ backgroundColor: 'purple', color: 'white' }">Button</Button>
<Button variant="primary" class="custom-class" :style="{ fontSize: '18px' }">Button</Button> -->