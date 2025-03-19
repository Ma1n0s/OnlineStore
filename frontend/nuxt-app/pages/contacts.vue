<script setup>
import GoogleMap from '~/components/Map/GoogleMap.vue';
</script>

<template>
  <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-6 text-center">Контакты</h1>

      <div class="flex flex-col md:flex-row items-center md:items-start space-y-8 md:space-y-0 md:space-x-8">
        <div class="flex-1 space-y-4">
          <p class="text-gray-700 leading-relaxed">Мы — команда энтузиастов, которая знает всё о качественных инструментах и их роли в вашем успехе. Наша компания уже более [X лет/лет] помогает профессионалам и любителям находить именно те инструменты, которые делают работу легче, быстрее и эффективнее.</p>
          <p class="text-gray-700 leading-relaxed">Наш ассортимент — это тщательно отобранные товары от проверенных мировых производителей. Мы предлагаем только то, что отвечает высоким стандартам качества и надежности. От ручного инструмента до профессионального оборудования — у нас есть всё, что нужно для ремонта, строительства, творчества и других задач.</p>
        </div>
        <div class="bg-red-700 rounded-lg p-4 transform scale-110 hover:scale-105 transition-transform duration-300 flex items-center justify-center shadow-lg">
          <span class="text-white text-2xl font-bold">LOGO</span>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <div class="flex items-center bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
          <svg class="w-8 h-8 mr-4 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          <div>
            <p class="font-semibold text-gray-900">Адрес</p>
            <p class="text-gray-600">Ул. Пушкина д.42</p>
          </div>
        </div>
        <div class="flex items-center bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
          <svg class="w-8 h-8 mr-4 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
          </svg>
          <div>
            <p class="font-semibold text-gray-900">Телефон</p>
            <p class="text-gray-600">+7 (900) 800 555 33-55</p>
          </div>
        </div>
        <div class="flex items-center bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
          <svg class="w-8 h-8 mr-4 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
          </svg>
          <div>
            <p class="font-semibold text-gray-900">Почта</p>
            <p class="text-gray-600">mail@gmail.com</p>
          </div>
        </div>
      </div>

      <div class="mt-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Отзывы наших клиентов</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="bg-gray-100 p-4 rounded-lg">
            <p class="italic">"Отличный сервис и качественные инструменты! Рекомендую!"</p>
            <p class="font-semibold mt-2">— Иван Иванов</p>
          </div>
          <div class="bg-gray-100 p-4 rounded-lg">
            <p class="italic">"Быстрая доставка и отличный выбор. Спасибо!"</p>
            <p class="font-semibold mt-2">— Мария Петрова</p>
          </div>
        </div>
      </div>

      <div class="mt-12">
        <GoogleMap />
      </div>
    </div>
  </div>
</template>