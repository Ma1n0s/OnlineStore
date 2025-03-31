<script setup>
import { ref, watch, computed } from 'vue';

const state = ref({
  showSecondForm: false,
  secondCustomer: {
    name: '',
    phone: ''
  },
  selectAll: false,
  items: [
  {
      id: 1,
      name: 'WMSITE10',
      code: '16313057',
      description: 'Бесщеточная аккумуляторная дрель-шуруповерт AEG BS18SBL-202C 4935472277',
      price: 25790,
      quantity: 1,
      selected: false,
      image: '/placeholder-product.jpg'
    },
    {
      id: 2,
      name: 'WMSITE11 товар',
      code: '12345678',
      description: 'Компактный перфоратор с мощным двигателем',
      price: 15000,
      quantity: 2,
      selected: false,
      image: '/placeholder-product.jpg'
    },

  ], 
  customer: {
    name: 'Иван Иванович Иванов',
    phone: '+7 922 555 99-00'
  },
  deliveryAddress: '',
  paymentMethod: 'cash',
  showQrCode: false
});

const selectedItems = computed(() => state.value.items.filter(item => item.selected));
const totalItemsCount = computed(() => selectedItems.value.length);
const totalWeight = computed(() => '4,8');
const isEmptyCart = computed(() => state.value.items.length === 0);

const totalAmount = computed(() => {
  return selectedItems.value.reduce((sum, item) => {
    return sum + (item.price * item.quantity);
  }, 0);
});

const formattedTotalAmount = computed(() => {
  return totalAmount.value.toLocaleString('ru-RU') + ' ₽';
});

const discountAmount = computed(() => {
  return Math.round(totalAmount.value * 0.1);
});

const finalAmount = computed(() => {
  return totalAmount.value - discountAmount.value;
});

const formattedFinalAmount = computed(() => {
  return finalAmount.value.toLocaleString('ru-RU') + ' ₽';
});

const formattedDiscountAmount = computed(() => {
  return '-' + discountAmount.value.toLocaleString('ru-RU') + ' ₽';
});

const increaseQuantity = (item) => {
  item.quantity += 1;
};

const decreaseQuantity = (item) => {
  if (item.quantity > 1) {
    item.quantity -= 1;
  }
};

const toggleSelectAll = () => {
  state.value.items.forEach((item) => {
    item.selected = state.value.selectAll;
  });
};

const removeSelectedItems = () => {
  state.value.items = state.value.items.filter(item => !item.selected);
};

const formatPrice = (price) => {
  return price.toLocaleString('ru-RU') + ' ₽';
};

watch(
  () => state.value.items.every((item) => item.selected),
  (allSelected) => {
    state.value.selectAll = allSelected;
  }
);

watch(selectedItems, (newVal) => {
  if (newVal.length === 0) {
    state.value.selectAll = false;
  }
}, { deep: true });

