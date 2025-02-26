<script setup>
import { ref } from 'vue';


</script>

<template>
      <div class="flex bg-gray-200 items-center justify-between px-52 p-2">
          <div class="font-bold flex flex-grow mr-4">
              <img src="/public/email.svg" alt="mail" class="w-5 h-5 mr-4" />
              Подпишитесь на рассылку и будьте в курсе! Акции, скидки, распродажи ждут!
          </div>
          <div class="flex-grow mr-4">
              <input type="text" placeholder="Введите email" class="h-8 w-full rounded-lg" />
          </div>
          <div>
              <button class="bg-red-600 rounded-lg text-white h-8 w-full mx-4">Подписаться</button>
          </div>
      </div>
  
      <div>
            <div class="grid grid-cols-5 gap-4 bg-gray-100 p-4 px-52">
              <div>
                  <ul>
                      <li class="font-bold mb-4">О компании</li>
                      <li class="hover:text-rose-700">Реквизиты</li>
                      <li class="hover:text-rose-700">О нас</li>
                      <li class="hover:text-rose-700">Информация для инвесторов</li>
                      <li class="hover:text-rose-700">Сертификаты</li>
                      <li class="hover:text-rose-700">Производители</li>
                      <li class="hover:text-rose-700">Правовая информация</li>
                      <li class="hover:text-rose-700">Правила применения рекомендательных технологий</li>
                  </ul>
              </div>
  
              <div>
                  <ul>
                      <li class="font-bold">Акции</li>
                      <li class="hover:text-rose-700  my-4">Наши акции</li>
                      <li class="font-bold mb-4">Компаниям</li>
                      <li class="hover:text-rose-700">Поставщикам</li>
                      <li class="hover:text-rose-700">Организациям</li>
                  </ul>
              </div>
  
              <div>
                  <ul>
                      <li class="font-bold mb-4">Получение и оплата</li>
                      <li class="hover:text-rose-700">Доставка курьером</li>
                      <li class="hover:text-rose-700">Доставка транспортной компанией</li>
                      <li class="hover:text-rose-700">Самовывоз</li>
                      <li class="hover:text-rose-700">Способы оплаты</li>
                  </ul>
              </div>
  
              <div>
                  <ul>
                      <li class="font-bold mb-4">Сервис и поддержка</li>
                      <li class="hover:text-rose-700">Обратная связь</li>
                      <li class="hover:text-rose-700">Помощь по работе с сайтом</li>
                      <li class="hover:text-rose-700 font-bold my-4">Работа у нас</li>
                      <li class="hover:text-rose-700 font-bold my-4">Арендодателям</li>
                  </ul>
              </div>
  
             
          <div>
           
           
          </div>
  
        </div>
      </div>
  </template>

