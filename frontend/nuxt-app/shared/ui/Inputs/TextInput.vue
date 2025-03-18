<template>
  <div
    class="input__wrapper flex items-center justify-around rounded-lg shadow-sm bg-white focus-within:outline focus-within:outline-[3px] focus-within:outline-primary-hover"
  >
    <!-- Левый слот -->
    <slot name="left"></slot>

    <!-- Input -->
    <input
      class="text__element text-dark p-2 h-full w-full rounded-lg bg-white outline-none border-none focus:ring-0"
      v-model="model"
      type="text"
      v-bind="$attrs"
    />

    <!-- Правый слот -->
    <slot name="right"></slot>
  </div>
</template>

<script setup lang="ts">
defineOptions({
  inheritAttrs: false,
});

const model = defineModel();
</script>

<style>
/* Отключаем стандартную обводку */
/* .text__element {
  border: none;
  outline: none;
} */
</style>
