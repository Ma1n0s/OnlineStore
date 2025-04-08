<template>
  <div class="text-2xl font-bold mb-4">{{ title }}</div>
  <slot></slot>
</template>

<script setup lang="ts">
const { title } = defineProps<{
  title: string;
}>();
</script>
