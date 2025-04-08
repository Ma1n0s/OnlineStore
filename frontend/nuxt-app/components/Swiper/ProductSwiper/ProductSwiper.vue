<template>
  <ClientOnly>
    <swiper-container
      ref="containerRef"
      :navigation="true"
      :pagination="true"
      :loop="true"
      :autoplay-delay="5000"
      slides-per-view="auto"
      :space-between="30"
      class="w-full"
    >
      <swiper-slide v-for="(product, idx) in products" :key="idx" class="w-fit pb-8">
        <ProductSlide :product="product" />
      </swiper-slide>
    </swiper-container>
  </ClientOnly>
</template>

<script setup lang="ts">
import type { Product } from "@/types/product.types";
import ProductSlide from "./ProductSlide.vue";

const containerRef = ref(null);
const products = ref<Product[]>([
  {
    name: "Шуруповерт",
    brand: "Makita",
    article: 6234232,
    price: {
      total: "7 000",
      discount: "50",
      sum: "14 000",
    },
    raiting: 5,
    images: [
      {
        alt: "Шуруповерт makita вид сбоку",
        src: "https://fi.makitamedia.com/images/3_Makita/301_machines/3011_a_GS1/30118_PNG_web/M8301D002_C2L0.png",
      },
    ],
  },
  {
    name: "Шуруповерт",
    brand: "Makita",
    article: 6234232,
    price: {
      total: "7 000",
      discount: "50",
      sum: "14 000",
    },
    raiting: 5,
    images: [
      {
        alt: "Шуруповерт makita вид сбоку",
        src: "https://fi.makitamedia.com/images/3_Makita/301_machines/3011_a_GS1/30118_PNG_web/M8301D002_C2L0.png",
      },
    ],
  },
  {
    name: "Шуруповерт",
    brand: "Makita",
    article: 6234232,
    price: {
      total: "7 000",
      discount: "50",
      sum: "14 000",
    },
    raiting: 5,
    images: [
      {
        alt: "Шуруповерт makita вид сбоку",
        src: "https://fi.makitamedia.com/images/3_Makita/301_machines/3011_a_GS1/30118_PNG_web/M8301D002_C2L0.png",
      },
    ],
  },
  {
    name: "Шуруповерт",
    brand: "Makita",
    article: 6234232,
    price: {
      total: "7 000",
      discount: "50",
      sum: "14 000",
    },
    raiting: 5,
    images: [
      {
        alt: "Шуруповерт makita вид сбоку",
        src: "https://fi.makitamedia.com/images/3_Makita/301_machines/3011_a_GS1/30118_PNG_web/M8301D002_C2L0.png",
      },
    ],
  },
  {
    name: "Шуруповерт",
    brand: "Makita",
    article: 6234232,
    price: {
      total: "7 000",
      discount: "50",
      sum: "14 000",
    },
    raiting: 5,
    images: [
      {
        alt: "Шуруповерт makita вид сбоку",
        src: "https://fi.makitamedia.com/images/3_Makita/301_machines/3011_a_GS1/30118_PNG_web/M8301D002_C2L0.png",
      },
    ],
  },
  {
    name: "Шуруповерт",
    brand: "Makita",
    article: 6234232,
    price: {
      total: "7 000",
      discount: "50",
      sum: "14 000",
    },
    raiting: 5,
    images: [
      {
        alt: "Шуруповерт makita вид сбоку",
        src: "https://fi.makitamedia.com/images/3_Makita/301_machines/3011_a_GS1/30118_PNG_web/M8301D002_C2L0.png",
      },
    ],
  },
  {
    name: "Шуруповерт",
    brand: "Makita",
    article: 6234232,
    price: {
      total: "7 000",
      discount: "50",
      sum: "14 000",
    },
    raiting: 5,
    images: [
      {
        alt: "Шуруповерт makita вид сбоку",
        src: "https://fi.makitamedia.com/images/3_Makita/301_machines/3011_a_GS1/30118_PNG_web/M8301D002_C2L0.png",
      },
    ],
  },
]);
</script>

<style scoped>
@import "swiper/css";
@import "swiper/css/navigation";
@import "swiper/css/pagination";
@import "swiper/css/effect-creative";

swiper-container {
  --swiper-navigation-color: rgb(38, 56, 58);
  --swiper-pagination-color: black;
}
</style>
