<template>
  <Button to="/category" class="flex items-center gap-2 h-full"
    ><div class="lg:block hidden">Каталог</div>
    <Icon name="material-symbols:view-list-rounded" class="h-6 w-6"
  /></Button>

  <!-- <div class="relative h-full" ref="target" to="/catalog">
    <CatalogList v-if="showMenu" :target="target" v-model:showMenu="showMenu" />
  </div> -->
</template>

<script setup lang="ts">
import Button from '~/components/ui/Button/Button.vue'
// const showMenu = ref(false)
// const toggleMenu = () => (showMenu.value = !showMenu.value)
</script>
