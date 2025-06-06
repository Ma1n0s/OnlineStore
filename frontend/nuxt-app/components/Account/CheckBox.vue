<template>
  <div class="flex items-center">
    <input
      type="checkbox"
      :id="id"
      :checked="modelValue"
      @change="$emit('update:modelValue', $event.target.checked)"
      class="rounded border-gray-300 text-primary focus:ring-primary"
    >
    <label :for="id" class="ml-2 block text-sm text-gray-700">
      {{ title }}
    </label>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: Boolean,
  title: String
})

const emit = defineEmits(['update:modelValue'])

const id = computed(() => 'checkbox-' + Math.random().toString(36).substring(2, 9))
</script>