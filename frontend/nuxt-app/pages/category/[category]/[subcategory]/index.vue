<script setup>
const route = useRoute();
</script>

<template>
  <div>
    <h1>Subcategory</h1>
    <p class="text-2xl font-bold p-4">{{ route.params.category }} - {{ route.params.subcategory }}</p>
  </div>
</template>
