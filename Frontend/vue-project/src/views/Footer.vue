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
                      <li class="hover:text-rose-700">Сервисный центр ВсеИнструменты.ру</li>
                      <li class="hover:text-rose-700">Обратная связь</li>
                      <li class="hover:text-rose-700">Помощь по работе с сайтом</li>
                      <li class="hover:text-rose-700 font-bold my-4">Работа у нас</li>
                      <li class="hover:text-rose-700 font-bold my-4">Арендодателям</li>
                  </ul>
              </div>
  
              <div class="flex flex-col items-center ">
              <div class="border border-black rounded-lg p-4 ">
                  <img src="/public/qr.svg" alt="QR Code" class="w-32 h-32 mx-auto" />
                  <p class="mt-2 text-center font-bold">Всё для дома, дачи, стройки и ремонта в вашем смартфоне</p>
                  <div class="flex justify-center mt-2">
                      <img src="" alt="Play Market" class="w-5 h-5 mx-1" />
                      <img src="" alt="App Store" class="w-5 h-5 mx-1" />
                  </div>
              </div>
          </div>
          <div>
            <h1 class="mb-2 font-bold">Давайте дружить</h1>
            <div class="flex items-center space-x-4">
                <img src="/public/vk.svg" alt="vkmini" class="h-8 w-8"/>
                <img src="/public/youtube.svg" alt="yotubemini" class="h-8 w-8"/>
                <img src="/public/telegram.svg" alt="telegrammini" class="h-8 w-8"/>
                <img src="/public/rutube.svg" alt="rutubemini" class="h-8 w-8"/>
                <img src="/public/yandex-zen.svg" alt="zenmini" class="h-8 w-8"/>
                <img src="/public/odnoklassniki.svg" alt="odnoklassnikimini" class="h-8 w-8"/>
            </div>
            <div class="text-xs mt-4 w-full">
                Вы принимаете условия политики в отношении обработки персональных данных и пользовательского соглашения каждый раз, когда оставляете свои
                данные в любой форме обратной связи на сайте ВсеИнструменты.ру
                <br />
                © 2006 — 2025. ВсеИнструменты.ру
            </div>
          </div>
  
        </div>
      </div>
  </template>

