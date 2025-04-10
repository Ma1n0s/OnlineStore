<template>
  <div class="container mx-auto px-4">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
      <div class="md:flex">
        <div class="md:w-1/2 bg-gray-100 p-8 flex items-center justify-center">
          <div class="relative w-full h-96">
            <NuxtImg
              class="w-full h-full object-contain transition-transform duration-300 hover:scale-105"
              src=""
              alt="Комбинированный набор инструментов"
            />
            <div class="absolute top-4 left-4 bg-red-600 text-white text-sm font-bold px-3 py-1 rounded">
              ХИТ ПРОДАЖ
            </div>
          </div>
        </div>

        <div class="md:w-1/2 p-8">
          <div class="flex items-center mb-4">
            <span class="bg-primary text-white text-sm font-semibold px-3 py-1 rounded-full mr-3"> НОВИНКА </span>
            <span class="text-yellow-500 flex items-center">
              <svg
                v-for="i in 5"
                :key="i"
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                />
              </svg>
              <span class="ml-1 text-gray-600">(127 отзывов)</span>
            </span>
          </div>

          <h3 class="text-2xl font-bold text-gray-900 mb-3">Профессиональный набор инструментов BOSCH 125 предметов</h3>

          <div class="flex items-baseline mb-6">
            <span class="text-3xl font-bold text-gray-900">24 990 ₽</span>
            <span class="ml-3 text-lg text-gray-500 line-through">29 990 ₽</span>
            <span class="ml-3 bg-red-100 text-red-800 text-sm font-semibold px-2 py-0.5 rounded">-17%</span>
          </div>

          <div class="prose max-w-none text-gray-600 mb-6">
            <p class="mb-4">
              Идеальный комплект для профессионального мастера и домашнего использования. Включает все необходимое для
              большинства ремонтных работ.
            </p>

            <ul class="space-y-2 mb-4">
              <li class="flex items-start">
                <Icon name="" class="w-5 h-5" />
                <span class="text-lg">Гарантия 5 лет на все инструменты</span>
              </li>
              <li class="flex items-start">
                <Icon name="" class="w-5 h-5" />
                <span class="text-lg">Ударопрочный кейс для хранения</span>
              </li>
              <li class="flex items-start">
                <Icon name="" class="w-5 h-5" />
                <span class="text-lg">Эргономичные рукоятки с антискользящим покрытием</span>
              </li>
            </ul>
          </div>

          <div class="flex flex-col sm:flex-row gap-4">
            <button
              class="bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-8 rounded-lg flex items-center justify-center transition-colors text-lg"
            >
              <Icon name="" class="w-5 h-5" />
              Купить сейчас
            </button>
            <button
              class="border-2 border-gray-300 hover:bg-gray-100 text-gray-800 font-bold py-4 px-8 rounded-lg flex items-center justify-center transition-colors text-lg"
            >
              <Icon name="" class="w-5 h-5" />
              Быстрый просмотр
            </button>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-200 bg-gray-50">
        <div class="p-6 text-center">
          <div class="flex justify-center mb-3">
            <Icon name="" class="w-5 h-5" />
          </div>
          <h4 class="font-bold text-gray-900 mb-2 text-lg">Гарантия качества</h4>
          <p class="text-gray-600">Все инструменты проходят проверку перед отправкой</p>
        </div>
        <div class="p-6 text-center">
          <div class="flex justify-center mb-3">
            <Icon name="" class="w-5 h-5" />
          </div>
          <h4 class="font-bold text-gray-900 mb-2 text-lg">Оплата в один клик</h4>
          <p class="text-gray-600">Купить все в один клик!</p>
        </div>
        <div class="p-6 text-center">
          <div class="flex justify-center mb-3">
            <Icon name="" class="w-5 h-5" />
          </div>
          <h4 class="font-bold text-gray-900 mb-2 text-lg">Оплата при получении</h4>
          <p class="text-gray-600">Наличными или по qr-коду</p>
        </div>
        <div class="p-6 text-center">
          <div class="flex justify-center mb-3">
            <Icon name="" class="w-5 h-5" />
          </div>
          <h4 class="font-bold text-gray-900 mb-2 text-lg">Легкий возврат</h4>
          <p class="text-gray-600">14 дней на возврат без вопросов</p>
        </div>
      </div>
    </div>
  </div>
</template>
