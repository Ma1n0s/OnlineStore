<template>
  <div class="pb-4">
    <h1 v-if="data.title" class="text-2xl font-bold">{{ data.title }}</h1>
    <div class="w-full flex flex-col py-2">
      <NuxtImg
        v-if="data.description_image_url"
        :src="`${data.description_image_url}` || 'no-photo.webp'"
        :alt="data.name"
        class="object-fill h-fit max-h-[300px] float-start pb-2 md:pb-4"
      />
      <div
        v-if="data.description"
        :class="['text-sm lg:text-lg overflow-hidden text-justify', hideText ? 'line-clamp-4' : 'line-clamp-none']"
        @click="hideText = !hideText"
      >
        {{ data.description }}
      </div>
      <button
        v-if="data && data?.description?.length > 400"
        @click="hideText = !hideText"
        class="w-full hover:bg-gray/10 py-1 flex items-center justify-center"
      >
        {{ hideText ? 'Показать больше' : 'Показать меньше' }}
        <Icon
          :name="hideText ? 'material-symbols:arrow-drop-down-rounded' : 'material-symbols:arrow-left-rounded'"
          class="w-8 h-8"
        />
      </button>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue'

// v-html="data.description"
defineProps<{
  data: {
    title: string
    description: string
    name: string
    description_image_url: string
  }
}>()

const hideText = ref(true)
</script>
