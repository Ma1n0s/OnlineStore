<script setup>
import Button from '~/components/ui/Button/Button.vue'
import { useCartStore } from '~/stores/cart'
import { useUserStore } from '~/stores/user'

const {
  public: { backendUrl },
} = useRuntimeConfig()

const userStore = useUserStore()
const cartStore = useCartStore()
const { cart, products, isLoading } = storeToRefs(cartStore)
const isPhone = ref(false)
const isSuccess = ref(false)

const sendOrder = useDebounceFn(async () => {
  if (cart.value?.user?.phone && cart.value?.user?.phone.length === 18) {
    try {
      await cartStore.createOrder().then(() => {
        isSuccess.value = true
        setTimeout(() => {
          isSuccess.value = false
        }, 5000)
      })
    } catch (e) {
      console.log(e)
    }
  } else {
    isPhone.value = true
  }

  console.log(cart.value?.user)
}, 1000)

const isEmpty = computed(() => products.value.length === 0)
const isSelected = computed(() => products.value.some(product => product.pivot.selected))
const isNotEnough = computed(() =>
  products.value.filter(product => product.type === 'instock').some(product => product.quantity < product.orderQuantity)
)

const weight = computed(() => {
  const result = products.value.reduce(
    (acc, val) => (val.pivot.selected ? acc + val?.weight * val?.orderQuantity : acc + 0),
    0
  )
  return result ? result : 0
})

const sum = computed(() => {
  const result = products.value.reduce(
    (acc, val) => (val.pivot.selected ? acc + val?.price * val?.orderQuantity : acc + 0),
    0
  )
  return result
})

const bonus = computed(() => {
  console.log(cart.value && cart.value?.user && cart.value?.user?.bonus_balance, 'test')
  if (cart.value && cart.value?.user && cart.value?.user?.bonus_balance) {
    return cart.value?.user?.bonus_balance > sum.value ? sum.value : cart.value?.user?.bonus_balance
  }
  return 0
})

const checkBonus = ref(!!cart.value?.checkBonus)
const total = computed(() => (checkBonus.value ? sum.value - bonus.value : sum.value))

const formatDateTime = datatime => {
  const date = new Date(datatime)
  return new Intl.DateTimeFormat('ru-RU', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
  })
    .format(date)
    .replace(',', '')
}

console.log(isEmpty.value, isSelected.value)

const handlePhoneInput = e => {
  const input = e.target
  // Оставляем только цифры и удаляем первый символ, если это 7 или 8
  let digits = input.value.replace(/\D/g, '')

  // Удаляем лишние цифры (максимум 10 цифр после +7)
  digits = digits.substring(0, 11)

  // Если номер начинается с 7 или 8, используем оставшиеся цифры
  if (digits.startsWith('7') || digits.startsWith('8')) {
    digits = digits.substring(1)
  }

  // Собираем номер в формате +7 (XXX) XXX-XX-XX
  let formattedValue = '+7'

  if (digits.length > 0) {
    formattedValue += ` (${digits.substring(0, 3)}`

    if (digits.length > 3) {
      formattedValue += `) ${digits.substring(3, 6)}`
    }
    if (digits.length > 6) {
      formattedValue += `-${digits.substring(6, 8)}`
    }
    if (digits.length > 8) {
      formattedValue += `-${digits.substring(8, 10)}`
    }
  }

  // Обновляем значение
  cart.value.user.phone = formattedValue

  // Перемещаем курсор в конец
  requestAnimationFrame(() => {
    input.setSelectionRange(formattedValue.length, formattedValue.length)
  })
}

