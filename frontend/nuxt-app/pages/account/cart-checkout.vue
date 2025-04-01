<script setup>
import { ref, watch, computed } from 'vue';
import TextInput from '~/components/ui/Inputs/TextInput.vue'
import Button from '~/components/ui/Button/Button.vue';

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

const emptyCartFinalAmount = computed(() => {
  return '0 ₽';
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
  <div class="min-h-screen bg-gray-50 py-4 sm:py-8">
    <div class="container mx-auto px-3 sm:px-4">
      <div class="flex flex-col lg:flex-row gap-4 sm:gap-6">
        <div class="lg:w-3/4">
          <div class="bg-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6">
              <div class="flex items-center mb-3 sm:mb-0">
                <h1 class="text-xl sm:text-2xl font-bold mr-2 sm:mr-3 text-gray-800">Корзина</h1>
                <span class="text-sm sm:text-base text-gray-500">2741-0895-29725</span>
              </div>
              <NuxtLink to="/" class="text-sm sm:text-base text-gray-600 hover:text-gray-800 transition-colors">
                ← Вернуться
              </NuxtLink>
            </div>
            
            <div class="relative w-full sm:w-3/4 md:w-1/2 lg:w-2/5">
              <TextInput
                placeholder="Поиск товара..."
                class="w-full pl-10 text-sm sm:text-base"
              >
                <template #left>
                  <Icon name="heroicons:magnifying-glass" class="text-gray-400" />
                </template>
              </TextInput>
            </div>
          </div>
          
          <div v-if="isEmptyCart" class="bg-white rounded-xl p-6 mb-6 shadow-sm text-center">
            <div class="mx-auto max-w-md">
              <Icon name="heroicons:shopping-bag" class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" />
              <h2 class="mt-3 sm:mt-4 text-lg sm:text-xl font-bold text-gray-800">Ваша корзина пока пуста</h2>
              <p class="mt-2 text-sm sm:text-base text-gray-600">
                Акции и специальные предложения помогут вам определиться с выбором!
              </p>
              <NuxtLink 
                to="/" 
                class="mt-4 sm:mt-6 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-hover"
              >
                Перейти к покупкам
              </NuxtLink>
            </div>
          </div>

          <div v-else class="bg-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 mb-4 sm:mb-6">
              <div class="flex items-center">
                <input
                  type="checkbox"
                  id="select-all"
                  v-model="state.selectAll"
                  @change="toggleSelectAll"
                  class="h-4 w-4 sm:h-5 sm:w-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300"
                />
                <label for="select-all" class="ml-2 text-sm sm:text-base text-gray-700">Выделить все</label>
                <Button 
                  variant="warning" 
                  size="small"
                  @click="removeSelectedItems"
                  :disabled="totalItemsCount === 0"
                  class="ml-3 text-sm"
                >
                  <Icon name="heroicons:trash" class="mr-1" />
                  Удалить
                </Button>
              </div>
              
              <div class="flex items-center space-x-2 sm:space-x-4 overflow-x-auto py-2">
                <Button 
                  variant="transparent" 
                  size="small"
                  class="text-xs sm:text-sm text-gray-500 hover:text-gray-600 whitespace-nowrap"
                >
                  <Icon name="heroicons:share" class="mr-1" />
                  Поделиться
                </Button>
                <Button 
                  variant="transparent" 
                  size="small"
                  class="text-xs sm:text-sm text-gray-500 hover:text-gray-600 whitespace-nowrap"
                >
                  <Icon name="heroicons:arrow-down-tray" class="mr-1" />
                  Скачать
                </Button>
                <Button 
                  variant="transparent" 
                  size="small"
                  class="text-xs sm:text-sm text-gray-500 hover:text-gray-600 whitespace-nowrap"
                >
                  <Icon name="heroicons:printer" class="mr-1" />
                  Печать
                </Button>
              </div>
            </div>
            
            <div class="divide-y divide-gray-200">
              <div 
                v-for="item in state.items" 
                :key="item.id"
                class="py-3 sm:py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4"
              >
                <div class="flex items-start sm:items-center gap-3 sm:gap-4">
                  <input
                    type="checkbox"
                    :id="'select-item-' + item.id"
                    v-model="item.selected"
                    class="h-4 w-4 sm:h-5 sm:w-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300 mt-1 sm:mt-0"
                  />
                  <img 
                    :src="item.image" 
                    :alt="item.name"
                    class="w-16 h-16 sm:w-20 sm:h-20 object-contain rounded-lg border border-gray-200"
                  />
                  <div class="flex-1 min-w-0">
                    <h3 class="text-sm sm:text-base font-medium text-gray-900 truncate">{{ item.name }}</h3>
                    <p class="text-xs sm:text-sm text-gray-500">Код: {{ item.code }}</p>
                    <p v-if="item.description" class="text-xs sm:text-sm text-gray-700 mt-1 line-clamp-2">{{ item.description }}</p>
                  </div>
                </div>
                
                <div class="flex items-center justify-between sm:justify-end gap-3 sm:gap-6 mt-2 sm:mt-0">
                  <p class="text-base sm:text-lg font-bold whitespace-nowrap text-gray-800">{{ formatPrice(item.price) }}</p>
                  <div class="flex items-center border border-gray-300 rounded-lg">
                    <button 
                      @click="decreaseQuantity(item)"
                      class="px-2 sm:px-3 py-1 text-gray-600 hover:bg-gray-100 transition-colors"
                      :class="{ 'opacity-50 cursor-not-allowed': item.quantity <= 1 }"
                      :disabled="item.quantity <= 1"
                    >
                      −
                    </button>
                    <span class="px-2 sm:px-3 py-1 border-x border-gray-300 text-sm sm:text-base">{{ item.quantity }}</span>
                    <button 
                      @click="increaseQuantity(item)"
                      class="px-2 sm:px-3 py-1 text-gray-600 hover:bg-gray-100 transition-colors"
                    >
                      +
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div v-if="!isEmptyCart" class="bg-white rounded-xl p-4 sm:p-6 shadow-sm">
            <h2 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4 text-gray-800">Данные получателя</h2>
            
            <div class="flex items-center mb-4 sm:mb-6">              
              <div class="flex items-center text-xs sm:text-sm text-gray-500">
                <Icon name="heroicons:information-circle" class="mr-1 w-4 h-4" />
                <span>Все поля обязательны</span>
              </div>
            </div>
            
            <div class="grid grid-cols-1 gap-3 sm:gap-4 mb-3 sm:mb-4">
              <TextInput
                id="name"
                label="Имя"
                v-model="state.customer.name"
                placeholder="Ваше имя"
                size="small"
              />
              <TextInput
                id="phone"
                label="Телефон"
                type="tel"
                v-model="state.customer.phone"
                placeholder="+7 (___) ___-__-__"
                size="small"
              />
            </div>

            <div class="mb-3 sm:mb-4">
              <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="state.showSecondForm" class="sr-only peer">
                <div class="relative w-9 h-5 sm:w-11 sm:h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 sm:after:h-5 sm:after:w-5 after:transition-all peer-checked:bg-primary-active"></div>
                <span class="ms-2 text-xs sm:text-sm font-medium text-gray-700">Другой получатель</span>
              </label>
            </div>

            <div v-if="state.showSecondForm" class="grid grid-cols-1 gap-3 sm:gap-4 mb-3 sm:mb-4">
              <TextInput
                id="secondName"
                label="Имя"
                v-model="state.secondCustomer.name"
                placeholder="Имя получателя"
                size="small"
              />
              <TextInput
                id="secondPhone"
                label="Телефон"
                type="tel"
                v-model="state.secondCustomer.phone"
                placeholder="+7 (___) ___-__-__"
                size="small"
              />
            </div>
          
            <div class="mb-3 sm:mb-4">
              <TextInput
                id="address"
                label="Адрес доставки"
                v-model="state.deliveryAddress"
                placeholder="Город, улица, дом, квартира"
                size="small"
              />
            </div>
            
            <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-200">
              <h3 class="text-base sm:text-lg font-bold mb-3 sm:mb-4 text-gray-800">Способ оплаты</h3>
              <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4">
                <label 
                  class="bg-gray-100 p-3 sm:p-4 rounded-lg cursor-pointer hover:bg-gray-200 transition-colors"
                  :class="{ 'ring-2 ring-primary': state.paymentMethod === 'cash' }"
                >
                  <input 
                    type="radio" 
                    v-model="state.paymentMethod" 
                    value="cash" 
                    class="hidden"
                  >
                  <div class="text-sm sm:text-base font-medium">Наличные</div>
                  <p class="text-xs sm:text-sm text-gray-600 mt-1">Оплата курьеру</p>
                </label>
                
                <label 
                  class="bg-gray-100 p-3 sm:p-4 rounded-lg cursor-pointer hover:bg-gray-200 transition-colors"
                  :class="{ 'ring-2 ring-primary': state.paymentMethod === 'card' }"
                >
                  <input 
                    type="radio" 
                    v-model="state.paymentMethod" 
                    value="card" 
                    class="hidden"
                  >
                  <div class="text-sm sm:text-base font-medium">Картой онлайн</div>
                  <p class="text-xs sm:text-sm text-gray-600 mt-1">Оплата на сайте</p>
                </label>
                
                <label 
                  class="bg-gray-100 p-3 sm:p-4 rounded-lg cursor-pointer hover:bg-gray-200 transition-colors"
                  :class="{ 'ring-2 ring-primary': state.paymentMethod === 'qr' }"
                >
                  <input 
                    type="radio" 
                    v-model="state.paymentMethod" 
                    value="qr" 
                    class="hidden"
                  >
                  <div class="text-sm sm:text-base font-medium">QR-код</div>
                  <p class="text-xs sm:text-sm text-gray-600 mt-1">Оплата по QR-коду</p>
                </label>
                
                <label 
                  class="bg-gray-100 p-3 sm:p-4 rounded-lg cursor-pointer hover:bg-gray-200 transition-colors"
                  :class="{ 'ring-2 ring-primary': state.paymentMethod === 'installment' }"
                >
                  <input 
                    type="radio" 
                    v-model="state.paymentMethod" 
                    value="installment" 
                    class="hidden"
                  >
                  <div class="text-sm sm:text-base font-medium">Рассрочка</div>
                  <p class="text-xs sm:text-sm text-gray-600 mt-1">2% на 6 месяцев</p>
                </label>
              </div>
            </div>
          </div>
        </div>
        
        <div class="lg:w-1/4">
          <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm sticky top-4 sm:top-6 border border-gray-200">
            <h2 class="text-base sm:text-lg font-bold mb-3 sm:mb-4 text-gray-800" v-if="!isEmptyCart">Ваш заказ</h2>
            <h2 class="text-base sm:text-lg font-bold mb-3 sm:mb-4 text-gray-800" v-else>Оформление</h2>
            
            <div v-if="isEmptyCart" class="mb-3 sm:mb-4">
              <p class="text-xs sm:text-sm text-gray-600">Выберите товары для оформления заказа</p>
            </div>
            
            <div v-else class="mb-3 sm:mb-4 space-y-2 sm:space-y-3">
              <div>
                <p class="text-xs sm:text-sm text-gray-500">Дата заказа</p>
                <p class="text-sm sm:text-base font-medium">{{ orderDate }}</p>
              </div>
              <div>
                <p class="text-xs sm:text-sm text-gray-500">Покупатель</p>
                <p class="text-sm sm:text-base font-medium" v-if="state.customer.name">{{ state.customer.name }}</p>
                <p class="text-xs sm:text-sm text-gray-400 italic" v-else>Не указано</p>
              </div>
              <div>
                <p class="text-xs sm:text-sm text-gray-500">Способ оплаты</p>
                <p class="text-sm sm:text-base font-medium">
                  <span v-if="state.paymentMethod === 'qr'">QR-код</span>
                  <span v-else-if="state.paymentMethod === 'cash'">Наличные</span>
                  <span v-else-if="state.paymentMethod === 'card'">Картой</span>
                  <span v-else-if="state.paymentMethod === 'installment'">Рассрочка</span>
                </p>
              </div>
            </div>

            <div v-if="!isEmptyCart" class="flex items-center justify-between mb-3 sm:mb-4">
              <h2 class="text-sm sm:text-base font-bold text-gray-800">
                {{ totalItemsCount }} товар • {{ totalWeight }} кг
              </h2>
              <span class="text-xs sm:text-sm text-green-600 font-medium">Выгода</span>
            </div>

            <div v-if="!isEmptyCart" class="space-y-2 sm:space-y-3 mb-3 sm:mb-4">
              <div class="flex justify-between items-center text-sm sm:text-base">
                <span>Сумма</span>
                <span class="font-medium text-gray-800">{{ formattedTotalAmount }}</span>
              </div>
              <div class="flex justify-between items-center text-sm sm:text-base">
                <span>Скидка</span>
                <span class="text-red-600">{{ formattedDiscountAmount }}</span>
              </div>
            </div>

            <div class="border-t border-gray-200 pt-3 sm:pt-4 mb-3 sm:mb-4">
              <div class="flex justify-between items-center mb-4 sm:mb-6">
                <span class="text-base sm:text-lg font-bold text-gray-800">Итого</span>
                <span class="text-xl sm:text-2xl font-bold text-gray-800">
                  {{ isEmptyCart ? emptyCartFinalAmount : formattedFinalAmount }}
                </span>
              </div>
            </div>

            <Button 
              variant="primary" 
              size="medium"
              :disabled="isEmptyCart || totalItemsCount === 0"
              class="w-full shadow-md text-sm sm:text-base"
              type="submit"
            >
              Оформить заказ
            </Button>

            <div class="mt-2 text-xxs sm:text-xs text-gray-500 text-center">
              Нажимая на кнопку, вы соглашаетесь с условиями обработки персональных данных
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
