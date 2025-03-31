<script setup>
import { ref, watch, computed } from 'vue';

const selectAll = ref(false);
const items = ref([
  {
    id: 1,
    name: 'WMSITE10',
    code: '16313057',
    description: 'Бесщеточная аккумуляторная дрель-шуруповерт AEG BS18SBL-202C 4935472277',
    price: '25 790 ₽',
    quantity: 1,
    selected: false,
    image: '/placeholder-product.jpg'
  },
  {
    id: 2,
    name: 'WMSITE11 товар',
    code: '12345678',
    description: 'Компактный перфоратор с мощным двигателем',
    price: '15 000 ₽',
    quantity: 2,
    selected: false,
    image: '/placeholder-product.jpg'
  },
]);

const customerName = ref('');
const showQrCode = ref(false);

const increaseQuantity = (item) => {
  item.quantity += 1;
};

const decreaseQuantity = (item) => {
  if (item.quantity > 1) {
    item.quantity -= 1;
  }
};

const toggleSelectAll = () => {
  items.value.forEach((item) => {
    item.selected = selectAll.value;
  });
};

const removeSelectedItems = () => {
  items.value = items.value.filter(item => !item.selected);
};

const totalAmount = computed(() => {
  return items.value.reduce((sum, item) => {
    if (item.selected) {
      const price = parseInt(item.price.replace(/\D/g, '')) || 0;
      return sum + (price * item.quantity);
    }
    return sum;
  }, 0).toLocaleString('ru-RU') + ' ₽';
});

const calculateTotalWeight = () => {
  // Здесь можно добавить реальный расчет веса на основе товаров
  return '4,8'; // Примерное значение
};

watch(
  () => items.value.every((item) => item.selected),
  (allSelected) => {
    selectAll.value = allSelected;
  }
);
</script>

