<template>
    <nav class="bg-white rounded-lg shadow p-4">
      <ul class="space-y-2">
        <li>
          <NuxtLink 
            to="/account" 
            class="flex items-center p-2 rounded hover:bg-gray-100"
            active-class="bg-blue-50 text-blue-600"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Обзор
          </NuxtLink>
        </li>
        <li>
          <NuxtLink 
            to="/account/bonuses" 
            class="flex items-center p-2 rounded hover:bg-gray-100"
            active-class="bg-blue-50 text-blue-600"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4H5z" />
            </svg>
            Бонусы
          </NuxtLink>
        </li>
        <li>
          <NuxtLink 
            to="/account/profile" 
            class="flex items-center p-2 rounded hover:bg-gray-100"
            active-class="bg-blue-50 text-blue-600"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Профиль
          </NuxtLink>
        </li>
      </ul>
    </nav>
  </template>