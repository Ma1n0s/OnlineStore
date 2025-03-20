<template>
  <TextInput>
    <template v-slot:right name="right">
      <Icon name="material-symbols:search-rounded" class="mr-1 h-8 w-8" />
    </template>
  </TextInput>
</template>

<script setup lang="ts">
import TextInput from "~/shared/ui/Inputs/TextInput.vue";
</script>
