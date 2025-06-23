<template>
  <textarea
    class="text-dark p-2 w-full rounded-lg bg-white active:border-primary-hover border-2 border-primary"
    v-bind="$attrs"
    v-model="model"
  ></textarea>
</template>

<script setup lang="ts">
const model = defineModel()
</script>
4