<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
      <div class="flex flex-col lg:flex-row gap-6">
        <div class="lg:w-3/4">
          <div class="bg-white rounded-xl p-6 mb-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
              <div class="flex items-center mb-4 sm:mb-0">
                <h1 class="text-2xl font-bold mr-3 text-gray-800">Корзина</h1>
                <span class="text-gray-500">2741-0895-29725</span>
              </div>
              <div class="flex flex-col items-end">
                <NuxtLink to="/" class="text-blue-600 hover:text-blue-800 transition-colors">
                  ← Вернуться к покупкам
                </NuxtLink>
              </div>
            </div>
            
            <div class="relative">
              <input
                type="text"
                placeholder="Быстрое добавление: введите наименование или код товара..."
                class="w-full p-3 pl-10 border border-gray-300 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <Icon name="heroicons:magnifying-glass" class="absolute left-3 top-3.5 text-gray-400" />
            </div>
          </div>
          
          <div class="bg-white rounded-xl p-6 mb-6 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
              <div class="flex items-center">
                <input
                  type="checkbox"
                  id="select-all"
                  v-model="selectAll"
                  @change="toggleSelectAll"
                  class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300"
                />
                <label for="select-all" class="ml-2 text-gray-700">Выделить все</label>
                <button 
                  @click="removeSelectedItems"
                  class="ml-4 flex items-center text-gray-500 hover:text-red-600 transition-colors"
                >
                  <Icon name="heroicons:trash" class="mr-1" />
                  Удалить выбранные
                </button>
              </div>
              
              <div class="flex items-center space-x-4 text-gray-500">
                <button class="flex items-center hover:text-blue-600 transition-colors">
                  <Icon name="heroicons:share" class="mr-1" />
                  Поделиться
                </button>
                <button class="flex items-center hover:text-blue-600 transition-colors">
                  <Icon name="heroicons:arrow-down-tray" class="mr-1" />
                  Скачать
                </button>
                <button class="flex items-center hover:text-blue-600 transition-colors">
                  <Icon name="heroicons:printer" class="mr-1" />
                  Распечатать
                </button>
              </div>
            </div>
            
            <div class="divide-y divide-gray-200">
              <div 
                v-for="item in items" 
                :key="item.id"
                class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
              >
                <div class="flex items-start sm:items-center gap-4">
                  <input
                    type="checkbox"
                    :id="'select-item-' + item.id"
                    v-model="item.selected"
                    class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300 mt-1 sm:mt-0"
                  />
                  <img 
                    :src="item.image" 
                    :alt="item.name"
                    class="w-20 h-20 object-contain rounded-lg border border-gray-200"
                  />
                  <div>
                    <h3 class="font-medium text-gray-900">{{ item.name }}</h3>
                    <p class="text-sm text-gray-500">Код: {{ item.code }}</p>
                    <p v-if="item.description" class="text-sm text-gray-700 mt-1">{{ item.description }}</p>
                  </div>
                </div>
                
                <div class="flex items-center gap-6">
                  <p class="text-lg font-bold whitespace-nowrap text-gray-800">{{ item.price }}</p>
                  <div class="flex items-center border border-gray-300 rounded-lg">
                    <button 
                      @click="decreaseQuantity(item)"
                      class="px-3 py-1 text-gray-600 hover:bg-gray-100 transition-colors"
                      :class="{ 'opacity-50 cursor-not-allowed': item.quantity <= 1 }"
                      :disabled="item.quantity <= 1"
                    >
                      −
                    </button>
                    <span class="px-3 py-1 border-x border-gray-300">{{ item.quantity }}</span>
                    <button 
                      @click="increaseQuantity(item)"
                      class="px-3 py-1 text-gray-600 hover:bg-gray-100 transition-colors"
                    >
                      +
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Форма получателя -->
          <div class="bg-white rounded-xl p-6 shadow-sm">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Укажите данные получателя заказа</h2>
            
            <div class="flex items-center justify-between mb-6">
              <div class="flex space-x-4">
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                  Физлицо
                </button>
                <button class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                  Юрлицо
                </button>
              </div>
              
              <div class="flex items-center text-gray-500">
                <Icon name="heroicons:information-circle" class="mr-1" />
                <span>Все поля обязательны для заполнения</span>
              </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Имя</label>
                <input
                  type="text"
                  id="name"
                  v-model="customerName"
                  class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Ваше имя"
                />
              </div>
              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                <input
                  type="tel"
                  id="phone"
                  class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="+7 (___) ___-__-__"
                />
              </div>
            </div>
          
            <div class="mb-4">
              <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Адрес доставки</label>
              <input
                type="text"
                id="address"
                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Город, улица, дом, квартира"
              />
            </div>
            
            <div class="mb-4">
              <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Комментарий к заказу</label>
              <textarea
                id="comment"
                rows="3"
                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Дополнительная информация"
              ></textarea>
            </div>

            <!-- Блок оплаты -->
            <div class="mt-6 pt-6 border-t border-gray-200">
              <h3 class="text-lg font-bold mb-4 text-gray-800">Способ оплаты</h3>
              
              <div class="flex flex-col space-y-4">
                <label class="flex items-center space-x-3 cursor-pointer">
                  <input type="radio" name="payment" class="h-5 w-5 text-blue-600 focus:ring-blue-500" checked />
                  <span class="text-gray-700">Наличными при получении</span>
                </label>
                
                <label class="flex items-center space-x-3 cursor-pointer">
                  <input type="radio" name="payment" class="h-5 w-5 text-blue-600 focus:ring-blue-500" />
                  <span class="text-gray-700">Банковской картой онлайн</span>
                </label>
                
                <label class="flex items-center space-x-3 cursor-pointer">
                  <input 
                    type="radio" 
                    name="payment" 
                    class="h-5 w-5 text-blue-600 focus:ring-blue-500" 
                    @change="showQrCode = !showQrCode"
                  />
                  <span class="text-gray-700">Оплата по QR-коду</span>
                </label>
              </div>

              <!-- QR-код (показывается при выборе соответствующего варианта) -->
              <div v-if="showQrCode" class="mt-6 p-4 bg-gray-50 rounded-lg flex flex-col items-center">
                <div class="mb-3 text-sm text-gray-600">Отсканируйте QR-код для оплаты</div>
                <div class="w-48 h-48 bg-white p-4 rounded border border-gray-300 flex items-center justify-center">
                  <!-- Здесь будет QR-код -->
                  <div class="text-center text-gray-400">
                    <Icon name="heroicons:qrcode" class="w-24 h-24 mx-auto" />
                    <div class="mt-2 text-xs">QR-код для оплаты</div>
                  </div>
                </div>
                <div class="mt-3 text-sm text-gray-500">
                  Или <a href="#" class="text-blue-600">перейдите по ссылке</a> для оплаты
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="lg:w-1/4">
          <div class="bg-white rounded-xl p-6 shadow-sm sticky top-6 border border-gray-200">
            <h2 class="text-lg font-bold mb-4 text-gray-800">Ваш заказ</h2>
            
            <!-- Добавленные поля -->
            <div class="mb-4 space-y-3">
              <div>
                <p class="text-sm text-gray-500">Дата заказа</p>
                <p class="font-medium">31 марта 2025 г.</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Покупатель</p>
                <p class="font-medium" v-if="customerName">{{ customerName }}</p>
                <p class="text-gray-400 italic" v-else>Не указано</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Способ оплаты</p>
                <p class="font-medium">
                  <span v-if="showQrCode">QR-код</span>
                  <span v-else>Наличные при получении</span>
                </p>
              </div>
            </div>

            <!-- <div class="mb-6 border-t border-gray-200 pt-4">
              <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-gray-600">Товары ({{ items.filter(i => i.selected).length }})</span>
                <span class="font-medium text-gray-800">{{ totalAmount }}</span>
              </div>
            </div> -->
            
          
            <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-bold text-gray-800">
        {{ items.filter(i => i.selected).length }} товар • {{ calculateTotalWeight() }} кг
      </h2>
      <span class="text-sm text-green-600 font-medium">Ваша выгода</span>
    </div>

    <div class="space-y-3 mb-4">
      <!-- Скидки -->
      <div class="flex justify-between items-center">
        <span class="">Скидка на товары</span>
        <span class="font-medium text-gray-800">{{ totalAmount }}</span>
      </div>
      <div class="flex justify-between items-center">
        <span>Ваша выгода</span>
        <span class="text-red-600">-5 358 ₽</span>
      </div>
    </div>

    <div class="border-t border-gray-200 pt-4 mb-4">
      <div class="flex justify-between items-center mb-6">
              <span class="text-lg font-bold text-gray-800">Итого</span>
              <span class="text-2xl font-bold text-gray-800">{{ totalAmount }}</span>
            </div>
    </div>

    <button 
      class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition-colors shadow-md"
      :class="{ 'opacity-50 cursor-not-allowed bg-gray-400': !items.some(i => i.selected) }"
      :disabled="!items.some(i => i.selected)"
    >
      Оформить заказ
    </button>

    <div class="mt-3 text-xs text-gray-500 text-center">
      Нажимая на кнопку, вы соглашаетесь с условиями обработки персональных данных
    </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>