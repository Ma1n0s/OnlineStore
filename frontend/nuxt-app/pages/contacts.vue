<script setup>
import TextInput from '~/components/ui/Inputs/TextInput.vue'
useHead({
  title: 'Контакты | Абсолют техно',
  meta: [
    {
      name: 'description',
      content:
        'Контактная информация компании Абсолют техно. Адрес, телефон, email и время работы. Свяжитесь с нами через VK, Telegram или WhatsApp для консультации и сотрудничества.',
    },
  ],
})
</script>

<template>
  <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <!-- Заголовок с декоративным элементом -->
      <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 relative inline-block">
          <span class="relative z-10">Наши контакты</span>
          <span class="absolute -bottom-2 left-0 w-full h-2 bg-primary z-0"></span>
        </h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
          Свяжитесь с нами удобным для вас способом. Мы всегда рады помочь и ответить на ваши вопросы.
        </p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">
        <!-- Контактная информация -->
        <div class="space-y-8">
          <!-- Блок с основной информацией -->
          <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
              <svg
                class="w-6 h-6 text-primary mr-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                ></path>
              </svg>
              Контактная информация
            </h2>

            <ul class="space-y-5">
              <li class="flex items-start">
                <div class="flex-shrink-0 bg-primary p-2 rounded-lg">
                  <svg
                    class="w-6 h-6 text-white"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                    ></path>
                  </svg>
                </div>
                <div class="ml-4">
                  <h3 class="text-sm font-medium text-gray-500">Email</h3>
                  <a
                    href="mailto:info@absolyut-tehno.ru"
                    class="text-lg font-semibold text-gray-900 hover:text-primary-active transition-colors"
                    >info@absolyut-tehno.ru</a
                  >
                </div>
              </li>

              <li class="flex items-start">
                <div class="flex-shrink-0 bg-primary p-2 rounded-lg">
                  <svg
                    class="w-6 h-6 text-white"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                    ></path>
                  </svg>
                </div>
                <div class="ml-4">
                  <h3 class="text-sm font-medium text-gray-500">Телефон</h3>
                  <a
                    href="tel:+78001234567"
                    class="text-lg font-semibold text-gray-900 hover:text-primary-active transition-colors"
                    >+7 (800) 123-45-67</a
                  >
                </div>
              </li>

              <li class="flex items-start">
                <div class="flex-shrink-0 bg-primary p-2 rounded-lg">
                  <svg
                    class="w-6 h-6 text-white"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                    ></path>
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                    ></path>
                  </svg>
                </div>
                <div class="ml-4">
                  <h3 class="text-sm font-medium text-gray-500">Адрес</h3>
                  <p class="text-lg font-semibold text-gray-900">г. Москва, ул. Техническая, 42, офис 305</p>
                </div>
              </li>

              <li class="flex items-start">
                <div class="flex-shrink-0 bg-primary p-2 rounded-lg">
                  <svg
                    class="w-6 h-6 text-white"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                    ></path>
                  </svg>
                </div>
                <div class="ml-4">
                  <h3 class="text-sm font-medium text-gray-500">Часы работы</h3>
                  <p class="text-lg font-semibold text-gray-900">Пн-Пт: 9:00 - 18:00</p>
                  <p class="text-sm text-gray-500 mt-1">Сб-Вс: выходной</p>
                </div>
              </li>
            </ul>
          </div>

          <!-- Социальные сети -->
          <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
              <svg
                class="w-6 h-6 text-white mr-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                ></path>
              </svg>
              Мы в соцсетях
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <a
                href="#"
                class="flex items-center justify-center px-4 py-3 bg-[#0077FF] text-white rounded-lg hover:bg-[#0066DD] transition-colors"
              >
                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M15.073 2H8.938C3.332 2 2 3.333 2 8.927v6.136C2 20.667 3.323 22 8.927 22h6.136C20.667 22 22 20.677 22 15.073V8.938C22 3.332 20.677 2 15.073 2zm3.073 14.27h-1.459c-.552 0-.718-.447-1.708-1.437-.864-.833-1.229-.937-1.448-.937-.302 0-.385.083-.385.5v1.312c0 .354-.115.563-1.042.563a5.692 5.692 0 0 1-4.448-2.667 11.626 11.626 0 0 1-2.302-4.833c0-.219.083-.417.5-.417h1.459c.375 0 .51.167.656.552.708 2.084 1.916 3.854 2.406 3.854.188 0 .27-.083.27-.552v-2.146c-.062-.979-.582-1.062-.582-1.416a.36.36 0 0 1 .374-.334h2.292c.313 0 .417.156.417.531v2.896c0 .313.135.417.229.417.188 0 .333-.104.677-.448 1.146-1.146 1.792-2.5 1.792-2.5.104-.209.271-.417.635-.417h1.459c.437 0 .53.219.437.531-.208.656-1.708 2.562-1.708 2.562-.167.26-.24.385 0 .698.145.225.656.646 1 1.041.708.76 1.271 1.396 1.271 1.875 0 .271-.167.417-.48.417z"
                  />
                </svg>
                ВКонтакте
              </a>

              <a
                href="#"
                class="flex items-center justify-center px-4 py-3 bg-[#0088CC] text-white rounded-lg hover:bg-[#0077BB] transition-colors"
              >
                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"
                  />
                </svg>
                Telegram
              </a>

              <a
                href="#"
                class="flex items-center justify-center px-4 py-3 bg-[#25D366] text-white rounded-lg hover:bg-[#1DA851] transition-colors"
              >
                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"
                  />
                </svg>
                WhatsApp
              </a>
            </div>
          </div>
        </div>

        <!-- Форма обратной связи -->
        <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 h-fit">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <svg
              class="w-6 h-6 text-primary mr-2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
              ></path>
            </svg>
            Форма обратной связи
          </h2>

          <form class="space-y-5">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Ваше имя</label>
              <TextInput type="text" id="name" class="w-full px-4 py-3" placeholder="Иван Иванов" />
            </div>

            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <TextInput type="email" id="email" class="w-full px-4 py-3" placeholder="example@mail.ru" />
            </div>

            <div>
              <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
              <TextInput type="tel" id="phone" class="w-full px-4 py-3" placeholder="+7 (___) ___-__-__" />
            </div>

            <div>
              <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Сообщение</label>
              <Textarea id="message" rows="4" class="w-full px-4 py-3" placeholder="Ваше сообщение..."></Textarea>
            </div>

            <button
              type="submit"
              class="w-full bg-primary hover:bg-primary-hover text-white font-medium py-3 px-4 rounded-lg transition-colors duration-300"
            >
              Отправить сообщение
            </button>
          </form>
        </div>
      </div>

      <!-- Карта на всю ширину внизу -->
      <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 w-full">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
          <svg
            class="w-6 h-6 text-primary mr-2"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
            ></path>
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
            ></path>
          </svg>
          Мы на карте
        </h2>
        <div class="aspect-w-16 aspect-h-9 rounded-xl overflow-hidden border border-gray-200 w-full">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2245.373789135826!2d37.61842341593095!3d55.75370498055257!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46b54a5a738fa419%3A0x7c347d506b52311f!2z0JrRgNCw0YHQvdCw0Y8g0J_Qu9C-0YnQsNC00Yw!5e0!3m2!1sru!2sru!4v1625070000000!5m2!1sru!2sru"
            width="100%"
            height="450"
            style="border: 0"
            allowfullscreen=""
            loading="lazy"
          ></iframe>
        </div>
      </div>
    </div>
  </div>
</template>
