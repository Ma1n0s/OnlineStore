<template>
  <div class="relative">
    <select
      @change="handleChange"
      v-model="selectedValue"
      class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline"
    >
      <option v-for="option in options" :key="option.value" :value="option.value">
        {{ option.label }}
      </option>
    </select>
    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
      <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
      </svg>
    </div>
    z
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps({
  options: {
    type: Array as () => Array<{ value: string; label: string }>,
    required: true,
  },
  modelValue: {
    type: String,
    default: '',
  },
})

const selectedValue = ref(props.modelValue)

const emit = defineEmits(['update:modelValue'])

function handleChange(event: Event) {
  const target = event.target as HTMLSelectElement
  selectedValue.value = target.value
  emit('update:modelValue', target.value)
}
</script>
