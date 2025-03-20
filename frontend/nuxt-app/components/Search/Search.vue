<template>
  <FormField label="123123">
    <template v-slot:additional>123</template>
    <TextInput>
      <template v-slot:right>
        <Icon name="material-symbols:search-rounded" class="mr-1 h-8 w-8" />
      </template>
    </TextInput>
  </FormField>
</template>

<script setup lang="ts">
import FormField from "~/shared/ui/FormField/FormField.vue";
import TextInput from "~/shared/ui/Inputs/TextInput.vue";
</script>
