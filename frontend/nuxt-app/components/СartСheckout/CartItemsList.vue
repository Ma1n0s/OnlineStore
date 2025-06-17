<script setup>
import Button from '~/components/ui/Button/Button.vue'
import { Icon } from '#components'

const cartStore = useCartStore()
const { cart, products, isLoading } = storeToRefs(cartStore)

const debouncedUpdate = useDebounceList(
  async product => {
    await cartStore.updateProduct(product)
  },
  1000,
  cartStore.refetchCart
)

const debounceSelectedAll = useDebounceFn(async event => {
  await cartStore.setSelected(event.target.checked)
}, 300)

const removeSelected = async () => {
  await cartStore.removeSelected()
}

const removeProduct = async product => {
  await cartStore.remove(product)
}

const toggleSelected = item => {
  item.selected = !item.selected
  debouncedUpdate(item.id, item)
}

const increaseQuantity = item => {
  if (item.quantity > item.orderQuantity && item.type === 'instock') {
    isLoading.value = true
    item.orderQuantity += 1
    debouncedUpdate(item.id, item)
  }

  if (item.type !== 'instock') {
    isLoading.value = true
    item.orderQuantity += 1
    debouncedUpdate(item.id, item)
  }
}

const decreaseQuantity = item => {
  if (item.orderQuantity !== 1 && item.type === 'instock') {
    isLoading.value = true
    item.orderQuantity -= 1

    if (item.quantity < item.orderQuantity) item.orderQuantity = item.quantity

    debouncedUpdate(item.id, item)
  }

  if (item.orderQuantity !== 1 && item.type !== 'instock') {
    item.orderQuantity -= 1
    debouncedUpdate(item.id, item)
  }
}

const printToExcel = () => {
  if (products.value.length === 0) {
    alert('Корзина пуста, нечего экспортировать')
    return
  }

  const selectedProducts = products.value.filter(item => item.selected)
  const dataToExport = selectedProducts.length > 0 ? selectedProducts : products.value

  const data = dataToExport.map(item => ({
    Наименование: item.name,
    'Код товара': item.code,
    'Цена за шт. (руб)': item.price,
    Количество: item.orderQuantity,
    'Сумма (руб)': item.price * item.orderQuantity,
  }))

  // Создаем рабочую книгу Excel
  const workbook = utils.book_new()
  const worksheet = utils.json_to_sheet(data)

  // Добавляем форматирование (ширину колонок)
  worksheet['!cols'] = [
    { wch: 30 }, // Наименование
    { wch: 15 }, // Артикул
    { wch: 15 }, // Цена
    { wch: 12 }, // Количество
    { wch: 15 }, // Сумма
    { wch: 15 }, // Остаток
    { wch: 15 }, // Старая цена
  ]

  utils.book_append_sheet(workbook, worksheet, 'Корзина')

  // Генерируем имя файла
  const now = new Date()
  const dateStr = now.toISOString().slice(0, 10)
  const timeStr = now.toTimeString().slice(0, 8).replace(/:/g, '-')
  const fileName = `Корзина_${dateStr}_${timeStr}.xlsx`

  // Сохраняем файл
  writeFileXLSX(workbook, fileName)
}
</script>

