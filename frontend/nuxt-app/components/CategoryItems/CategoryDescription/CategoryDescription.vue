<template>
  <div class="py-4">
    <h1>{{ data.title }}</h1>
    <div>
      <NuxtImg :src="`${data.description_image_url}`" :alt="data.name" />
      <div v-html="data.description" class="flex gap-4 flex-col lg:flex-row justify-center items-center"></div>
    </div>
  </div>
</template>
<script setup lang="ts">
defineProps<{
  data: {
    title: string
    description: string
    name: string
    description_image_url: string
  }
}>()
</script>
