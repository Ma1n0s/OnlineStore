<script setup lang="ts">
import { ref, onMounted, reactive, computed } from 'vue'
import Breadcrumbs from '~/components/BreadCrumbs/Breadcrumbs.vue'

const breadcrumbs = computed(() => [{ url: '/category', name: 'Каталог', color: '#6b7280' }])
</script>
<template>
  <div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
    <Breadcrumbs :list="breadcrumbs" />
    <div class="mb-8 text-center">
      <h2 class="text-3xl font-bold hidden sm:block">Как воспользоваться услугой аренды</h2>
      <h5 class="text-xl font-bold sm:hidden">Как воспользоваться услугой аренды</h5>
    </div>

    <div class="space-y-12 mb-12">
      <div class="bg-white p-6 rounded-lg shadow-sm">
        <h3 class="text-2xl font-semibold hidden sm:block mb-4">Взять инструмент в аренду</h3>
        <h6 class="text-lg font-semibold sm:hidden mb-4">Взять инструмент в аренду</h6>

        <ul class="list-disc pl-5 space-y-2 mb-6">
          <li>Выберите инструмент – на сайте или по телефону</li>
          <li>Оформите заявку на аренду – через сайт или при разговоре с менеджером</li>
          <li>Согласуйте условия – срок и стоимость аренды, размер залога для физических лиц</li>
          <li><span class="font-semibold">Выберите способ получения:</span></li>
        </ul>

        <div class="grid md:grid-cols-2 gap-6">
          <div class="bg-gray-50 p-4 rounded-lg">
            <h6 class="font-semibold mb-2">Доставка</h6>
            <p class="mb-2">В день доставки дождитесь звонка курьера для согласования времени получения заказа</p>
            <p class="mb-2">Получите инструмент на объекте – курьер проведет его демонстрацию</p>
            <p>Подпишите договор и оплатите услугу</p>
          </div>

          <div class="bg-gray-50 p-4 rounded-lg">
            <h6 class="font-semibold mb-2">Самовывоз</h6>
            <p class="mb-2">После заявки за вами резервируется выбранный инструмент на <b>3 дня</b></p>
            <p class="mb-2">В этот срок приезжайте в пункт аренды за заказом</p>
            <p>Подпишите договор, посмотрите демонстрацию товара, оплатите услугу и получите заказ</p>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-sm">
        <h3 class="text-2xl font-semibold hidden sm:block mb-4">Продлить аренду при необходимости увеличения срока</h3>
        <h6 class="text-lg font-semibold sm:hidden mb-4">Продлить аренду при необходимости увеличения срока</h6>

        <ul class="list-disc pl-5 space-y-2">
          <li>Если вы хотите продлить срок, позвоните менеджеру ВиРент и согласуйте дополнительные дни аренды</li>
          <li>
            Оплатите дополнительный срок аренды: юридические лица по счету, физические лица наличными либо по ссылке на
            оплату
          </li>
        </ul>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-sm">
        <h3 class="text-2xl font-semibold hidden sm:block mb-4">Вернуть инструмент после использования</h3>
        <h6 class="text-lg font-semibold sm:hidden mb-4">Вернуть инструмент после использования</h6>

        <ul class="list-disc pl-5 space-y-2 mb-6">
          <li>
            За день до окончания аренды менеджер ВиРент позвонит вам и напомнит о возврате или предложит продление
            аренды
          </li>
          <li>
            Для передачи инструмента в пункт аренды вы можете выбрать доставку или самовывоз – это не зависит от того,
            каким способом вы получали заказ
          </li>
          <li>При сдаче сотрудник ВиРент проверяет инструмент и возвращает залог. Вы подписываете документы</li>
        </ul>

        <img src="" alt="Процесс аренды" class="mx-auto rounded-lg" />
      </div>
    </div>

    <div class="mb-12">
      <h2 class="text-3xl font-bold text-center mb-8 hidden sm:block">Что требуется для оформления услуги аренды</h2>
      <h5 class="text-xl font-bold text-center mb-8 sm:hidden">Что требуется для оформления услуги аренды</h5>

      <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm">
          <h3 class="text-xl font-semibold mb-4 hidden sm:block">Для физических лиц</h3>
          <h6 class="text-lg font-semibold mb-4 sm:hidden">Для физических лиц</h6>
          <ul class="list-disc pl-5 space-y-2">
            <li>Паспорт</li>
            <li>Сумма залога наличными</li>
            <li>Личное присутствие при получении и сдаче инструмента</li>
          </ul>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
          <h3 class="text-xl font-semibold mb-4 hidden sm:block">Для юридических лиц</h3>
          <h6 class="text-lg font-semibold mb-4 sm:hidden">Для юридических лиц</h6>
          <ul class="list-disc pl-5 space-y-2">
            <li>Реквизиты для оформления договора и счета</li>
            <li>Оплата счета</li>
            <li>
              Печать, доверенность и документ, подтверждающий личность сотрудника, указанного в доверенности для
              получения
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="mb-12">
      <h5 class="text-xl font-semibold mb-4 hidden sm:block">Документы для скачивания</h5>
      <h6 class="text-lg font-semibold mb-4 sm:hidden">Документы для скачивания</h6>

      <div class="flex flex-wrap gap-4">
        <a
          href=""
          class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-hover"
          download="tipovoj_dogovor_arendy"
          target="_blank"
        >
          Типовой договор аренды
        </a>
        <a
          href=""
          class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-hover"
          download="rekvizity_virent"
          target="_blank"
        >
          Реквизиты ВиРент
        </a>
      </div>
    </div>

    <div class="mb-12 bg-gray-50 p-6 rounded-lg">
      <h3 class="text-2xl font-semibold mb-4 hidden sm:block">
        Гарантия ВиРент – ваша защита в непредвиденных ситуациях
      </h3>
      <b class="text-lg font-semibold sm:hidden block mb-4">Гарантия ВиРент – ваша защита в непредвиденных ситуациях</b>

      <div class="prose max-w-none mb-6">
        <p>Наше оборудование проходит проверку и обслуживание перед отправкой к клиенту.</p>
        <p>Поломки и неисправности – явление редкое. Но у нас есть решение и на такие случаи – гарантия.</p>
        <p>
          Шильдик «На гарантии» в карточке товара – знак того, что мы ответственны за качество оборудования и готовы
          ремонтировать или заменять его бесплатно в случае неисправности.
        </p>
      </div>

      <h4 class="text-xl font-semibold text-center mb-6">Гарантия ВиРент – это удобно</h4>

      <div class="space-y-4">
        <div class="flex flex-col md:flex-row gap-4">
          <div class="flex-1 bg-white p-4 rounded border-l-4 border-red-500">
            <div class="flex items-start">
              <svg
                class="w-5 h-5 mt-0.5 mr-2 text-red-500 flex-shrink-0"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M11.575 13.3999L6.675 18.2999C6.49167 18.4833 6.25834 18.5749 5.975 18.5749C5.69167 18.5749 5.45834 18.4833 5.275 18.2999C5.09167 18.1166 5 17.8833 5 17.5999C5 17.3166 5.09167 17.0833 5.275 16.8999L10.175 11.9999L5.275 7.0999C5.09167 6.91656 5 6.68323 5 6.3999C5 6.11656 5.09167 5.88323 5.275 5.6999C5.45834 5.51656 5.69167 5.4249 5.975 5.4249C6.25834 5.4249 6.49167 5.51656 6.675 5.6999L11.575 10.5999L16.475 5.6999C16.6583 5.51656 16.8917 5.4249 17.175 5.4249C17.4583 5.4249 17.6917 5.51656 17.875 5.6999C18.0583 5.88323 18.15 6.11656 18.15 6.3999C18.15 6.68323 18.0583 6.91656 17.875 7.0999L12.975 11.9999L17.875 16.8999C18.0583 17.0833 18.15 17.3166 18.15 17.5999C18.15 17.8833 18.0583 18.1166 17.875 18.2999C17.6917 18.4833 17.4583 18.5749 17.175 18.5749C16.8917 18.5749 16.6583 18.4833 16.475 18.2999L11.575 13.3999Z"
                  fill="currentColor"
                ></path>
              </svg>
              <span>Ремонтировать самостоятельно</span>
            </div>
          </div>
          <div class="flex-1 bg-white p-4 rounded border-l-4 border-green-500">
            <div class="flex items-start">
              <svg
                class="w-4 h-4 mt-0.5 mr-2 text-green-500 flex-shrink-0"
                viewBox="0 0 14 10"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M4.71975 9.65617C4.60863 9.65617 4.50446 9.63867 4.40725 9.60367C4.31002 9.56917 4.21975 9.51034 4.13641 9.427L0.553081 5.84367C0.400298 5.69084 0.327248 5.49284 0.333914 5.2495C0.341131 5.00667 0.421131 4.80892 0.573906 4.65617C0.726689 4.50334 0.921131 4.427 1.15725 4.427C1.39336 4.427 1.5878 4.50334 1.74057 4.65617L4.71975 7.63534L11.7823 0.572794C11.935 0.420019 12.1331 0.343628 12.3764 0.343628C12.6192 0.343628 12.8169 0.420019 12.9698 0.572794C13.1225 0.725578 13.1989 0.923353 13.1989 1.16613C13.1989 1.40946 13.1225 1.60752 12.9698 1.76029L5.3031 9.427C5.21976 9.51034 5.12943 9.56917 5.03225 9.60367C4.93502 9.63867 4.83086 9.65617 4.71975 9.65617Z"
                  fill="currentColor"
                ></path>
              </svg>
              <span>Мы приедем на объект и заберем оборудование или отремонтируем на месте</span>
            </div>
          </div>
        </div>

        <div class="flex flex-col md:flex-row gap-4">
          <div class="flex-1 bg-white p-4 rounded border-l-4 border-red-500">
            <div class="flex items-start">
              <svg
                class="w-5 h-5 mt-0.5 mr-2 text-red-500 flex-shrink-0"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M11.575 13.3999L6.675 18.2999C6.49167 18.4833 6.25834 18.5749 5.975 18.5749C5.69167 18.5749 5.45834 18.4833 5.275 18.2999C5.09167 18.1166 5 17.8833 5 17.5999C5 17.3166 5.09167 17.0833 5.275 16.8999L10.175 11.9999L5.275 7.0999C5.09167 6.91656 5 6.68323 5 6.3999C5 6.11656 5.09167 5.88323 5.275 5.6999C5.45834 5.51656 5.69167 5.4249 5.975 5.4249C6.25834 5.4249 6.49167 5.51656 6.675 5.6999L11.575 10.5999L16.475 5.6999C16.6583 5.51656 16.8917 5.4249 17.175 5.4249C17.4583 5.4249 17.6917 5.51656 17.875 5.6999C18.0583 5.88323 18.15 6.11656 18.15 6.3999C18.15 6.68323 18.0583 6.91656 17.875 7.0999L12.975 11.9999L17.875 16.8999C18.0583 17.0833 18.15 17.3166 18.15 17.5999C18.15 17.8833 18.0583 18.1166 17.875 18.2999C17.6917 18.4833 17.4583 18.5749 17.175 18.5749C16.8917 18.5749 16.6583 18.4833 16.475 18.2999L11.575 13.3999Z"
                  fill="currentColor"
                ></path>
              </svg>
              <span>Платить за ремонт</span>
            </div>
          </div>
          <div class="flex-1 bg-white p-4 rounded border-l-4 border-green-500">
            <div class="flex items-start">
              <svg
                class="w-4 h-4 mt-0.5 mr-2 text-green-500 flex-shrink-0"
                viewBox="0 0 14 10"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M4.71975 9.65617C4.60863 9.65617 4.50446 9.63867 4.40725 9.60367C4.31002 9.56917 4.21975 9.51034 4.13641 9.427L0.553081 5.84367C0.400298 5.69084 0.327248 5.49284 0.333914 5.2495C0.341131 5.00667 0.421131 4.80892 0.573906 4.65617C0.726689 4.50334 0.921131 4.427 1.15725 4.427C1.39336 4.427 1.5878 4.50334 1.74057 4.65617L4.71975 7.63534L11.7823 0.572794C11.935 0.420019 12.1331 0.343628 12.3764 0.343628C12.6192 0.343628 12.8169 0.420019 12.9698 0.572794C13.1225 0.725578 13.1989 0.923353 13.1989 1.16613C13.1989 1.40946 13.1225 1.60752 12.9698 1.76029L5.3031 9.427C5.21976 9.51034 5.12943 9.56917 5.03225 9.60367C4.93502 9.63867 4.83086 9.65617 4.71975 9.65617Z"
                  fill="currentColor"
                ></path>
              </svg>
              <span
                >Мы выполним ремонт бесплатно, если поломка возникла из-за заводского брака, внутренней неисправности
                или естественного износа механизмов</span
              >
            </div>
          </div>
        </div>

        <div class="flex flex-col md:flex-row gap-4">
          <div class="flex-1 bg-white p-4 rounded border-l-4 border-red-500">
            <div class="flex items-start">
              <svg
                class="w-5 h-5 mt-0.5 mr-2 text-red-500 flex-shrink-0"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M11.575 13.3999L6.675 18.2999C6.49167 18.4833 6.25834 18.5749 5.975 18.5749C5.69167 18.5749 5.45834 18.4833 5.275 18.2999C5.09167 18.1166 5 17.8833 5 17.5999C5 17.3166 5.09167 17.0833 5.275 16.8999L10.175 11.9999L5.275 7.0999C5.09167 6.91656 5 6.68323 5 6.3999C5 6.11656 5.09167 5.88323 5.275 5.6999C5.45834 5.51656 5.69167 5.4249 5.975 5.4249C6.25834 5.4249 6.49167 5.51656 6.675 5.6999L11.575 10.5999L16.475 5.6999C16.6583 5.51656 16.8917 5.4249 17.175 5.4249C17.4583 5.4249 17.6917 5.51656 17.875 5.6999C18.0583 5.88323 18.15 6.11656 18.15 6.3999C18.15 6.68323 18.0583 6.91656 17.875 7.0999L12.975 11.9999L17.875 16.8999C18.0583 17.0833 18.15 17.3166 18.15 17.5999C18.15 17.8833 18.0583 18.1166 17.875 18.2999C17.6917 18.4833 17.4583 18.5749 17.175 18.5749C16.8917 18.5749 16.6583 18.4833 16.475 18.2999L11.575 13.3999Z"
                  fill="currentColor"
                ></path>
              </svg>
              <span>Мириться с простоями</span>
            </div>
          </div>
          <div class="flex-1 bg-white p-4 rounded border-l-4 border-green-500">
            <div class="flex items-start">
              <svg
                class="w-4 h-4 mt-0.5 mr-2 text-green-500 flex-shrink-0"
                viewBox="0 0 14 10"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M4.71975 9.65617C4.60863 9.65617 4.50446 9.63867 4.40725 9.60367C4.31002 9.56917 4.21975 9.51034 4.13641 9.427L0.553081 5.84367C0.400298 5.69084 0.327248 5.49284 0.333914 5.2495C0.341131 5.00667 0.421131 4.80892 0.573906 4.65617C0.726689 4.50334 0.921131 4.427 1.15725 4.427C1.39336 4.427 1.5878 4.50334 1.74057 4.65617L4.71975 7.63534L11.7823 0.572794C11.935 0.420019 12.1331 0.343628 12.3764 0.343628C12.6192 0.343628 12.8169 0.420019 12.9698 0.572794C13.1225 0.725578 13.1989 0.923353 13.1989 1.16613C13.1989 1.40946 13.1225 1.60752 12.9698 1.76029L5.3031 9.427C5.21976 9.51034 5.12943 9.56917 5.03225 9.60367C4.93502 9.63867 4.83086 9.65617 4.71975 9.65617Z"
                  fill="currentColor"
                ></path>
              </svg>
              <span
                >Мы заменим оборудование на исправное в день обращения или на следующий день. Если оборудования не будет
                в наличии, отправим к вам механика для ремонта</span
              >
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="mb-12">
      <h5 class="text-xl font-semibold mb-4">Важные вопросы о гарантии</h5>

      <div class="space-y-2">
        <div class="border-t border-gray-200"></div>

        <div class="bg-white rounded-lg overflow-hidden">
          <button class="w-full flex justify-between items-center p-4 text-left hover:bg-gray-50">
            <span class="font-medium">Всегда ли действует гарантия?</span>
            <svg
              class="w-5 h-5 text-gray-500 transform transition-transform"
              viewBox="0 0 20 20"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M4.26242 5.83333L9.99999 11.4987L15.7374 5.83333L17.5 7.57773L9.99999 15L2.5 7.57773L4.26242 5.83333Z"
                fill="currentColor"
              ></path>
            </svg>
          </button>
          <div class="px-4 pb-4 hidden">
            Нет, гарантия ВиРент не действует, если наши эксперты обнаружат механические повреждения, следы вскрытия или
            других вмешательств в конструкцию оборудования.
          </div>
        </div>

        <div class="border-t border-gray-200"></div>

        <div class="bg-white rounded-lg overflow-hidden">
          <button class="w-full flex justify-between items-center p-4 text-left hover:bg-gray-50">
            <span class="font-medium">Как определить, гарантийный случай или нет?</span>
            <svg
              class="w-5 h-5 text-gray-500 transform transition-transform"
              viewBox="0 0 20 20"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M4.26242 5.83333L9.99999 11.4987L15.7374 5.83333L17.5 7.57773L9.99999 15L2.5 7.57773L4.26242 5.83333Z"
                fill="currentColor"
              ></path>
            </svg>
          </button>
          <div class="px-4 pb-4 hidden">
            Перед ремонтом оборудование проходит экспертизу в течение 1 – 3 рабочих дней. За это время наши мастера
            определяют, является ли случай гарантийным.
          </div>
        </div>

        <div class="border-t border-gray-200"></div>

        <div class="bg-white rounded-lg overflow-hidden">
          <button class="w-full flex justify-between items-center p-4 text-left hover:bg-gray-50">
            <span class="font-medium">Что будет, если ремонт не пройдет по гарантии?</span>
            <svg
              class="w-5 h-5 text-gray-500 transform transition-transform"
              viewBox="0 0 20 20"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M4.26242 5.83333L9.99999 11.4987L15.7374 5.83333L17.5 7.57773L9.99999 15L2.5 7.57773L4.26242 5.83333Z"
                fill="currentColor"
              ></path>
            </svg>
          </button>
          <div class="px-4 pb-4 hidden">
            Клиенту нужно будет оплатить его. Мы отправим счет за ремонт и дефектную ведомость на электронную почту
            клиента и сообщим ему о платном ремонте.
          </div>
        </div>

        <div class="border-t border-gray-200"></div>
      </div>
    </div>

    <div class="mb-12">
      <h2 class="text-3xl font-bold mb-6 hidden sm:block">Важные условия аренды</h2>
      <h5 class="text-xl font-bold mb-6 sm:hidden">Важные условия аренды</h5>

      <div class="bg-white rounded-lg overflow-hidden">
        <div class="border-b border-gray-200">
          <nav class="flex overflow-x-auto -mb-px">
            <button class="px-4 py-3 font-medium text-blue-600 border-b-2 border-blue-600 whitespace-nowrap">
              Общие правила
            </button>
            <button
              class="px-4 py-3 font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 whitespace-nowrap"
            >
              Сроки аренды
            </button>
            <button
              class="px-4 py-3 font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 whitespace-nowrap"
            >
              Оплата услуги
            </button>
            <button
              class="px-4 py-3 font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 whitespace-nowrap"
            >
              Советы по эксплуатации
            </button>
            <button
              class="px-4 py-3 font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 whitespace-nowrap"
            >
              Порядок возврата
            </button>
          </nav>
        </div>

        <div class="p-4">
          <div>
            <ul class="list-disc pl-5 space-y-2">
              <li>Услугой аренды могут воспользоваться только юридические лица.</li>
              <li>
                Для оформления услуги обязательно гражданство Российской Федерации или наличие прописки на территории
                Российской Федерации.
              </li>
              <li>
                Услуга аренды действует на территории Москвы и Московской области, а также Санкт-Петербурга и
                Ленинградской области
              </li>
              <li>
                Ассортимент и цены можно узнать на сайте virent.ru, а также в прайс-листе, запросив его через сайт или у
                менеджера.
              </li>
              <li>Актуальное наличие товара, итоговую стоимость и размер залога следует уточнять у менеджера.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- <div class="mb-12 bg-gray-50 p-6 rounded-lg">
      <div class="max-w-2xl mx-auto">
        <div class="text-center mb-6">
          <h3 class="text-2xl font-semibold hidden sm:block mb-2">Есть вопросы?</h3>
          <h4 class="text-xl font-semibold sm:hidden mb-2">Есть вопросы?</h4>
          <p class="text-gray-600">Задайте их через форму обратной связи</p>
        </div>

        <form class="space-y-4">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Ваше имя</label>
            <input
              type="text"
              id="name"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
            <input
              type="tel"
              id="phone"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              placeholder="+7 (___) ___-__-__"
            />
          </div>

          <div>
            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Текст сообщения</label>
            <textarea
              id="message"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            ></textarea>
          </div>

          <button
            type="submit"
            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Отправить
          </button>

          <p class="text-xs text-gray-500">
            Нажимая кнопку «Отправить», я соглашаюсь на получение информации от интернет-магазина и уведомлений о
            состоянии моих заказов, а также принимаю условия
            <a
              href="/static/politika-konfidentsialnosti/"
              class="text-blue-600 hover:underline"
              title="условия политики конфиденциальности"
              >политики конфиденциальности</a
            >,
            <a
              href="/static/polzovatelskoe-soglashenie/"
              class="text-blue-600 hover:underline"
              title="пользовательского соглашения"
              >пользовательского соглашения</a
            >
            и даю согласие на
            <a
              href="https://virent.ru/static/politika-obrabotki-polzovatelskih-dannyh"
              class="text-blue-600 hover:underline"
              title="обработки персональных данных"
              >обработку персональных данных</a
            >
          </p>
        </form>
      </div>
    </div> -->
  </div>
</template>
