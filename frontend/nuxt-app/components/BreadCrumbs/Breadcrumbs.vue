<script setup lang="ts">
import { NuxtLink } from '#components'

interface ICrumb {
  name: string
  url?: string
  color?: string
}

const props = defineProps<{ list: ICrumb[] }>()
</script>

<template>
  <nav class="flex items-center gap-2 text-gray-600 dark:text-gray-400 mb-4 overflow-x-auto py-2 scrollbar-hide">
    <div class="hidden md:flex items-center gap-2 text-sm">
      <NuxtLink to="/" class="hover:text-primary transition-colors whitespace-nowrap" exact-active-class="text-primary font-medium">
        Главная
      </NuxtLink>

      <template v-for="(crumb, index) in props.list" :key="index">
        <Icon name="material-symbols:chevron-right" class="h-4 w-4 text-gray-400 flex-shrink-0" />
        <component
          :is="crumb.url ? NuxtLink : 'div'"
          :to="crumb.url"
          :class="['transition-colors whitespace-nowrap', crumb.color ? `text-${crumb.color}` : 'hover:text-primary']"
          exact-active-class="text-primary font-medium"
        >
          {{ crumb.name }}
        </component>
      </template>
    </div>

    <div class="flex md:hidden items-center gap-2 text-sm">
      <NuxtLink to="/" class="hover:text-primary transition-colors" exact-active-class="text-primary font-medium">
        <Icon name="material-symbols:home" class="h-4 w-4" />
      </NuxtLink>

      <template v-if="props.list.length > 0">
        <Icon name="material-symbols:chevron-right" class="h-4 w-4 text-gray-400 flex-shrink-0" />
        <component
          :is="props.list[props.list.length - 1].url ? NuxtLink : 'div'"
          :to="props.list[props.list.length - 1].url"
          :class="['transition-colors whitespace-nowrap', props.list[props.list.length - 1].color ? `text-${props.list[props.list.length - 1].color}` : 'hover:text-primary']"
          exact-active-class="text-primary font-medium"
        >
          {{ props.list[props.list.length - 1].name }}
        </component>
      </template>
    </div>
  </nav>
</template>

<style>
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style>