<template>
  <div
    class="input__wrapper flex items-center justify-around border-2 border-transparent rounded-lg shadow-sm bg-white outline focus-within:outline focus-within:outline-primary-hover focus-within:outline-2 focus-visible:outline focus-visible:outline-primary-hover outline-8"
  >
    <slot name="left"></slot>
    <input
      class="text__element text-dark p-2 h-full w-full rounded-lg bg-white"
      v-model="model"
      type="text"
      v-bind="$attrs"
    />
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
.input__wrapper:focus-within {
  @apply outline-primary-hover outline-2 outline;
}

.text__element {
  border: none;
  outline: none;
}
</style>
