<script setup>
import Button from '~/components/ui/Button/Button.vue'

const cartStore = useCartStore()
const { cart, products } = storeToRefs(cartStore)

const removeSelected = () => {}
const toggleSelected = item => {
  item.selected = !item.selected
  console.log(item.selected)
  // cartStore.updateProduct(item)
}
</script>
<template>
  <div class="bg-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 mb-4 sm:mb-6">
      <div class="flex items-center">
        <input
          @change="async event => await cartStore.setSelected(event.target.checked)"
          :checked="cart.selected"
          type="checkbox"
          id="select-all"
          class="h-4 w-4 sm:h-5 sm:w-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300"
        />
        <label for="select-all" class="ml-2 text-sm sm:text-base text-gray-700">Выделить все</label>
        <Button variant="warning" size="small" class="ml-3 text-sm" @click="removeSelected">
          <img src="" alt="" class="" />
          Удалить
        </Button>
      </div>

      <div class="flex items-center space-x-2 sm:space-x-4 overflow-x-auto py-2">
        <Button
          variant="transparent"
          size="small"
          class="text-xs sm:text-sm text-gray-500 hover:text-gray-600 whitespace-nowrap"
        >
          <img src="" alt="" class="" />
          Скачать
        </Button>
        <Button
          variant="transparent"
          size="small"
          class="text-xs sm:text-sm text-gray-500 hover:text-gray-600 whitespace-nowrap"
        >
          Печать
        </Button>
      </div>
    </div>

    <div class="divide-y divide-gray-200">
      <div v-if="products">
        <div
          v-for="item in products"
          :key="'rented-' + item.id"
          class="py-3 sm:py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4"
        >
          <div class="flex items-start sm:items-center gap-3 sm:gap-4">
            <input
              type="checkbox"
              :id="'select-rented-item-' + item.id"
              :checked="item.selected"
              @change="toggleSelected(item)"
              class="h-4 w-4 sm:h-5 sm:w-5 text-primary rounded focus:ring-primary-active border-gray-300 mt-1 sm:mt-0"
            />
            <NuxtImg
              :src="item.main_image"
              :alt="item.name"
              width="80"
              height="80"
              format="webp"
              loading="lazy"
              decoding="async"
              class="w-16 h-16 sm:w-20 sm:h-20 object-contain rounded-lg"
              sizes="(max-width: 640px) 64px, 80px"
            />
            <div class="flex-1 min-w-0">
              <h3 class="text-sm sm:text-base font-medium text-gray-900 truncate">{{ item.name }}</h3>
              <p class="text-xs sm:text-sm text-gray-500">Код: {{ item.code }}</p>
              <!-- <div class="flex items-center gap-2 mt-1">
                <span class="text-xs sm:text-sm bg-gray text-white px-2 py-1 rounded">
                  Аренда: {{ item.rentalDays }} {{ dayWord(item.rentalDays) }}
                </span>
                <div class="flex items-center rounded-lg overflow-hidden shadow-sm">
                  <button
                    @click="decreaseRentalDays(item)"
                    class="flex items-center justify-center px-3 py-1 bg-primary text-white hover:bg-primary-hover transition-colors border-r border-blue-200"
                    :class="{ 'opacity-50 cursor-not-allowed': item.rentalDays <= 1 }"
                    :disabled="item.rentalDays <= 1"
                  >
                    <Icon name="material-symbols:check-indeterminate-small-rounded" class="w-6 h-6" />
                  </button>
                  <button
                    @click="increaseRentalDays(item)"
                    class="flex items-center justify-center px-3 py-1 bg-primary text-white hover:bg-primary-hover transition-colors"
                  >
                    <Icon name="material-symbols:add-rounded" class="w-6 h-6" />
                  </button>
                </div>
              </div> -->
            </div>
          </div>

          <div class="flex items-center justify-between sm:justify-end gap-3 sm:gap-6 mt-2 sm:mt-0">
            <p class="text-base sm:text-lg font-bold whitespace-nowrap text-gray-800">
              {{ item.price }}
            </p>
            <div class="flex items-center border border-gray-300 rounded-lg">
              <button
                class="px-2 sm:px-3 py-1 text-gray-600 hover:bg-gray-100 transition-colors"
                :class="{ 'opacity-50 cursor-not-allowed': item?.quantity <= 1 }"
                :disabled="item?.quantity <= 1"
              >
                −
              </button>
              <span class="px-2 sm:px-3 py-1 border-x border-gray-300 text-sm sm:text-base">{{
                item?.orderQuantity
              }}</span>
              <button
                :disabled="item?.quantity <= 1"
                class="px-2 sm:px-3 py-1 text-gray-600 hover:bg-gray-100 transition-colors"
                :class="{ 'opacity-50 cursor-not-allowed': item?.quantity === item?.orderQuantity }"
              >
                +
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- <div
        v-for="item in regularItems"
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
          <NuxtImg
            :src="item.main_image"
            :alt="item.name"
            class="w-16 h-16 sm:w-20 sm:h-20 object-contain rounded-lg border border-gray-200"
          />
          <div class="flex-1 min-w-0">
            <h3 class="text-sm sm:text-base font-medium text-gray-900 truncate">{{ item.name }}</h3>
            <p class="text-xs sm:text-sm text-gray-500">Код: {{ item.code }}</p>
            <p
              v-if="item.description"
              class="text-xs sm:text-sm text-gray-700 mt-1 line-clamp-2"
              v-html="item.description"
            ></p>
          </div>
        </div>

        <div class="flex items-center justify-between sm:justify-end gap-3 sm:gap-6 mt-2 sm:mt-0">
          <p class="text-base sm:text-lg font-bold whitespace-nowrap text-gray-800">
            {{ formatPrice(item.price) }}
          </p>
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
      </div> -->
    </div>
  </div>
</template>
