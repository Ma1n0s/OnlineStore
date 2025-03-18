<script setup lang="ts">
  import { ref } from 'vue';
  import Header from '../Header.vue';
  import Footer from '../Footer.vue';

  const isMkadInsideVisible = ref(false);
  const isReturnPolicyVisible = ref(false);
  const isShippingOptionsVisible = ref(false);
  const toggleMkadInside = () => {
    isMkadInsideVisible.value = !isMkadInsideVisible.value;
  };
  const toggleReturnPolicy = () => {
    isReturnPolicyVisible.value = !isReturnPolicyVisible.value;
  };
  const toggleShippingOptions = () => {
    isShippingOptionsVisible.value = !isShippingOptionsVisible.value;
  };
</script>
<template>
    <Header/>
    <div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
        <div>
            <p class="text-gray-600 mb-2">Главная / Доставка транспортной компанией / Доставка Почтой России</p>
        </div>

        <h2 class="font-bold text-2xl mb-2">Доставка Почтой России</h2>
        <div>
            <div class="bg-white border-2 shadow-sm w-24 h-16 md:w-32 md:h-20 rounded-lg mr-4 md:mr-8 flex-shrink-0">
                <img src="/public/russian-post.svg" alt="russianPost" class="w-full h-full object-contain mx-auto" />
            </div>
            <p>Доставка товаров осуществляется из Москвы. Цены на товары будут соответствовать ценам, действующим в нашем магазине для этого города.</p>

            <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0">
                <div class="bg-gray-100 p-6 rounded-lg shadow-md w-full sm:w-1/3">
                    <h2 class="text-lg font-semibold mb-4">Ограничения</h2>
                    <ul class="list-disc list-inside">
                    <li>Доставка Почтой России работает в тестовом режиме, только при заказе и оплате через сайт</li>
                    <li>Доставляются заказы весом до 20 кг</li>
                    <li>Доставляются заказы, имеющие небольшие габариты: сумма трех измерений (длина, ширина, высота) не должна превышать 3 м</li>
                    <li>Жидкости не доставляются</li>
                    </ul>
                </div>
                
                <div class="bg-gray-100 p-6 rounded-lg shadow-md w-full sm:w-1/3">
                    <h2 class="text-lg font-semibold mb-4">Оплата для физических лиц</h2>
                    <ul class="list-disc list-inside">
                    <li>Электронные способы оплаты через сайт</li>
                    <li>Банковский или почтовый перевод</li>
                    </ul>
                </div>
                
                <div class="bg-gray-100 p-6 rounded-lg shadow-md w-full sm:w-1/3">
                    <h2 class="text-lg font-semibold mb-4">Отправка заказа</h2>
                    <p>Оформить заказ на доставку Почтой России можно только через корзину на сайте. Формирование и отправка заказа осуществляются только после его оплаты. О том, что оплата прошла, мы сообщим через смс или уведомление по электронной почте.</p>
                </div>
            </div>
            
            <div>
            <div>
                <h2 class="font-bold text-2xl">Частые вопросы</h2>
                
                    <div class="flex flex-col w-full mt-2 hover:bg-gray-200 rounded">
                        <hr class="border-gray-300" />
                        <div class="flex items-center w-full my-1">
                            <button @click="toggleMkadInside" class="px-4 py-2 rounded w-full text-left bg-transparent border-none">
                                Стоимость и сроки
                            </button>
                            <img :src="isMkadInsideVisible ? '/public/arrow-up.svg' : '/public/arrow-down.svg'" alt="arrow" class="w-4 h-4 mr-2" />
                        </div>
                        <hr class="border-gray-300" />
                    </div>
                    <div v-if="isMkadInsideVisible" class="mt-4 p-4">
                        <p class="text-gray-800 leading-relaxed">
                            Сроки и стоимость рассчитываются в зависимости от выбранного региона доставки и веса заказа. Расчет производится на основе данных, предоставляемых Почтой России. Эту информацию вы узнаете в карточке товара на вкладке «Способы получения товара», а также при оформлении заказа. Срок доставки указывается с момента передачи заказа в «Почту России».
                        </p>
                    </div>

                    <div class="flex flex-col w-full mt-2 hover:bg-gray-200 rounded">
                        <hr class="border-gray-300" />
                        <div class="flex items-center w-full my-1">
                            <button @click="toggleReturnPolicy" class="px-4 py-2 rounded w-full text-left bg-transparent border-none">
                                Как Отследить отправку заказа ?
                            </button>
                            <img :src="isReturnPolicyVisible ? '/public/arrow-up.svg' : '/public/arrow-down.svg'" alt="arrow" class="w-4 h-4 mr-2" />
                        </div>
                        <hr class="border-gray-300" />
                    </div>
                    <div v-if="isReturnPolicyVisible" class="mt-4 p-4">
                        <p class="text-gray-800 leading-relaxed">
                            <div>
                                <ul class="list-disc ml-5 text-gray-800">
                                    <li class="mb-2">
                                        Через наш сайт по номеру заказа: в верхнем правом углу кликните по ссылке 
                                        <!-- <a href="#" class="text-blue-600 underline">«Проверить статус заказа»</a> -->
                                    </li>
                                    <li class="mb-2">
                                        Через сервис Почты России по трек-номеру: на сайте или в 
                                        <!-- <a href="#" class="text-blue-600 underline">мобильном приложении</a> -->
                                    </li>
                                </ul>
                                <p class="text-gray-800 mt-4">
                                    Трек-номер вы найдете на странице заказа в личном кабинете, как только заказ будет передан на доставку в Почту России.
                                </p>
                            </div>
                        </p>
                    </div>

                    <div class="flex flex-col w-full mt-2 hover:bg-gray-200 rounded">
                        <hr class="border-gray-300" />
                        <div class="flex items-center w-full my-1">
                            <button @click="toggleShippingOptions" class="px-4 py-2 rounded w-full text-left bg-transparent border-none">
                                Получение заказа
                            </button>
                            <img :src="isShippingOptionsVisible ? '/public/arrow-up.svg' : '/public/arrow-down.svg'" alt="arrow" class="w-4 h-4 mr-2" />
                        </div>
                        <hr class="border-gray-300" />
                    </div>
                    <div v-if="isShippingOptionsVisible" class="mt-4 p-4">
                        <p class="text-gray-800 mb-4">
                            В соответствии с указанным адресом заказ будет отправлен в районное почтовое отделение. О его доставке вы получите извещение. Заберите заказ в течение 5 рабочих дней. Хранение свыше этого срока оплачивается отдельно. Посылка может храниться в почтовом отделении в течение 15 дней. После этого она будет возвращена отправителю, а заказ отменен.
                        </p>
                        <ul class="list-disc pl-6 text-gray-700 mb-4">
                            <li>При приеме заказа в почтовом отделении нужно взвесить посылку – сравнить вес по накладной и фактический.</li>
                            <li>Важно проверить внешний вид упаковки – она не должна быть помята или разорвана.</li>
                            <li>Вскрывая упаковку, следует проверить наличие всех заказанных позиций, а также внешний вид товара. На нем не должно быть дефектов (вмятин, сколов, деформаций).</li>
                        </ul>
                        <p class="text-gray-800 mb-4">
                            При обнаружении любых недостатков товара составьте заявление-претензию непосредственно в почтовом отделении. Желательно сфотографировать все дефекты. При обнаружении недостатков вне почтового отделения претензии не принимаются.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <Footer/>
</template>