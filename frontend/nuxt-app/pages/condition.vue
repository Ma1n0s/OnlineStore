<script setup>
useHead({
  title: `Абсолют техно`,
  meta: [
    {
      name: 'description',
      content: `Инструменты и оборудование для строительства и ремота`,
    },
  ],
})

// const toggleFAQ = event => {
//   const button = event.currentTarget
//   const content = button.nextElementSibling
//   const icon = button.querySelector('icon')

//   content.classList.toggle('hidden')
//   icon.classList.toggle('transform')
//   icon.classList.toggle('rotate-180')
// }
</script>
<template>
  <div class="min-h-screen bg-white">
    <div class="relative text-black py-20">
      <div class="container mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Условия аренды инструмента</h1>
        <p class="text-xl md:text-2xl max-w-2xl mx-auto opacity-90">
          Гибкие условия аренды профессионального оборудования
        </p>
      </div>
      <div class="absolute bottom-0 left-0 right-0 h-12 bg-white transform skew-y-1 origin-top-left"></div>
    </div>

    <div class="container mx-auto px-6 py-16 -mt-10">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2">
          <div class="bg-white p-8 rounded-xl shadow-lg mb-12">
            <h2 class="text-3xl font-bold mb-8">Общие условия аренды</h2>

            <div class="space-y-8">
              <div class="p-6 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                <div class="flex items-start mb-4">
                  <div class="p-2 rounded-full bg-second/10 mr-4">
                    <Icon name="material-symbols:check-rounded" class="w-6 h-6 text-second" />
                  </div>
                  <div>
                    <h3 class="text-xl font-semibold mb-2">Требования к арендатору</h3>
                    <p class="text-gray-600">
                      Для оформления аренды необходимо предоставить паспорт и подписать договор. Для юридических лиц -
                      пакет документов компании. Залоговая сумма составляет 30-50% от стоимости оборудования.
                    </p>
                  </div>
                </div>
              </div>

              <div class="p-6 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                <div class="flex items-start mb-4">
                  <div class="p-2 rounded-full bg-second/10 mr-4">
                    <Icon name="material-symbols:check-rounded" class="w-6 h-6 text-second" />
                  </div>
                  <div>
                    <h3 class="text-xl font-semibold mb-2">Сроки аренды</h3>
                    <p class="text-gray-600">
                      Минимальный срок аренды - 1 сутки. Максимальный срок без переоформления - 30 дней. При длительной
                      аренде (более 7 дней) предоставляются специальные условия.
                    </p>
                  </div>
                </div>
              </div>

              <div class="p-6 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                <div class="flex items-start mb-4">
                  <div class="p-2 rounded-full bg-second/10 mr-4">
                    <Icon name="material-symbols:check-rounded" class="w-6 h-6 text-second" />
                  </div>
                  <div>
                    <h3 class="text-xl font-semibold mb-2">Самовывоз</h3>
                    <p class="text-gray-600">
                      Все оборудование выдается и принимается только в нашем офисе по адресу: г. Нижний Тагил, ул.
                      Аганичева 101А. Перед выдачей проводится обязательный инструктаж и проверка работоспособности.
                    </p>
                  </div>
                </div>
              </div>

              <div class="p-6 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                <div class="flex items-start mb-4">
                  <div class="p-2 rounded-full bg-second/10 mr-4">
                    <Icon name="material-symbols:check-rounded" class="w-6 h-6 text-second" />
                  </div>
                  <div>
                    <h3 class="text-xl font-semibold mb-2">Состояние оборудования</h3>
                    <p class="text-gray-600">
                      Оборудование выдается в чистом, исправном состоянии с полным комплектом принадлежностей. При
                      возврате проводится тщательная проверка на соответствие исходному состоянию.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 p-8 rounded-xl shadow-lg mb-12">
            <h2 class="text-3xl font-bold mb-8">Тарифы и оплата</h2>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                  <tr>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Тип оборудования
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Сутки
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Неделя
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Месяц
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      Строительный инструмент
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">от 500 ₽</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">от 2 500 ₽</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">от 8 000 ₽</td>
                  </tr>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      Профессиональное оборудование
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">от 1 500 ₽</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">от 7 000 ₽</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">от 25 000 ₽</td>
                  </tr>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Промышленные решения</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">от 5 000 ₽</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">от 20 000 ₽</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">от 70 000 ₽</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="mt-8 p-6 bg-second/5 rounded-lg border border-second/20">
              <h3 class="text-xl font-semibold mb-3 text-second">Специальные предложения</h3>
              <p class="text-gray-600">
                При аренде оборудования на срок более 14 дней предоставляется скидка 15%. Для постоянных клиентов
                действуют индивидуальные тарифы.
              </p>
            </div>
          </div>

          <div class="bg-white p-8 rounded-xl shadow-lg">
            <h2 class="text-3xl font-bold mb-8">Ответственность и гарантии</h2>

            <div class="space-y-6">
              <div class="flex items-start">
                <div class="flex-shrink-0 mt-1">
                  <div class="flex items-center justify-center h-6 w-6 rounded-full bg-red-100">
                    <Icon name="heroicons:exclamation-circle" class="h-4 w-4 text-red-500" />
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-gray-600">
                    <span class="font-semibold">Важно:</span> Арендатор несет полную материальную ответственность за
                    сохранность и работоспособность оборудования на весь срок аренды.
                  </p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="flex-shrink-0 mt-1">
                  <div class="flex items-center justify-center h-6 w-6 rounded-full bg-green-100">
                    <Icon name="heroicons:shield-check" class="h-4 w-4 text-green-500" />
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-gray-600">
                    Мы гарантируем исправность всего оборудования на момент передачи. В случае поломки по вине
                    производителя или естественного износа, ремонт осуществляется за наш счет.
                  </p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="flex-shrink-0 mt-1">
                  <div class="flex items-center justify-center h-6 w-6 rounded-full bg-blue-100">
                    <Icon name="heroicons:document-text" class="h-4 w-4 text-blue-500" />
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-gray-600">
                    Все условия аренды, включая стоимость, сроки и ответственность, фиксируются в договоре, который
                    подписывается обеими сторонами.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="space-y-8">
          <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-second">
            <h3 class="text-xl font-bold mb-4">Часто задаваемые вопросы</h3>

            <div class="space-y-4">
              <div class="border-b border-gray-200 pb-4">
                <button class="flex justify-between items-center w-full text-left">
                  <span class="font-medium text-gray-900">Какие документы нужны для аренды?</span>
                  <Icon name="heroicons:chevron-down" class="h-5 w-5 text-gray-500" />
                </button>
                <p class="mt-2 text-sm text-gray-600 hidden">
                  Для физических лиц: паспорт. Для юридических лиц: пакет документов компании (свидетельство о
                  регистрации, ИНН, документы на подписавшего договор).
                </p>
              </div>

              <div class="border-b border-gray-200 pb-4">
                <button class="flex justify-between items-center w-full text-left">
                  <span class="font-medium text-gray-900">Что делать при поломке оборудования?</span>
                  <Icon name="heroicons:chevron-down" class="h-5 w-5 text-gray-500" />
                </button>
                <p class="mt-2 text-sm text-gray-600 hidden">
                  Немедленно прекратите использование и свяжитесь с нами. Не пытайтесь ремонтировать оборудование
                  самостоятельно - это может привести к дополнительным повреждениям.
                </p>
              </div>

              <div class="border-b border-gray-200 pb-4">
                <button class="flex justify-between items-center w-full text-left">
                  <span class="font-medium text-gray-900">Можно ли продлить аренду?</span>
                  <Icon name="heroicons:chevron-down" class="h-5 w-5 text-gray-500" />
                </button>
                <p class="mt-2 text-sm text-gray-600 hidden">
                  Да, при условии, что оборудование не забронировано другим клиентом. О продлении необходимо сообщить не
                  менее чем за 24 часа до окончания срока аренды.
                </p>
              </div>

              <div class="border-b border-gray-200 pb-4">
                <button class="flex justify-between items-center w-full text-left">
                  <span class="font-medium text-gray-900">Как рассчитывается залог?</span>
                  <Icon name="heroicons:chevron-down" class="h-5 w-5 text-gray-500" />
                </button>
                <p class="mt-2 text-sm text-gray-600 hidden">
                  Залог составляет 30-50% от стоимости оборудования и возвращается полностью при возврате техники в
                  исправном состоянии. При повреждениях сумма ущерба вычитается из залога.
                </p>
              </div>
            </div>
          </div>

          <div class="bg-gradient-to-r from-second to-second-dark rounded-xl p-8 text-white shadow-lg">
            <h3 class="text-xl font-bold mb-4">Остались вопросы?</h3>
            <p class="mb-6">Наши специалисты готовы проконсультировать вас по всем условиям аренды</p>
            <button class="w-full bg-white text-second font-bold py-3 px-6 rounded-lg hover:bg-opacity-90 transition">
              Заказать звонок
            </button>
          </div>

          <div class="bg-white p-6 rounded-xl shadow-md">
            <h3 class="text-xl font-bold mb-4">Часы работы</h3>
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-gray-600">Понедельник - Пятница</span>
                <span class="font-medium">9:00 - 18:00</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Суббота</span>
                <span class="font-medium">10:00 - 16:00</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Воскресенье</span>
                <span class="font-medium">Выходной</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
