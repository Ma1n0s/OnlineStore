<template>
  <div class="flex flex-col rounded-3xl p-1 ~max-w-56/80 h-fit shadow-xl cursor-pointer">
    <div class="w-full h-fit">
      <NuxtImg :src="product.images[0].src" :alt="product.images[0].alt" format="webp" class="bg-cover" />
    </div>
    <div class="p-2">
      <div class="flex items-center">
        <Icon name="material-symbols:star-rate-rounded" class="h-6 w-6 text-yellow-500" />
        <Icon name="material-symbols:star-rate-rounded" class="h-6 w-6 text-yellow-500" />
        <Icon name="material-symbols:star-rate-rounded" class="h-6 w-6 text-yellow-500" />
        <Icon name="material-symbols:star-rate-rounded" class="h-6 w-6 text-yellow-500" />
        <Icon name="material-symbols:star-rate-rounded" class="h-6 w-6 text-yellow-500" />
        <div class="pl-1 text-sm">
          {{ product.raiting }}
        </div>
      </div>
      <div class="~text-xl/base hover:text-primary">{{ product.name }} {{ product.brand }}</div>
      <div class="flex justify-between">
        <div>
          <div class="flex">
            <div class="~text-base/sm text-gray line-through">{{ product.price.sum }} ₽</div>
            <div class="text-danger ~text-sm/[0.7rem] pl-1">{{ product.price.discount }}%</div>
          </div>
          <div class="~text-2xl/xl">{{ product.price.total }} ₽</div>
        </div>
        <Button><Icon name="material-symbols:add-shopping-cart-rounded" class="h-6 w-6" /></Button>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import type { Product } from "@/types/product.types";
import Button from "~/shared/ui/Button/Button.vue";
defineProps<{
  product: Product;
}>();
</script>
