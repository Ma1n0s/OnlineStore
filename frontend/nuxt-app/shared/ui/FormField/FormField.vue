<template>
  <div class="field mb-4">
    <label v-if="label" class="field__label block text-sm font-medium text-gray-700 mb-1">{{ label }}</label>
    <slot></slot>
    <div v-if="error" class="field__error text-red-500 text-sm mt-1">{{ error }}</div>
    <div v-if="maxLength" class="field__counter text-gray-500 text-sm mt-1">{{ currentLength }} / {{ maxLength }}</div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from "vue";

const props = defineProps<{
  label?: string;
  disabled?: boolean;
  to?: string;
  error?: string;
  maxLength?: number;
}>();

const currentLength = ref(0);

// Если нужно отслеживать длину ввода, можно использовать watch или передавать значение из родительского компонента
watch(
  () => props.maxLength,
  (newValue) => {
    if (newValue) {
      currentLength.value = newValue;
    }
  }
);
</script>