<template>
  <div class="bg-white rounded-xl p-5 shadow-2xl">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div class="flex items-center">
        <input
          @change="debounceSelectedAll"
          :checked="cart.selected"
          type="checkbox"
          id="select-all"
          class="h-5 w-5 rounded border-gray-300 text-primary focus:ring-primary"
        />
        <label for="select-all" class="ml-2 text-sm font-medium text-gray-700">Выбрать все</label>
        <Button
          variant="danger"
          size="small"
          class="ml-3"
          @click="removeSelected"
          :disabled="!products.some(p => p.selected)"
        >
          <Icon name="heroicons:trash" class="w-4 h-4 mr-1.5" />
          Удалить выбранные
        </Button>
      </div>

      <div class="flex items-center space-x-3">
        <!-- <Button
          variant="ghost"
          size="small"
          class="text-gray-500 hover:text-gray-700"
        >
          <Icon name="heroicons:arrow-down-tray" class="w-4 h-4 mr-1.5" />
          Скачать список
        </Button> -->
        <Button variant="ghost" size="small" class="text-gray-500 hover:text-gray-700" @click="printToExcel">
          <Icon name="heroicons:printer" class="w-4 h-4 mr-1.5" />
          Скачать список (Excel)
        </Button>
      </div>
    </div>

    <div>
      <template v-if="products.length > 0">
        <div
          v-for="item in products"
          :key="'cart-item-' + item.id"
          class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
        >
          <div class="flex items-start sm:items-center gap-4 w-full sm:w-auto">
            <input
              type="checkbox"
              :id="'select-item-' + item.id"
              :checked="item.selected"
              @change="toggleSelected(item)"
              class="h-5 w-5 rounded border-gray-300 text-primary focus:ring-primary mt-0.5 sm:mt-0"
            />
            <Button
              variant="warning"
              size="small"
              class="text-sm !px-4 !h-fit max-h-full self-center"
              @click="removeProduct(item)"
            >
              <Icon name="material-symbols:close-rounded" class="w-6 h-6" />
            </Button>
            <NuxtLink :to="`/products/${item.slug}`" class="flex items-start sm:items-center gap-4 flex-1 min-w-0">
              <div class="relative flex-shrink-0">
                <NuxtImg
                  :src="item.main_image"
                  :alt="item.name"
                  width="96"
                  height="96"
                  format="webp"
                  loading="lazy"
                  decoding="async"
                  class="w-20 h-20 object-contain rounded-lg bg-gray-50 border border-gray-200"
                />
                <!-- <span v-if="item.quantity <= 5" class="absolute -top-2 -right-2 bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded-full">
                  Осталось {{ item.quantity }}
                </span> -->
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="text-base font-medium text-gray-900 truncate">{{ item.name }}</h3>
                <p class="text-sm text-gray-500 mt-1">Код: {{ item.code }}</p>
                <p v-if="item.price < item.original_price" class="text-sm text-gray-500 mt-1">
                  <span class="line-through mr-2">{{ item.original_price }} ₽</span>
                  <span class="text-red-600 font-medium">{{ item.price }} ₽</span>
                </p>
              </div>
            </NuxtLink>
          </div>

          <div
            class="sm:w-64"
            :class="
              item.quantity < item.orderQuantity && item.type === 'instock' && '!border-primary border-2 rounded-lg p-2'
            "
          >
            <div class="flex items-center justify-between sm:justify-end gap-4">
              <p class="text-lg font-bold text-gray-900 whitespace-nowrap">
                {{ (item.price * item.orderQuantity).toLocaleString('ru-RU') }} ₽
              </p>
              <div class="flex items-center border border-gray-300 rounded-lg bg-white">
                <button
                  @click.prevent="decreaseQuantity(item)"
                  :disabled="item.orderQuantity <= 1 && item.type == 'instock' && item.orderQuantity !== 1"
                  class="px-3 py-1 text-gray-600 hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <Icon name="heroicons:minus" class="w-4 h-4" />
                </button>
                <span class="px-3 py-1 border-x border-gray-300 text-sm sm:text-base font-medium">
                  {{ item.orderQuantity }}
                </span>
                <button
                  @click.prevent="increaseQuantity(item)"
                  :disabled="item.orderQuantity >= item.quantity && item.type == 'instock'"
                  class="px-3 py-1 text-gray-600 hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <Icon name="heroicons:plus" class="w-4 h-4" />
                </button>
              </div>
            </div>

            <div v-if="item.quantity < item.orderQuantity && item.type === 'instock'" class="text-lg text-primary">
              Недостаточно товара
            </div>
          </div>
        </div>
      </template>

      <div v-else class="py-8 text-center text-gray-500">В корзине нет товаров</div>
    </div>
  </div>
</template>
