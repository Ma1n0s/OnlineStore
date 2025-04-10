<template>
  <div>
    <!-- Фильтр по цене -->
    <div class="mb-6">
      <div class="flex justify-between items-center mb-3">
        <h3 class="font-semibold text-gray-900">Цена, ₽</h3>
        <button @click="resetPrice" class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors">
          Сбросить
        </button>
      </div>

      <div class="flex items-center space-x-3 mb-4">
        <div class="relative flex-1">
          <TextInput
            :modelValue="state.priceRange.inputMin"
            @update:modelValue="handleMinPriceInput"
            @keyup.enter="updateMinPriceFromInput"
            class="w-full"
          />
        </div>
        <span class="text-gray-400">—</span>
        <div class="relative flex-1">
          <TextInput
            :modelValue="state.priceRange.inputMax"
            @update:modelValue="handleMaxPriceInput"
            @keyup.enter="updateMaxPriceFromInput"
            class="w-full"
          />
        </div>
      </div>

      <div class="px-2">
        <div class="relative h-8">
          <div class="absolute w-full h-1 bg-gray-200 rounded-full top-1/2 transform -translate-y-1/2"></div>
          <div
            class="absolute h-1 bg-red-500 rounded-full top-1/2 transform -translate-y-1/2"
            :style="{
              left: `${
                ((state.priceRange.currentMin - state.priceRange.min) / (state.priceRange.max - state.priceRange.min)) *
                100
              }%`,
              width: `${
                ((state.priceRange.currentMax - state.priceRange.currentMin) /
                  (state.priceRange.max - state.priceRange.min)) *
                100
              }%`,
            }"
          ></div>
          <input
            type="range"
            :min="state.priceRange.min"
            :max="state.priceRange.max"
            v-model.number="state.priceRange.currentMin"
            @input="handleSliderChange('min')"
            class="absolute w-full appearance-none pointer-events-none"
          />
          <input
            type="range"
            :min="state.priceRange.min"
            :max="state.priceRange.max"
            v-model.number="state.priceRange.currentMax"
            @input="handleSliderChange('max')"
            class="absolute w-full appearance-none pointer-events-none"
          />
        </div>
      </div>
    </div>

    <!-- Фильтр по брендам -->
    <div class="mb-6">
      <h3 class="font-semibold text-gray-900 mb-3">Производители</h3>
      <div class="space-y-2 max-h-60 overflow-y-auto pr-2">
        <label
          v-for="brand in brands"
          :key="brand"
          class="flex items-center space-x-2 py-1 hover:bg-gray-50 px-2 rounded cursor-pointer"
          @click="toggleBrand(brand)"
        >
          <input
            type="checkbox"
            :checked="state.filters.selectedBrands.includes(brand)"
            class="rounded text-red-600 focus:ring-red-500 border-gray-300"
            @change="toggleBrand(brand)"
          />
          <span class="text-gray-700">{{ brand }}</span>
        </label>
      </div>
    </div>

    <!-- Кнопки фильтров -->
    <div class="space-y-3">
      <button
        @click="toggleFilters"
        class="w-full flex items-center justify-center space-x-2 border border-gray-300 rounded-xl py-2 px-4 hover:bg-gray-50 transition-colors"
      >
        <Icon name="tabler:filter" class="h-6 w-6" />
        <span>Все фильтры</span>
      </button>
      <button class="w-full bg-red-600 hover:bg-red-700 text-white rounded-xl py-2 px-4 transition-colors font-medium">
        Показать {{ filteredItemsCount }} товаров
      </button>
    </div>
  </div>
</template>

<script setup>
import TextInput from '~/components/ui/Inputs/TextInput.vue'
// import Button from '~/components/ui/Button/Button.vue'
import { computed } from 'vue'

const props = defineProps({
  state: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits([
  'toggleFilters',
  'resetPrice',
  'toggleBrand',
  'handleMinPriceInput',
  'handleMaxPriceInput',
  'updateMinPriceFromInput',
  'updateMaxPriceFromInput',
  'handleSliderChange',
])

const brands = ['Ryobi', 'Bosch', 'Makita', 'DeWalt', 'Metabo', 'Hitachi', 'AEG', 'Black+Decker', 'Hilti', 'Milwaukee']

const filteredItemsCount = computed(() => {
  return props.state.items.filter(item => {
    const priceMatch =
      item.price >= props.state.priceRange.currentMin && item.price <= props.state.priceRange.currentMax
    const brandMatch =
      props.state.filters.selectedBrands.length === 0 || props.state.filters.selectedBrands.includes(item.brand)
    return priceMatch && brandMatch
  }).length
})

const toggleFilters = () => emit('toggleFilters')
const resetPrice = () => emit('resetPrice')
const toggleBrand = brand => emit('toggleBrand', brand)
const handleMinPriceInput = value => emit('handleMinPriceInput', value)
const handleMaxPriceInput = value => emit('handleMaxPriceInput', value)
const updateMinPriceFromInput = () => emit('updateMinPriceFromInput', props.state.priceRange.inputMin)
const updateMaxPriceFromInput = () => emit('updateMaxPriceFromInput', props.state.priceRange.inputMax)
const handleSliderChange = type => emit('handleSliderChange', type)
</script>

<style scoped>
input[type='range']::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 18px;
  height: 18px;
  background: #dc2626;
  border-radius: 50%;
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

input[type='range']::-moz-range-thumb {
  width: 18px;
  height: 18px;
  background: #dc2626;
  border-radius: 50%;
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

input[type='range'] {
  -webkit-appearance: none;
  appearance: none;
  height: 8px;
  width: 100%;
  position: absolute;
  top: 0;
  left: 0;
  background: transparent;
  pointer-events: none;
}

input[type='range']::-webkit-slider-runnable-track {
  @apply bg-transparent h-1 rounded-full;
}

input[type='range']::-webkit-slider-thumb {
  @apply bg-red-600 w-5 h-5 rounded-full appearance-none cursor-pointer pointer-events-auto;
  transform: translateY(-50%);
  position: relative;
  z-index: 10;
  top: 50%;
  margin-top: 8px;
}

input[type='range']::-moz-range-track {
  @apply bg-transparent h-1 rounded-full;
}

input[type='range']::-moz-range-thumb {
  @apply bg-red-600 w-5 h-5 rounded-full appearance-none cursor-pointer pointer-events-auto;
  position: relative;
  z-index: 10;
  top: 50%;
  margin-top: 2px;
}

input[type='range']:first-of-type::-webkit-slider-thumb {
  z-index: 20;
}

input[type='range']:last-of-type::-webkit-slider-thumb {
  z-index: 15;
}
</style>
