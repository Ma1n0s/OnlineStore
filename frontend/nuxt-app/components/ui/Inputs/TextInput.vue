<template>
  <!-- :class="[$attrs.class]" -->
  <div
    :class="[setAtr && $attrs.class]"
    class="input__wrapper h-full flex items-center justify-around border-2 border-transparent rounded-lg bg-white outline outline-2 outline-primary box-content"
  >
    <slot name="left"></slot>
    <input
      class="text__element text-dark p-2 pl-3 h-full w-full rounded-lg bg-inherit"
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

const { setAtr = false } = defineProps<{
  setAtr?: boolean
}>()

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
