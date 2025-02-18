<script setup>
import { ref } from 'vue';
import LoginModal from '@/components/LoginModal.vue';
import BaceIcon from '@/components/BaceIcon.vue';

const thisMenu = ref(false);
const showMenu = ref(false);
const aboutUsMenu = ref(false);
const LoginModals = ref(false);
const infoMenu = ref(false);
const Menu = ref(false);

function OpenModalLogin(){
  LoginModals.value = true;
}
function CloseModalLogin(){
  LoginModals.value = false;
}

</script>

<template>
  <div class="bg-white shadow">
    <div class="container mx-auto flex justify-between items-center px-4 py-4">
      <div class="flex justify-between items-center">
        <div class="flex space-x-4">
          <div class="flex">
            <img src="/public/flag-black.svg" alt="Logo" class="w- h-5 mr-2" />
            <div class="text-[12px] hover:text-rose-700">Москва</div>
          </div>
          <div class="flex">
            <img src="/public/location.svg" alt="Logo" class="w-5 h-5 mr-2" />
            <div class="text-[12px] hover:text-rose-700">{{ 369 }} магазинов</div>
          </div>
          <div class="flex justify-between space-x-4 items-center px-72">
            <div class="flex">
              <img src="/public/portfolio.svg" alt="Logo" class="w-5 h-5" />
              <div class="text-[12px]">Покупаю как юрлицо</div>
            </div>
            <div class="flex relative">
              <div 
                class="text-[12px] cursor-pointer "
                @mouseenter="showMenu = true"
                @mouseleave="showMenu = false"
              >
                Получение и оплата
                <div v-if="showMenu" class="absolute left-0 mt-1 bg-white shadow-lg rounded-md z-10"
                @mouseenter="showMenu = true"
                @mouseleave="showMenu = false"
                >
                <nav>
                  <ul class="py-2">
                    <li class="px-4 py-2 hover:bg-gray-100">Категория 1</li>
                    <li class="px-4 py-2 hover:bg-gray-100">Категория 2</li>
                    <li class="px-4 py-2 hover:bg-gray-100">Категория 3</li>
                    <li class="px-4 py-2 hover:bg-gray-100">Категория 4</li>
                  </ul>
                </nav>
                </div>
              </div>
            </div>
            <div class="flex">
              <div class="text-[12px] hover:text-rose-700">Сервис и поддержка</div>
            </div>
            <div class="flex relative">
              <div 
                class="text-[12px] cursor-pointer"
                @mouseenter="aboutUsMenu = true"
                @mouseleave="aboutUsMenu = false"
              >
                О нас
                <div v-if="aboutUsMenu" class="absolute left-0 mt-1 bg-white shadow-lg rounded-md z-10"
                @mouseenter="aboutUsMenu = true"
                @mouseleave="aboutUsMenu = false"
                >
                  <ul class="py-2">
                    <li class="px-4 py-2 hover:bg-gray-100">Категория 1</li>
                    <li class="px-4 py-2 hover:bg-gray-100">Категория 2</li>
                    <li class="px-4 py-2 hover:bg-gray-100">Категория 3</li>
                    <li class="px-4 py-2 hover:bg-gray-100">Категория 4</li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="flex">
              <div class="text-[12px] hover:text-rose-700">Инвесторам</div>
            </div>
          </div>
        </div>
      </div>

      <div class="flex justify-end items-end">
          <img src="/public/question-mark.svg" alt="Logo" class="w-5 h-5 mr-2" />
          <div class="text-[12px]">8 800 550-37-70</div>
        </div>
    </div>
      <div class="container mx-auto flex justify-between items-center px-4 py-4">
        <div class="flex items-center ">
            <img src="/public/FULL.png" alt="Logo" class="w-40 " />
            <div class="flex items-center ml-4">
         <div class="flex items-center justify-center bg-gray-700 p-3 rounded-lg text-white">   
          <img src="/public/menu.svg" alt="categor" class="w-3 h-3 bg-white shadow-lg z-10 mr-2" />
          <div 
              class="cursor-pointer  hover:text-blue-600 relative"
              @mouseenter="thisMenu = true"
              @mouseleave="thisMenu = false"
          >
              Категория
              <div v-if="thisMenu" class="absolute bg-white border  rounded-lg shadow-lg mt-2 z-20">
                  <nav>
                      <ul class="py-2">
                          <li class="px-4 py-2 hover:bg-gray-100">Категория 1</li>
                          <li class="px-4 py-2 hover:bg-gray-100">Категория 2</li>
                          <li class="px-4 py-2 hover:bg-gray-100">Категория 3</li>
                          <li class="px-4 py-2 hover:bg-gray-100">Категория 4</li>
                      </ul>
                  </nav>
              </div>
          </div>
        </div>
    </div>
    </div>
    <div class="flex items-center space-x-4">
        <div class="flex border border-2 border-gray-400 rounded-lg overflow-hidden">
            <input type="text" placeholder="Все для дома,дачи,ремонта и стройки" class="p-3 w-96 outline-none" />
            <button @click="handleClick" class="bg-gray-400 p-3 focus:outline-none">
                <img src="/public/zoom-black.svg" alt="search" class="w-6 h-6" />
            </button>
        </div>
        <div class="flex items-center space-x-1">
            <img src="/public/stars.svg" alt="favorite" class="w-5 h-5 "/>
            <div class="hover:text-rose-700">Избранное</div>
        </div>
        <div class="flex items-center space-x-1">
            <img src="/public/pantone.svg" alt="favorite" class="w-5 h-5" />
            <div class="hover:text-rose-700">Сравнение</div>
        </div>
        <div class="flex items-center space-x-1">
            <img src="/public/order.svg" alt="box" class="w-5 h-5" />
            <div class="hover:text-rose-700">Заказы</div>
        </div>
        <div class="flex items-center space-x-1">
            <img src="/public/shopping-cart.svg" alt="buy" class="w-5 h-5" />
            <div class="hover:text-rose-700">Корзина</div>
        </div>
        <div>
            <button @click="OpenModalLogin" class="border border-2 rounded-lg bg-gray-300 px-4 py-2">
                Войти
            </button>
            <!-- <LoginModal :isOpen="isModalOpen" @close="closeModal" name="x"/> -->
        </div>
    </div>
</div>

    <div class="container mx-auto flex justify-between items-center px-4 py-4 mb-10">
      <div class="flex justify-between items-center">
        <nav>
          <ul class="flex space-x-4">
            <li class="text-red-600">% Акции</li>
            <li class="hover:text-rose-700">Уборка снега</li>
            <li class="hover:text-rose-700">Отопление</li>
            <li class="hover:text-rose-700">Инструмент</li>
            <li class="hover:text-rose-700">Электрика</li>
            <li class="hover:text-rose-700">Силовая техника</li>
            <li class="hover:text-rose-700">Станки</li>
            <li class="hover:text-rose-700">Спецодежда и СИЗ</li>
            <li class="hover:text-rose-700">Сантехника</li>
            <li class="hover:text-rose-700">Авто</li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>
