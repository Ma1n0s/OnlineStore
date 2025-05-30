<template>
  <NuxtLayout>
    <NuxtPage />
    <MobileBottomNav />
  </NuxtLayout>
</template>

<style>
body {
  font-family: 'Montserrat', sans-serif;
}
</style>

<script setup>
import MobileBottomNav from './components/MobileBottomNav/MobileBottomNav.vue'

// onMounted(() => {
//   const { isAuthenticated, user } = useSanctumAuth();
//   console.log(user.value, isAuthenticated.value, "user pinia");
// });
</script>
