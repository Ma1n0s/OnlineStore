<template>
  <div
    class="input__wrapper h-full flex items-center justify-around border-none rounded-lg bg-white outline outline-2 outline-primary"
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
})

const model = defineModel()
</script>

<style>
.input__wrapper:focus-within {
  @apply outline-primary-hover outline-2 outline;
}

.text__element,
.text__element:focus-within {
  border: none;
  outline: none;
}
</style>
