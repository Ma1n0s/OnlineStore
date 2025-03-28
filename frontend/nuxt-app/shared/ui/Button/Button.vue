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
import { RouterLink } from "vue-router";
import { computed } from "vue";

const { variant = "primary", ...props } = defineProps<{
  variant?: "primary" | "secondary" | "transparent" | "warning";
  disabled?: boolean;
  to?: string;
}>();

const variantClasses = {
  primary: "text-dark bg-primary hover:bg-primary-hover", // Голубой 1
  secondary: "text-second bg-second hover:bg-second-hover", // Голубой 2
  transparent:
    "text-dark bg-transparent border border-dark hover:bg-white", // Прозрачный
  warning: "text-white bg-danger hover:bg-danger-hover", // Красный
};

const buttonClasses = computed(() => [
  "px-6 py-2 rounded-xl hover:transition duration-300",
  variantClasses[variant],
  props.disabled ? "opacity-50 cursor-not-allowed" : "",
]);
</script>
