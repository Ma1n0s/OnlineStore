<template>
  <NuxtLink
    v-if="props.to"
    :to="props.to"
    :class="buttonClasses"
    :disabled="props.disabled"
    :alt="props.alt || `Кнопка ${props.to}`"
  >
    <slot></slot>
  </NuxtLink>
  <button v-else :disabled="props.disabled" :class="buttonClasses">
    <slot></slot>
  </button>
  <!-- <component :is="asComponents" :disabled="props.disabled" :to="props.disabled ? '#' : props.to" :class="buttonClasses">
    <slot></slot>
  </component> -->
</template>

<script setup lang="ts">
import { computed } from 'vue'
// import { NuxtLink } from '#components'
const { variant = 'primary', ...props } = defineProps<{
  variant?: 'primary' | 'secondary' | 'transparent' | 'warning'
  disabled?: boolean
  to?: string
  alt?: string
}>()

// const asComponents = computed(() => (props.to ? NuxtLink : 'button'))

const variantClasses = {
  primary: 'text-white bg-primary hover:bg-primary-hover', // Голубой 1
  secondary: 'text-white bg-second hover:bg-second-hover', // Голубой 2
  transparent: 'text-dark bg-transparent border-2 border-dark hover:bg-white', // Прозрачный
  warning: 'text-white bg-danger hover:bg-danger-hover', // Красный
}

const buttonClasses = computed(() => [
  'px-6 py-2 rounded-xl hover:transition duration-300 border-2 border-transparent',
  variantClasses[variant],
  props.disabled ? 'opacity-50 cursor-not-allowed' : '',
])
</script>
