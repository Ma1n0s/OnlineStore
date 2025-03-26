<template>
    <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg shadow-lg text-white p-6">
      <div class="flex justify-between items-start">
        <div>
          <h3 class="text-lg font-medium">Ваши бонусы</h3>
          <p class="text-3xl font-bold mt-2">1 245</p>
          <p class="text-sm opacity-80 mt-1">До следующего уровня: 255 бонусов</p>
        </div>
        <div class="bg-white bg-opacity-20 p-3 rounded-full">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4H5z" />
          </svg>
        </div>
      </div>
      
      <div class="mt-6">
        <div class="flex justify-between text-sm mb-1">
          <span>Silver</span>
          <span>Gold</span>
          <span>Platinum</span>
        </div>
        <div class="w-full bg-white bg-opacity-30 rounded-full h-2.5">
          <div class="bg-yellow-400 h-2.5 rounded-full" style="width: 75%"></div>
        </div>
      </div>
    </div>
  </template>