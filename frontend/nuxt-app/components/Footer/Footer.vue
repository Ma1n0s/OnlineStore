<script setup lang="ts">
import { RouterLink } from "vue-router";
const links = [
  // { link: 'catalog', name: 'Каталог' },
  // { link: 'stores', name: 'Адреса магазинов' },
  // { link: 'improve', name: 'Что улучшить?' },
  { link: "contacts", name: "Контакты" },
  { link: "about", name: "О компании" },
];
</script>
<template>
  <footer class="bg-dark text-white relative">
    <div class="container mx-auto px-4">
      <div class="py-5 border-b border-gray-700">
        <div class="flex flex-wrap -mx-4">
          <div class="w-full md:w-1/3 px-4 mb-8">
            <div class="flex items-center">
              <i class="text-orange-500 text-3xl mr-4"></i>
              <div>
                <h4 class="text-white text-xl font-semibold">Адрес</h4>
                <span class="text-gray-500">Ул.Пушкина д.42 </span>
              </div>
            </div>
          </div>
          <div class="w-full md:w-1/3 px-4 mb-8">
            <div class="flex items-center">
              <i class="text-orange-500 text-3xl mr-4"></i>
              <div>
                <h4 class="text-white text-xl font-semibold">Телефон</h4>
                <span class="text-gray-500">+7 (900) 800 555 33-55</span>
              </div>
            </div>
          </div>
          <div class="w-full md:w-1/3 px-4 mb-8">
            <div class="flex items-center">
              <i class="text-orange-500 text-3xl mr-4"></i>
              <div>
                <h4 class="text-white text-xl font-semibold">Почта</h4>
                <span class="text-gray-500">mail@gmail.com</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="py-5">
        <div class="flex flex-wrap -mx-4">
          <div class="w-full md:w-1/3 px-4 mb-8">
            <div>
              <div class="mb-8">
                <RouterLink to="#">
                  <img src="https://i.ibb.co/QDy827D/ak-logo.png" class="max-w-full" alt="logo" />
                </RouterLink>
              </div>
              <div class="mb-8">
                <p class="text-gray-500 text-sm leading-7">
                  Мы предлагаем широкий ассортимент качеасственного оборудования. У нас вы найдете всё необходимое для
                  строительства, ремонта, садовых работ и многого другого. Мы гарантируем надежность, доступные цены.
                </p>
              </div>
            </div>
          </div>
          <div class="w-full md:w-1/3 px-4 mb-8">
            <div class="footer-widget">
              <div class="footer-widget-heading mb-8">
                <h3 class="text-white text-xl font-semibold relative">
                  Полезные ссылки
                  <span class="absolute bottom-0 left-0 h-0.5 w-12 bg-orange-500"></span>
                </h3>
              </div>
              <ul class="flex flex-wrap">
                <li v-for="(item, index) in links" :key="index" class="w-1/2 mb-3">
                  <RouterLink :to="item.link || '#'" class="text-gray-500 hover:text-orange-500">{{
                    item.name
                  }}</RouterLink>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>
