<template>
  <div class="mb-4">
    <div v-if="label" class="block text-sm font-medium text-red mb-1">{{ label }}</div>
    <slot name="element"></slot>
    <slot name="additional"></slot>
  </div>
</template>

<!-- исправить стандартный slots, цвет текста -->
<!-- если передавать template без имени то он встает в slot по умолчанию -->

<script setup lang="ts">
defineProps<{
  label?: string;
}>();
</script>
