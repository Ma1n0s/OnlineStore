<template>
  <NuxtLayout>
    <div class="min-h-screen flex flex-col">
      <NuxtPage />
      <MobileBottomNav />
    </div>
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
