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
  primary: "px-6 py-2 rounded-lg text-black bg-[#89CFF0] hover:bg-[#60BBCB] transition duration-300", // Голубой 1
  secondary: "px-6 py-2 rounded-lg text-black bg-[#60BBCB] hover:bg-[#31918C] transition duration-300", // Голубой 2
  transparent:
    "px-6 py-2 rounded-lg text-[#0D182A] bg-transparent border border-[#0D182A] hover:bg-[#E9F1F7] transition duration-300", // Прозрачный
  warning: "px-6 py-2 rounded-lg text-white bg-[#D62828] hover:bg-[#B22222] transition duration-300", // Красный
};

const buttonClasses = computed(() => [
  "btn",
  variantClasses[variant],
  props.disabled ? "opacity-50 cursor-not-allowed" : "",
]);
</script>

<!-- <Button variant="primary" class="custom-class">Button</Button>
<Button variant="secondary" class="custom-class">Button</Button>
<Button variant="transparent" class="custom-class">Button</Button>
<Button variant="warning" class="custom-class">Button</Button>
<Button variant="primary" :style="{ backgroundColor: 'purple', color: 'white' }">Button</Button>
<Button variant="primary" class="custom-class" :style="{ fontSize: '18px' }">Button</Button> -->
