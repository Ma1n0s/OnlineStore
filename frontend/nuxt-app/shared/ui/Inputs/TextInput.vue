<template>
  <div class="input__wrapper flex items-center justify-around rounded-lg shadow-sm bg-white">
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
  @apply outline-primary-hover outline-[3px] outline;
}

.text__element {
  border: none;
  outline: none;
}
</style>
