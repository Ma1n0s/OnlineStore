<script setup>
  const user = {
    name: 'Иванов Иван Иванович',
    company: 'ООО "ТехноПром"',
    status: 'VIP клиент',
    bonuses: '1 245',
    level: 'Gold'
  }
</script>
<template>
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-32"></div>
      <div class="px-6 py-4 relative">
        <div class="absolute -top-12 left-6 border-4 border-white rounded-full overflow-hidden">
          <img 
            src="https://randomuser.me/api/portraits/men/32.jpg" 
            alt="User avatar" 
            class="h-24 w-24 object-cover"
          >
        </div>
        
        <div class="mt-8">
          <div class="flex justify-between items-start">
            <div>
              <h2 class="text-xl font-bold">{{ user.name }}</h2>
              <p class="text-gray-600">{{ user.company }}</p>
            </div>
            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
              {{ user.status }}
            </span>
          </div>
          
          <div class="mt-4 grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-gray-500">Бонусов</p>
              <p class="text-lg font-semibold">{{ user.bonuses }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Уровень</p>
              <p class="text-lg font-semibold">{{ user.level }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>
  
  