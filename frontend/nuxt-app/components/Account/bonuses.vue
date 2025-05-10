<script setup>
const bonusPoints = ref(1250);
const progress = computed(() => Math.min((bonusPoints.value / 2000) * 100, 100));
const level = computed(() => {
  if (bonusPoints.value < 500) return 'Стандарт';
  if (bonusPoints.value < 1500) return 'Серебро';
  return 'Золото';
});

const levelBenefits = {
  'Стандарт': ['1% кэшбэк', 'Специальные предложения'],
  'Серебро': ['3% кэшбэк', 'Приоритетная поддержка', 'Специальные предложения'],
  'Золото': ['5% кэшбэк', 'Эксклюзивные товары', 'Персональный менеджер', 'Приоритетная поддержка']
};
</script>

<template>
  <div class="bg-white shadow rounded-lg p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
      <div class="space-y-4">
        <h2 class="text-2xl font-bold text-gray-900">Ваша бонусная карта</h2>
        
        <div class="flex items-center gap-4">
          <div class="relative">
            <div class="w-20 h-20 rounded-full flex items-center justify-center" 
                 :class="{
                   'bg-gray-200': level === 'Стандарт',
                   'bg-gray-300': level === 'Серебро',
                   'bg-yellow-200': level === 'Золото'
                 }">
              <span class="text-2xl font-bold">
                {{ level.charAt(0) }}
              </span>
            </div>
            <span class="absolute -bottom-2 -right-2 bg-primary text-white text-xs font-bold px-2 py-1 rounded-full">
              {{ level }}
            </span>
          </div>
          
          <div>
            <p class="text-sm text-gray-500">Накоплено бонусов</p>
            <p class="text-3xl font-bold">{{ bonusPoints }} ₽</p>
          </div>
        </div>
      </div>
      
      <div class="bg-red-50 p-4 rounded-lg md:w-1/2">
        <h3 class="font-medium text-primary mb-2">Преимущества уровня {{ level }}</h3>
        <ul class="space-y-1 text-sm text-primary">
          <li v-for="(benefit, index) in levelBenefits[level]" :key="index" class="flex items-start">
            <svg class="h-4 w-4 text-primary mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ benefit }}
          </li>
        </ul>
      </div>
    </div>
    
    <div class="mt-6">
      <div class="flex justify-between text-sm text-gray-500 mb-1">
        <span>0 ₽</span>
        <span>2000 ₽</span>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-2.5">
        <div class="bg-primary h-2.5 rounded-full" :style="`width: ${progress}%`"></div>
      </div>
      <p class="text-right text-sm text-gray-500 mt-1">
        До следующего уровня: {{ 2000 - bonusPoints }} ₽
      </p>
    </div>
    
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <button class="bg-primary hover:bg-primary-hover text-white py-2 px-4 rounded-lg text-sm font-medium transition">
        Как получить бонусы
      </button>
      <button class="bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 py-2 px-4 rounded-lg text-sm font-medium transition">
        Как потратить
      </button>
      <button class="bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 py-2 px-4 rounded-lg text-sm font-medium transition">
        Условия программы
      </button>
      <button class="bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 py-2 px-4 rounded-lg text-sm font-medium transition">
        Поделиться картой
      </button>
    </div>
  </div>
</template>