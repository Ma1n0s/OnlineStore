<script setup></script>
<template>
  <div class="min-h-screen bg-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-4xl font-bold text-dark mb-8">Контакты</h1>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Контактная информация -->
        <div class="space-y-6">
          <div class="bg-dark p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold text-white mb-4">Контактная информация</h2>
            <ul class="space-y-4">
              <li class="flex items-center">
                <span class="text-white">Телефон:</span>
                <!-- <a href="tel:+1234567890" class="ml-2 text-white hover:text-primary-hover">+1 (234) 567-890</a> -->
              </li>
              <li class="flex items-center">
                <span class="text-white">Адрес:</span>
                <span class="ml-2 text-white">ул. Примерная, 123, Город, Страна</span>
              </li>
              <li class="flex items-center">
                <span class="text-white">Почта:</span>
                <!-- <a href="mailto:info@example.com" class="ml-2 text-white hover:text-primary-hover">info@example.com</a> -->
              </li>
              <li class="flex items-center">
                <span class="text-white">Время работы:</span>
                <span class="ml-2 text-white">Пн-Пт: 9:00 - 18:00</span>
              </li>
            </ul>
          </div>
          <div class="flex flex-col">
            <h4 class="items-start justify-start mb-2 font-bold text-2xl">Связитесь с нами</h4>
            <div class="flex bg-danger rounded-lg p-2 mb-2 w-64 items-center justify-center">
              <img src="" alt="vk" class="mr-2" />
              <div>Написать в VK</div>
            </div>
            <div class="flex bg-danger rounded-lg p-2 mb-2 w-64 items-center justify-center">
              <img src="" alt="Telegramm" class="mr-2" />
              <div>Написать в Telegramm</div>
            </div>
            <div class="flex bg-danger rounded-lg p-2 mb-2 w-64 items-center justify-center">
              <img src="" alt="Whatchap" class="mr-2" />
              <div>Написать в Whatchap</div>
            </div>
          </div>
        </div>

        <div class="bg-gray-200 p-6 rounded-lg shadow-md">
          <h2 class="text-2xl font-semibold text-dark mb-4">Мы на карте</h2>
          <div class="aspect-w-16 aspect-h-9">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.8354345093747!2d144.95373531531615!3d-37.816279742021665!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad642af0f11fd81%3A0xf577d6e1f7f8f8f8!2sExample%20Address!5e0!3m2!1sen!2sau!4v1625070000000!5m2!1sen!2sau"
              width="600"
              height="450"
              style="border: 0"
              allowfullscreen=""
              loading="lazy"
              class="rounded-lg"
            ></iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Дополнительные стили, если необходимо */
</style>
