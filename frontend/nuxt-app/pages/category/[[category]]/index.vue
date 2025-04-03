<script setup>
const route = useRoute();
</script>

<template>
  <div>
    <h1>Category</h1>
    <p class="text-2xl font-bold p-4">{{ route.params.category }}</p>
  </div>
</template>
