<template>
  <div class="mb-4">
    <div v-if="label" class="block text-sm font-medium text-red mb-1">{{ label }}</div>
    <slot name="element"></slot>
    <slot name="additional"></slot>
    <slot></slot>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  label?: string
}>()
</script>

<!-- <FormField label="Пример метки">
   Слот по умолчанию 
  <template>
    <p>Этот текст будет вставлен в слот по умолчанию.</p>
  </template>

   Именованный слот "element" 
  <template #element>
    <button class="bg-blue-500 text-white p-2 rounded">Кнопка</button>
  </template>

  Именованный слот "additional"
  <template #additional>
    <p class="text-gray-600">Дополнительная информация.</p>
  </template>
</FormField> -->
