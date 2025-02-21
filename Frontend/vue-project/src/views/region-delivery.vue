<script setup lang="ts">
  import { ref } from 'vue';
  import Header from './Header.vue';
  import Footer from './Footer.vue';

  const isMkadInsideVisible = ref(false);
  const toggleMkadInside = () => {
    isMkadInsideVisible.value = !isMkadInsideVisible.value;
  };
</script>
<template>
    <Header />
    <div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
        <div>
            <p class="text-gray-600 mb-2">Главная / Доставка транспортной компанией</p>
            <h2 class="font-bold text-2xl mb-2">Доставка транспортной компанией</h2>
            <p>Доставка товаров осуществляется из Москвы. Цены на товары будут соответствовать ценам, действующим в нашем магазине для этого города. Вы можете выбрать доставку другой транспортной компанией, оформив заказ через менеджера по телефону.</p>
        </div>

        <h2 class="font-bold text-2xl mb-2">Наши основные партнеры</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="flex bg-gray-100 p-4 md:p-6 rounded-2xl shadow">
            <div class="bg-white w-24 h-16 md:w-32 md:h-20 rounded-lg mr-4 md:mr-8 flex-shrink-0">
                <img src="/public/russian-post.svg" alt="russianPost" class="w-full h-full object-contain mx-auto" />
            </div>
            <div class="flex-1">
                <h2 class="text-xl md:text-2xl font-bold my-2">Почта России</h2>
                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4 mt-2">
                <RouterLink to="" class="text-blue-700">Уточнит условия</RouterLink>
                <RouterLink to="" class="text-blue-700">Отследить груз</RouterLink>
                </div>
            </div>
            </div>

            <div class="flex bg-gray-100 p-4 md:p-6 rounded-2xl shadow">
            <div class="bg-white w-24 h-16 md:w-32 md:h-20 rounded-lg mr-4 md:mr-8 flex-shrink-0">
                <img src="/public/sdek.svg" alt="sdek" class="w-full h-full object-contain mx-auto" />
            </div>
            <div class="flex-1">
                <h2 class="text-xl md:text-2xl font-bold my-2">СДЭК</h2>
                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4 mt-2">
                <RouterLink to="" class="text-blue-700">Уточнит условия</RouterLink>
                <RouterLink to="" class="text-blue-700">Отследить груз</RouterLink>
                </div>
            </div>
            </div>

            <div class="flex bg-gray-100 p-4 md:p-6 rounded-2xl shadow">
            <div class="bg-white w-24 h-16 md:w-32 md:h-20 rounded-lg mr-4 md:mr-8 flex-shrink-0">
                <img src="/public/pek_logo.svg" alt="pek_logo" class="w-full h-full object-contain mx-auto" />
            </div>
            <div class="flex-1">
                <h2 class="text-xl md:text-2xl font-bold my-2">ПЭК</h2>
                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4 mt-2">
                <RouterLink to="" class="text-blue-700">Уточнит условия</RouterLink>
                <RouterLink to="" class="text-blue-700">Отследить груз</RouterLink>
                </div>
            </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <h2 class="font-bold text-2xl mb-4">Стоимость доставки заказа до транспортных компаний (ТК)</h2>
            <table class="min-w-full border-collapse text-left text-sm">
                <thead>
                    <tr>
                        <th class="px-4 py-3 border-b font-medium text-gray-700">Общий вес заказа</th>
                        <th class="px-4 py-3 border-b font-medium text-gray-700">Внутри МКАД</th>
                        <th class="px-4 py-3 border-b font-medium text-gray-700 hidden md:table-cell">От 0 до 15 км</th>
                        <th class="px-4 py-3 border-b font-medium text-gray-700 hidden md:table-cell">От 16 до 35 км</th>
                        <th class="px-4 py-3 border-b font-medium text-gray-700 hidden md:table-cell">От 36 до 60 км</th>
                        <th class="px-4 py-3 border-b font-medium text-gray-700 hidden md:table-cell">От 61 до 100 км</th>
                        <th class="px-4 py-3 border-b font-medium text-gray-700 hidden md:table-cell">От 101 до 130 км</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-4">от 0 до 14.9 кг</td>
                        <td class="px-4 py-4">290</td>
                        <td class="px-4 py-4 hidden md:table-cell">290</td>
                        <td class="px-4 py-4 hidden md:table-cell">390</td>
                        <td class="px-4 py-4 hidden md:table-cell">650</td>
                        <td class="px-4 py-4 hidden md:table-cell">750</td>
                        <td class="px-4 py-4 hidden md:table-cell">1000</td>
                    </tr>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-4">от 15 до 29.9 кг</td>
                        <td class="px-4 py-4">540</td>
                        <td class="px-4 py-4 hidden md:table-cell">540</td>
                        <td class="px-4 py-4 hidden md:table-cell">640</td>
                        <td class="px-4 py-4 hidden md:table-cell">900</td>
                        <td class="px-4 py-4 hidden md:table-cell">1000</td>
                        <td class="px-4 py-4 hidden md:table-cell">1250</td>
                    </tr>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-4">от 30 до 99.9 кг</td>
                        <td class="px-4 py-4">780</td>
                        <td class="px-4 py-4 hidden md:table-cell">780</td>
                        <td class="px-4 py-4 hidden md:table-cell">1500</td>
                        <td class="px-4 py-4 hidden md:table-cell">1800</td>
                        <td class="px-4 py-4 hidden md:table-cell">2100</td>
                        <td class="px-4 py-4 hidden md:table-cell">2400</td>
                    </tr>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-4">от 100 до 199.9 кг</td>
                        <td class="px-4 py-4">1200</td>
                        <td class="px-4 py-4 hidden md:table-cell">1400</td>
                        <td class="px-4 py-4 hidden md:table-cell">1700</td>
                        <td class="px-4 py-4 hidden md:table-cell">2000</td>
                        <td class="px-4 py-4 hidden md:table-cell">2300</td>
                        <td class="px-4 py-4 hidden md:table-cell">2600</td>
                    </tr>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-4">от 200 до 499.9 кг</td>
                        <td class="px-4 py-4">1450</td>
                        <td class="px-4 py-4 hidden md:table-cell">1650</td>
                        <td class="px-4 py-4 hidden md:table-cell">1950</td>
                        <td class="px-4 py-4 hidden md:table-cell">2250</td>
                        <td class="px-4 py-4 hidden md:table-cell">2550</td>
                        <td class="px-4 py-4 hidden md:table-cell">2850</td>
                    </tr>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-4">от 500 до 999.9 кг</td>
                        <td class="px-4 py-4">1700</td>
                        <td class="px-4 py-4 hidden md:table-cell">1900</td>
                        <td class="px-4 py-4 hidden md:table-cell">2200</td>
                        <td class="px-4 py-4 hidden md:table-cell">2500</td>
                        <td class="px-4 py-4 hidden md:table-cell">2800</td>
                        <td class="px-4 py-4 hidden md:table-cell">3100</td>
                    </tr>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-4">от 1000 до 1499.9 кг</td>
                        <td class="px-4 py-4">9400</td>
                        <td class="px-4 py-4 hidden md:table-cell">9800</td>
                        <td class="px-4 py-4 hidden md:table-cell">10400</td>
                        <td class="px-4 py-4 hidden md:table-cell">11100</td>
                        <td class="px-4 py-4 hidden md:table-cell">12300</td>
                        <td class="px-4 py-4 hidden md:table-cell">13200</td>
                    </tr>
                </tbody>
            </table>
            <div class="my-2 mb-2">
                <p class="text-gray-700 text-sm">
                    ⚠️ При заказе товаров длиной более 3,5 м и/или весом более 100 кг, а также при расстоянии доставки более 15 км стоимость услуги согласовывается отдельно.
                </p>
            </div>
        </div>

        <div>
            <div>
                <div class="flex flex-col w-full mt-2 hover:bg-gray-200 rounded">
                    <hr class="border-gray-300" />
                    <div class="flex items-center w-full my-1">
                    <button @click="toggleMkadInside" class="px-4 py-2 rounded w-full text-left bg-transparent border-none">
                        Памятка покупателя
                    </button>
                    <img :src="isMkadInsideVisible ? '/public/arrow-up.svg' : '/public/arrow-down.svg'" alt="arrow" class="w-4 h-4 mr-2" />
                    </div>
                    <hr class="border-gray-300" />
                </div>
                <hr class="border-gray-300" />
            </div>
            <div v-if="isMkadInsideVisible" class="mt-4 p-4">
                <ol class="list-decimal list-inside space-y-4">
                    <li>Как только заказ (груз) прибудет в город, с вами свяжется представитель транспортной компании. Вам необходимо будет оплатить услуги доставки и получить покупку.</li>
                    <li>Если заказ был оформлен от имени юридического лица, для получения товара необходимо предоставить доверенность от организации или печать. Без необходимых документов или печати отгрузка товаров не производится.</li>
                    <li>При приеме заказа в офисе транспортной компании нужно выполнить следующие действия.</li>
                    <ul class="list-disc list-inside space-y-2">
                        <li>Весить посылку, сравнить по накладной вес при отправке и вес при получении посылки</li>
                        <li>Проверить внешний вид посылки. Она должна быть в целой упаковке, не помята и не разорвана, упаковка должна быть перемотана фирменным скотчем «ВсеИнструменты.ру»</li>
                        <li>Проверить внешний вид инструмента. На нем не должно быть дефектов: вмятин, деформаций, сколов</li>
                    </ul>
                    <li>В случае обнаружения каких-либо недостатков нужно сразу составить заявление-претензию в офисе транспортной компании. Если есть возможность, необходимо сфотографировать все недостатки. Если вы забрали посылку из офиса транспортной компании и обнаружили недостатки по внешнему виду и целостности упаковки уже за пределами офиса, претензии приниматься не будут.</li>
                </ol>
            </div>
        </div>
    </div>
    <Footer />
</template>