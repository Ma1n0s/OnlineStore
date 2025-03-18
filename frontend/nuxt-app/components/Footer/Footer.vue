<script setup lang="js">
import { RouterLink } from 'vue-router';
const links = [
  // { link: 'catalog', name: 'Каталог' },
  // { link: 'stores', name: 'Адреса магазинов' },
  // { link: 'improve', name: 'Что улучшить?' },
  // { link: 'contacts', name: 'Контакты' },
  // { link: 'about', name: 'О компании' }
];
</script>
<!-- <template>
    <footer class="bg-gray-800 text-white py-8">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="footer-section">
            <h3 class="text-xl font-bold mb-4">О нас</h3>
            <p class="text-gray-400">
              Мы — компания, которая предлагает лучшие решения для ваших нужд. Наша цель — сделать ваш бизнес успешным.
            </p>
          </div>
  
          <div class="footer-section">
            <h3 class="text-xl font-bold mb-4">Быстрые ссылки</h3>
            <ul class="space-y-2">
              <li><NuxtLink to="/" class="text-gray-400 hover:text-white transition duration-300">Главная</NuxtLink></li>
              <li><NuxtLink to="/about" class="text-gray-400 hover:text-white transition duration-300">О нас</NuxtLink></li>
              <li><NuxtLink to="/services" class="text-gray-400 hover:text-white transition duration-300">Услуги</NuxtLink></li>
              <li><NuxtLink to="/contact" class="text-gray-400 hover:text-white transition duration-300">Контакты</NuxtLink></li>
            </ul>
          </div>

          <div class="footer-section">
            <h3 class="text-xl font-bold mb-4">Контакты</h3>
            <ul class="text-gray-400 space-y-2">
              <li>Адрес: ул. Пушкина, 42, Город</li>
              <li>Телефон: +7 (900) 555-33-55</li>
              <li>Email: </li>
            </ul>
          </div>
        </div>
  

        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
        
        </div>
      </div>
    </footer>
</template> -->
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

      <!-- Footer Content Section -->
      <div class="py-5">
        <div class="flex flex-wrap -mx-4">
          <div class="w-full md:w-1/3 px-4 mb-8">
            <div>
              <div class="mb-8">
                <RouterLink to="/">
                  <img src="https://i.ibb.co/QDy827D/ak-logo.png" class="max-w-full" alt="logo" />
                </RouterLink>
              </div>
              <div class="mb-8">
                <p class="text-gray-500 text-sm leading-7">
                  Мы предлагаем широкий ассортимент качеасственного оборудования. У нас вы найдете всё необходимое для
                  строительства, ремонта, садовых работ и многого другого. Мы гарантируем надежность, доступные цены.
                </p>
              </div>
              <!-- <div>
                <span class="text-white text-xl font-bold mb-5 block">Социальные сети</span>
                <div class="flex flex-row">
                  <RouterLink to="#" class="text-white text-lg mr-4">
                    <img src="/public/vk.svg" alt="telegram" class="w-10 h-10"/>
                  </RouterLink>
                  <RouterLink to="#" class="text-white text-lg mr-4">
                    <img src="/public/telegram.svg" alt="telegram" class="w-10 h-10"/>
                  </RouterLink>
                  <RouterLink to="#" class="text-white text-lg">
                    <img src="/public/youtube.svg" alt="telegram" class="w-10 h-10"/>
                  </RouterLink>
                </div>
              </div> -->
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
                  <RouterLink :to="item.link" class="text-gray-500 hover:text-orange-500">{{ item.name }}</RouterLink>
                </li>
              </ul>
            </div>
          </div>
          <!-- <div class="w-full md:w-1/3 px-4 mb-8">
            <div class="footer-widget">
              <div class="footer-widget-heading mb-8">
                <h3 class="text-white text-xl font-semibold relative">
                  Подпишись
                  <span class="absolute bottom-0 left-0 h-0.5 w-12 bg-orange-500"></span>
                </h3>
              </div>
              <div class="footer-text mb-8">
                <p class="text-gray-500 text-sm">
                  Не забудьте следить за нашими новостями
                </p>
              </div>
              <div class="subscribe-form relative">
                <form action="#">
                  <input type="text" placeholder="Ваша электроная почта" class="w-full bg-gray-800 text-white py-3 px-6 border border-gray-800">
                  <button class="absolute right-0 top-0 bg-orange-500 py-3 px-6 border border-orange-500">
                    <i class="fab fa-telegram-plane text-white text-xl"></i>
                  </button>
                </form>
              </div>
            </div>
          </div> -->
        </div>
      </div>
    </div>
  </footer>
</template>
