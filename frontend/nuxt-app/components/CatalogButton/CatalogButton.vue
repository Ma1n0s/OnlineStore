<template>
  <div class="relative" ref="target">
    <Button @click.prevent="toggleMenu" class="flex items-center gap-2 h-full"
      ><div class="lg:block hidden">Каталог</div>
      <Icon name="material-symbols:view-list-rounded" class="h-6 w-6"
    /></Button>
    <div
      v-if="showMenu"
      class="absolute mt-1 z-10 flex bg-white border border-primary-hover text-dark rounded-xl p-1 max-h-96 h-[96rem]"
    >
      <div class="h-full flex flex-col p-2 border-r border-primary">
        <div
          v-for="(item, index) in catalog"
          :key="index"
          :class="[
            '~p-4/2 cursor-pointer box-border border-b border-transparent hover:border-dark',
            { '!border-dark': item.name === selectedCategory?.name },
          ]"
          @mouseenter="toggleSelectedCategory(item)"
        >
          {{ item.name }}
        </div>
      </div>
      <div v-show="!!selectedCategory" class="grid grid-cols-3 p-2 w-[800px]">
        <div
          v-for="(elements, index) in selectedCategory?.elements"
          :key="`${index}-catalog`"
          class="~p-4/2 h-fit cursor-pointer hover:bg-dark/20"
        >
          {{ elements?.name || "Error" }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Button from "~/components/ui/Button/Button.vue";

type CatalogItem = { name: string; elements: any[] };

const catalog: CatalogItem[] = [
  {
    name: "Инструменты",
    elements: [
      { name: "Шуруповерты" },
      { name: "Дрели" },
      { name: "Болгарки" },
      { name: "Бороздоделы" },
      { name: "Гайковерты" },
      { name: "Гвоздезабиватели" },
      { name: "Дрели" },
      { name: "Граверы" },
      { name: "Заклепочники" },
      { name: "Измерительный инструмент" },
      { name: "Лабораторное оборудование" },
      { name: "Лобзики" },
      { name: "Ножницы по металлу" },
      { name: "Отбойные молотки" },
      { name: "Паяльное оборудование" },
      { name: "Перфораторы" },
      { name: "Пилы" },
      { name: "Пистолеты" },
      { name: "Пневмоинструмент" },
      { name: "Пневмошуруповерты" },
      { name: "Фены" },
      { name: "Фрезеры" },
      { name: "Сварочное оборудование" },
      { name: "Строительные пылесосы" },
      { name: "Электрические отвертки" },
      { name: "Шлифмашинки" },
      { name: "Электрорубанки" },
    ],
  },
  { name: "Другое", elements: [{ name: "1" }, { name: "2" }] },
];

const showMenu = ref(false);
const toggleMenu = () => (showMenu.value = !showMenu.value);

const selectedCategory = ref<CatalogItem>(catalog[0] || null);
const toggleSelectedCategory = (item: CatalogItem) => {
  selectedCategory.value = item;
};

const target = useTemplateRef<HTMLElement>("target");
onClickOutside(target, (event) => {
  console.log(showMenu.value);
  showMenu.value = false;
});
</script>
