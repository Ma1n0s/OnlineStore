<template>
  <!-- <FormField label="123123">
    <template v-slot:additional>123</template> -->
  <div class="relative w-full" ref="target">
    <TextInput @input="search" @click="search">
      <template v-slot:right>
        <div class="cursor-pointer h-full flex justify-center">
          <Icon name="material-symbols:search-rounded" class="mr-1 h-8 w-8" />
        </div>
      </template>
    </TextInput>
    <div class="box-border absolute mt-1 w-full bg-white shadow-2xl rounded-xl p-1 z-10" v-show="showSearch">
      <div v-for="(item, index) in data.categories" :key="index" class="flex items-center py-1">
        <NuxtImg :src="item.src" width="40" height="40" />
        <div class="p-1">
          {{ item.name }}
        </div>
      </div>
      <hr class="border-primary/50 my-1" />
      <div v-for="(item, index) in data.items" :key="index" class="flex items-center py-1">
        <NuxtImg :src="item.src" width="60" height="60" />
        <div>
          <div class="p-1">
            {{ item.name }}
          </div>
          <div>{{ item.price }} руб.</div>
        </div>
      </div>
    </div>
  </div>

  <!--
  </FormField> -->
</template>

<script setup lang="ts">
// import FormField from "~/shared/ui/FormField/FormField.vue";
import TextInput from "~/shared/ui/Inputs/TextInput.vue";
import { onClickOutside } from "@vueuse/core";

const data = ref({
  categories: [
    {
      name: "Шуруповерты",
      src: "https://fi.makitamedia.com/images/3_Makita/301_machines/3011_a_GS1/30118_PNG_web/M8301D002_C2L0.png",
    },
    {
      name: "Шурупы",
      src: "https://princefastener.com/wp-content/uploads/2022/03/figure-1.-screws-300x300.png",
    },
  ],
  items: [
    {
      name: "Шуруповерт Makita",
      src: "https://fi.makitamedia.com/images/3_Makita/301_machines/3011_a_GS1/30118_PNG_web/M8301D002_C2L0.png",
      price: 10000,
    },
    {
      name: "Шуруповерт Ingersoll Rand L5110-K1",
      src: "https://hotech-ms.com/wp-content/uploads/2020/11/W7150EU-WEB-01-300x300.png",
      price: 7000,
    },
  ],
});
const showSearch = ref(false);

const search = () => {
  showSearch.value = true;
  console.log(showSearch.value);
};

const target = useTemplateRef<HTMLElement>("target");
onClickOutside(target, (event) => {
  console.log(showSearch.value);
  showSearch.value = false;
});
</script>