const UpdatePhone = async user => {
  try {
    const response = await $fetch(`${backendUrl}/api/profile`, {
      method: 'PUT',
      body: {
        name: user.name,
        email: user.email,
        phone: user.phone,
      },
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
      credentials: 'include',
    })

    userStore.setUser(response.user)
  } catch (error) {
    console.log(error)
  } finally {
    await cartStore.refetchCart()
    isPhone.value = false
  }
}
</script>
<template>
  <Modal
    :isOpen="isPhone"
    @close="
      () => {
        isPhone = false
        cart.user.phone = ''
      }
    "
  >
    <div class="flex flex-col gap-4">
      <span class="text-lg text-center">Укажите номер телефона</span>
      <input
        type="tel"
        id="phone"
        v-model="cart.user.phone"
        @input="handlePhoneInput"
        placeholder="+7 (___) ___-__-__"
        class="w-full px-4 py-2 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 transition"
      />

      <Button class="w-full" @click="UpdatePhone(cart.user)">Сохранить</Button>
    </div>
  </Modal>

  <Modal
    class="!p-4 md:p-6 !w-72 md:!w-96 !h-40 rounded-lg"
    :isOpen="isSuccess"
    @close="
      () => {
        isSuccess = false
      }
    "
  >
    <div class="flex flex-col gap-4 items-center justify-center w-full overflow-hidden">
      <p class="text-lg md:text-xl font-bold text-nowrap">Заказ создан</p>
      <p class="text-lg md:text-xl font-bold text-nowrap">Мы свяжимся с вами</p>
    </div>
  </Modal>

  <div class="bg-white rounded-xl p-4 shadow-2xl sm:p-6 sticky top-8 sm:top-20">
    <h2 class="text-base sm:text-lg font-bold mb-3 sm:mb-4 text-gray-800" v-if="isEmpty">Ваш заказ</h2>
    <h2 class="text-base sm:text-lg font-bold mb-3 sm:mb-4 text-gray-800" v-else>Оформление</h2>

    <div v-if="isEmpty" class="mb-3 sm:mb-4">
      <p class="text-xs sm:text-sm text-gray-600">Выберите товары для оформления заказа</p>
    </div>

    <div v-else class="mb-3 sm:mb-4 space-y-2 sm:space-y-3">
      <div>
        <p class="text-xs sm:text-sm text-gray-500">Дата заказа</p>
        <p class="text-sm sm:text-base font-medium">{{ formatDateTime(cart.updated_at) }}</p>
      </div>
      <div>
        <p class="text-xs sm:text-sm text-gray-500">Покупатель</p>
        <p class="text-sm sm:text-base font-medium" v-if="cart.user">{{ cart.user.name }}</p>
        <p class="text-xs sm:text-sm text-gray-400 italic" v-else>Не указано</p>
      </div>

      <div>
        <p class="text-sm sm:text-base font-medium">Доступно бонусов: {{ bonus }} ₽</p>
      </div>
    </div>

    <div v-if="!isEmpty && isSelected" class="flex items-center justify-between mb-3 sm:mb-4">
      <h2 class="text-sm sm:text-base font-bold text-gray-800">
        {{ products.reduce((acc, val) => (val.pivot.selected ? acc + 1 : acc + 0), 0) }} товар
        <span v-if="weight">• {{ weight }} кг</span>
      </h2>
    </div>

    <div v-if="!isEmpty && isSelected" class="space-y-2 sm:space-y-3 mb-3 sm:mb-4">
      <div class="flex justify-between items-center text-sm sm:text-base">
        <span>Сумма</span>
        <span class="font-medium text-gray-800">{{ sum }} ₽</span>
      </div>

      <div class="flex justify-between items-center text-sm sm:text-base" v-if="!!bonus">
        <span>
          <input type="checkbox" v-model="checkBonus" @change="e => cartStore.updateOrderBonus(e.target.checked)" />
          Бонусы
        </span>
        <span class="font-medium text-gray-800">{{ bonus }} ₽</span>
      </div>
    </div>

    <div class="border-t border-gray-200 pt-3 sm:pt-4 mb-3 sm:mb-4">
      <div class="flex justify-between items-center mb-4 sm:mb-6">
        <span class="text-base sm:text-lg font-bold text-gray-800">Итого</span>
        <span class="text-xl sm:text-2xl font-bold text-gray-800"> {{ isEmpty ? 0 : total }} ₽ </span>
      </div>
    </div>

    <Button
      variant="primary"
      size="medium"
      :disabled="isEmpty || !isSelected || isNotEnough || isLoading"
      class="w-full shadow-md text-sm sm:text-base"
      type="submit"
      @click="sendOrder"
    >
      Оформить заказ
    </Button>

    <div class="mt-2 text-xxs sm:text-xs text-gray-500 text-center">
      Нажимая на кнопку, вы соглашаетесь с условиями обработки персональных данных
    </div>
  </div>
</template>
