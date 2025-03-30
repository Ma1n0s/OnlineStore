<script setup>
import { ref, watch } from 'vue';

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

watch(
  () => items.value.every((item) => item.selected),
  (allSelected) => {
    selectAll.value = allSelected;
  }
);
</script>

<template>
  <div class="min-h-screen bg-gray-100 py-8">
    <div class="container mx-auto px-4">
      <div class="flex flex-col lg:flex-row gap-6">
        <div class="lg:w-3/4">
          <div class="bg-white rounded-xl p-6 mb-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
              <div class="flex items-center mb-4 sm:mb-0">
                <h1 class="text-2xl font-bold mr-3">Корзина</h1>
                <span class="text-gray-500">2741-0895-29725</span>
              </div>
              <NuxtLink to="/" class="text-gray hover:text-gray transition-colors">
                ← Вернуться к покупкам
              </NuxtLink>
            </div>
            
            <div class="relative">
              <input
                type="text"
                placeholder="Быстрое добавление: введите наименование или код товара..."
                class="w-full p-3 pl-10 border border-gray-300 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray focus:border-transparent"
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
                  class="h-5 w-5 text-gray rounded focus:ring-gray border-gray-300"
                />
                <label for="select-all" class="ml-2 text-gray-700">Выделить все</label>
                <button 
                  @click="removeSelectedItems"
                  class="ml-4 flex items-center text-gray-500 hover:text-red-500 transition-colors"
                >
                  <Icon name="heroicons:trash" class="mr-1" />
                  Удалить выбранные
                </button>
              </div>
              
              <div class="flex items-center space-x-4 text-gray-500">
                <button class="flex items-center hover:text-gray transition-colors">
                  <Icon name="heroicons:share" class="mr-1" />
                  Поделиться
                </button>
                <button class="flex items-center hover:text-gray transition-colors">
                  <Icon name="heroicons:arrow-down-tray" class="mr-1" />
                  Скачать
                </button>
                <button class="flex items-center hover:text-gray transition-colors">
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
                    class="h-5 w-5 text-blue-600 rounded focus:ring-gray border-gray-300 mt-1 sm:mt-0"
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
                  <p class="text-lg font-bold whitespace-nowrap">{{ item.price }}</p>
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
            <h2 class="text-xl font-bold mb-4">Укажите данные получателя заказа</h2>
            
            <div class="flex items-center justify-between mb-6">
              <div class="flex space-x-4">
                <button class="px-4 py-2 bg-blue-100 text-gray rounded-lg font-medium">
                  Физлицо
                </button>
                <button class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
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
                  class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray focus:border-transparent"
                  placeholder="Ваше имя"
                />
              </div>
              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                <input
                  type="tel"
                  id="phone"
                  class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray focus:border-transparent"
                  placeholder="+7 (___) ___-__-__"
                />
              </div>
            </div>
            
            <div class="mb-4">
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input
                type="email"
                id="email"
                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray focus:border-transparent"
                placeholder="your@email.com"
              />
            </div>
            
            <div class="mb-4">
              <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Адрес доставки</label>
              <input
                type="text"
                id="address"
                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray focus:border-transparent"
                placeholder="Город, улица, дом, квартира"
              />
            </div>
            
            <div class="mb-4">
              <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Комментарий к заказу</label>
              <textarea
                id="comment"
                rows="3"
                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray focus:border-transparent"
                placeholder="Дополнительная информация"
              ></textarea>
            </div>
          </div>
        </div>
        
        <div class="lg:w-1/4">
          <div class="bg-white rounded-xl p-6 shadow-sm sticky top-6">
            <h2 class="text-lg font-bold mb-4">Ваш заказ</h2>
            
            <div class="mb-6">
              <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-gray-600">Товары ({{ items.filter(i => i.selected).length }})</span>
                <span class="font-medium">{{ totalAmount }}</span>
              </div>
              <div class="flex justify-between py-2 ">
                <span class="text-gray-600">Скидка</span>
                <span class="text-green-600">0 ₽</span>
              </div>
            </div>
            
            <div class="flex justify-between items-center mb-6">
              <span class="text-lg font-bold">Итого</span>
              <span class="text-2xl font-bold">{{ totalAmount }}</span>
            </div>
            
            <button 
              class="w-full bg-gray hover:bg-gray text-white py-3 px-4 rounded-lg font-medium transition-colors"
              :class="{ 'opacity-50 cursor-not-allowed': !items.some(i => i.selected) }"
              :disabled="!items.some(i => i.selected)"
            >
              Оформить заказ
            </button>
            
            <div class="mt-4 text-sm text-gray-500">
              Нажимая на кнопку, вы соглашаетесь с условиями обработки персональных данных и условиями продажи
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
