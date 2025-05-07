<script setup lang="ts">
import type { Advantage } from '~/types'

const {
  public: { backendUrl },
} = useRuntimeConfig()

const { data: advantages } = await useAsyncData('advantages', () => 
  $fetch<Advantage[]>(`${backendUrl}/api/advantages`)
)

const regularAdvantages = computed(() => 
  advantages.value?.filter(a => !a.is_special) || []
)

const specialAdvantage = computed(() => 
  advantages.value?.find(a => a.is_special)
)
</script>

<template>
  <div class="flex flex-col lg:flex-row gap-4">
    <NuxtLink
      v-for="advantage in regularAdvantages"
      :key="advantage.id"
      :to="advantage.link || '#'"
      class="h-48 min-w-52 flex flex-col justify-between items-start gap-2 bg-zinc-200 py-4 px-4 rounded-2xl hover:bg-zinc-300 transition-colors cursor-pointer shadow-xl hover:shadow-2xl"
    >
      <div class="flex flex-col items-start gap-2">
        <Icon v-if="advantage.icon" :name="advantage.icon" class="h-8 w-8" />
        <p class="text-base font-extrabold">{{ advantage.title }}</p>
        <p v-if="advantage.description" class="text-sm">{{ advantage.description }}</p>
      </div>
      <p class="text-sm sm:text-base hover:text-primary transition-colors">Подробнее</p>
    </NuxtLink>

    <NuxtLink
      v-if="specialAdvantage"
      :to="specialAdvantage.link || '#'"
      class="bg-gradient-to-bl from-slate-900 to-slate-200 w-full h-48 p-4 rounded-2xl transition-colors cursor-pointer shadow-xl hover:shadow-2xl flex flex-col gap-4"
    >
      <div class="text-white text-2xl font-bold">{{ specialAdvantage.title }}</div>
      <div v-if="specialAdvantage.tags" class="flex flex-row flex-wrap items-start gap-2">
        <div 
          v-for="(tag, index) in specialAdvantage.tags" 
          :key="index"
          class="bg-white rounded-2xl p-2 px-4 text-black font-bold text-sm"
        >
          {{ tag }}
        </div>
      </div>
    </NuxtLink>
  </div>
</template>