const orderDate = computed(() => {
  const today = new Date();
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return today.toLocaleDateString('ru-RU', options);
});
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
          
          <div v-if="isEmptyCart" class="bg-white rounded-xl p-8 mb-6 shadow-sm text-center">
            <div class="mx-auto max-w-md">
              <Icon name="heroicons:shopping-bag" class="mx-auto h-12 w-12 text-gray-400" />
              <h2 class="mt-4 text-xl font-bold text-gray-800">Ваша корзина пока пуста</h2>
              <p class="mt-2 text-gray-600">
                Акции, специальные предложения и обзоры самых интересных товаров на главной странице помогут вам определиться с выбором!
              </p>
              <NuxtLink 
                to="/" 
                class="mt-6 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Перейти к покупкам
              </NuxtLink>
            </div>
          </div>

          <div v-else class="bg-white rounded-xl p-6 mb-6 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
              <div class="flex items-center">
                <input
                  type="checkbox"
                  id="select-all"
                  v-model="state.selectAll"
                  @change="toggleSelectAll"
                  class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300"
                />
                <label for="select-all" class="ml-2 text-gray-700">Выделить все</label>
                <button 
                  @click="removeSelectedItems"
                  :disabled="totalItemsCount === 0"
                  class="ml-4 flex items-center text-gray-500 hover:text-red-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
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
                v-for="item in state.items" 
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
                  <p class="text-lg font-bold whitespace-nowrap text-gray-800">{{ formatPrice(item.price) }}</p>
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
          
          <div v-if="!isEmptyCart" class="bg-white rounded-xl p-6 shadow-sm">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Укажите данные получателя заказа</h2>
            
            <div class="flex items-center justify-between mb-6">              
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
                  v-model="state.customer.name"
                  class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Ваше имя"
                />
              </div>
              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                <input
                  type="tel"
                  id="phone"
                  v-model="state.customer.phone"
                  class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="+7 (___) ___-__-__"
                />
              </div>
            </div>

            <div class="mb-4">
              <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="state.showSecondForm" class="sr-only peer">
                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                <span class="ms-3 text-sm font-medium text-gray-700">Заберет другой человек</span>
              </label>
            </div>

            <div v-if="state.showSecondForm" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <label for="secondName" class="block text-sm font-medium text-gray-700 mb-1">Имя</label>
                <input
                  type="text"
                  id="secondName"
                  v-model="state.secondCustomer.name"
                  class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Имя получателя"
                />
              </div>
              <div>
                <label for="secondPhone" class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                <input
                  type="tel"
                  id="secondPhone"
                  v-model="state.secondCustomer.phone"
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
                v-model="state.deliveryAddress"
                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Город, улица, дом, квартира"
              />
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-200">
              <h3 class="text-lg font-bold mb-4 text-gray-800">Способ оплаты</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label 
                  class="bg-gray-100 p-4 rounded-lg cursor-pointer hover:bg-gray-200 transition-colors"
                  :class="{ 'ring-2 ring-blue-500': state.paymentMethod === 'cash' }"
                >
                  <input 
                    type="radio" 
                    v-model="state.paymentMethod" 
                    value="cash" 
                    class="hidden"
                  >
                  <div class="font-medium">Наличные при получении</div>
                  <p class="text-sm text-gray-600 mt-1">Оплата наличными курьеру</p>
                </label>
                
                <label 
                  class="bg-gray-100 p-4 rounded-lg cursor-pointer hover:bg-gray-200 transition-colors"
                  :class="{ 'ring-2 ring-blue-500': state.paymentMethod === 'card' }"
                >
                  <input 
                    type="radio" 
                    v-model="state.paymentMethod" 
                    value="card" 
                    class="hidden"
                  >
                  <div class="font-medium">Картой онлайн</div>
                  <p class="text-sm text-gray-600 mt-1">Оплата картой на сайте</p>
                </label>
                
                <label 
                  class="bg-gray-100 p-4 rounded-lg cursor-pointer hover:bg-gray-200 transition-colors"
                  :class="{ 'ring-2 ring-blue-500': state.paymentMethod === 'qr' }"
                >
                  <input 
                    type="radio" 
                    v-model="state.paymentMethod" 
                    value="qr" 
                    class="hidden"
                  >
                  <div class="font-medium">QR-код</div>
                  <p class="text-sm text-gray-600 mt-1">Оплата по QR-коду</p>
                </label>
                
                <label 
                  class="bg-gray-100 p-4 rounded-lg cursor-pointer hover:bg-gray-200 transition-colors"
                  :class="{ 'ring-2 ring-blue-500': state.paymentMethod === 'installment' }"
                >
                  <input 
                    type="radio" 
                    v-model="state.paymentMethod" 
                    value="installment" 
                    class="hidden"
                  >
                  <div class="font-medium">Рассрочка</div>
                  <p class="text-sm text-gray-600 mt-1">2% рассрочка на 6 месяцев</p>
                </label>
              </div>
            </div>
          </div>
        </div>
        
        <div v-if="!isEmptyCart" class="lg:w-1/4">
          <div class="bg-white rounded-xl p-6 shadow-sm sticky top-6 border border-gray-200">
            <h2 class="text-lg font-bold mb-4 text-gray-800">Ваш заказ</h2>
            
            <div class="mb-4 space-y-3">
              <div>
                <p class="text-sm text-gray-500">Дата заказа</p>
                <p class="font-medium">{{ orderDate }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Покупатель</p>
                <p class="font-medium" v-if="state.customer.name">{{ state.customer.name }}</p>
                <p class="text-gray-400 italic" v-else>Не указано</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Способ оплаты</p>
                <p class="font-medium">
                  <span v-if="state.paymentMethod === 'qr'">QR-код</span>
                  <span v-else-if="state.paymentMethod === 'cash'">Наличные при получении</span>
                  <span v-else-if="state.paymentMethod === 'card'">Картой онлайн</span>
                  <span v-else-if="state.paymentMethod === 'installment'">Рассрочка</span>
                </p>
              </div>
            </div>

            <div class="flex items-center justify-between mb-4">
              <h2 class="text-lg font-bold text-gray-800">
                {{ totalItemsCount }} товар • {{ totalWeight }} кг
              </h2>
              <span class="text-sm text-green-600 font-medium">Ваша выгода</span>
            </div>

            <div class="space-y-3 mb-4">
              <div class="flex justify-between items-center">
                <span>Сумма заказа</span>
                <span class="font-medium text-gray-800">{{ formattedTotalAmount }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span>Скидка</span>
                <span class="text-red-600">{{ formattedDiscountAmount }}</span>
              </div>
            </div>

            <div class="border-t border-gray-200 pt-4 mb-4">
              <div class="flex justify-between items-center mb-6">
                <span class="text-lg font-bold text-gray-800">Итого к оплате</span>
                <span class="text-2xl font-bold text-gray-800">{{ formattedFinalAmount }}</span>
              </div>
            </div>

            <button 
              class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition-colors shadow-md"
              :class="{ 'opacity-50 cursor-not-allowed bg-gray-400': totalItemsCount === 0 }"
              :disabled="totalItemsCount === 0"
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