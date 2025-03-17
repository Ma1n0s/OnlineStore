<template>
  <div class="relative" @mouseover="() => (showMenu = true)" @mouseleave="() => (showMenu = false)">
    <Button @click.prevent="toggleMenu">Каталог</Button>
    <div v-show="showMenu" class="absolute flex bg-dark text-white">
      <div class="h-full w-full flex flex-col">
        <div v-for="(item, index) in catalog" :key="index" class="~p-4/2" @mouseenter="toggleSelectedCategory(item)">
          {{ item.name }}
        </div>
      </div>
      <div v-show="!!selectedCategory">
        <div v-for="elements in selectedCategory">{{ elements.name }}</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Button from "~/shared/ui/Button/Button.vue";
const showMenu = ref(false);
const toggleMenu = () => (showMenu.value = !showMenu.value);

const selectedCategory = ref<[] | null>(null);
const toggleSelectedCategory = (item: Record<any, any>) => {
  selectedCategory.value = item.elements;
};

const catalog = [
  { name: "Электроинструменты", elements: [{ name: "Шуруповерты" }, { name: "Дрели" }] },
  { name: "Другое", elements: [{ name: "1" }, { name: "2" }] },
];
</script>
