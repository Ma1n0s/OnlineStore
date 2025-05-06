<template>
    <div class="bg-red-200">
        <div class="mx-auto w-full max-w-screen-2xl px-8 py-8">
          <div class="flex flex-col lg:flex-row gap-8">
            <!-- Левая колонка (меню аккаунта) -->
            <div class="lg:w-1/4">
                <div class="p-6 max-w-4xl mx-auto">
                    <div class="space-y-0"> <!-- Изменил space-y-6 на space-y-0 -->
                        <div class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-lg cursor-pointer border-b border-gray-200"> <!-- Добавил border-b -->
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <Icon name="" />
                                </div>
                                <span class="font-medium">Ваша информация</span>
                            </div>
                            <Icon name="" />
                        </div>
                        <div class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-lg cursor-pointer border-b border-gray-200"> <!-- Добавил border-b -->
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <Icon name="" />
                                </div>
                                <span class="font-medium">Your orders</span>
                            </div>
                            <Icon name="" />
                        </div>
    
                        <div class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-lg cursor-pointer border-b border-gray-200"> <!-- Добавил border-b -->
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <Icon name="" />
                                </div>
                                <span class="font-medium">Пароль</span>
                            </div>
                            <Icon name="" />
                        </div>
    
                        <div class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-lg cursor-pointer border-b border-gray-200"> <!-- Добавил border-b -->
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <Icon name="" />
                                </div>
                                <span class="font-medium">Настройки электронной почты</span>
                            </div>
                            <Icon name="" />
                        </div>
    
                        <div class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-lg cursor-pointer border-b border-gray-200"> <!-- Добавил border-b -->
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <Icon name="" />
                                </div>
                                <span class="font-medium">123</span>
                            </div>
                            <Icon name="" />
                        </div>
    
                        <div class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-lg cursor-pointer border-b border-gray-200"> <!-- Добавил border-b -->
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <Icon name="" />
                                </div>
                                <span class="font-medium">Обратная связь</span>
                            </div>
                            <Icon name="" />
                        </div>
    
                        <div class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-lg cursor-pointer text-red-500">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                    <Icon name="" />
                                </div>
                                <span class="font-medium">выйти</span>
                            </div>
                            <Icon name="" />
                        </div>
                    </div>
    
                    <button class="mt-8 flex items-center text-gray-600 hover:text-gray-800">
                        <Icon name="" />
                        <span>Назад</span>
                    </button>
                </div>
            </div>
    
            <!-- Правая колонка (персональные данные) -->
            <div class="lg:w-4/5">
            <div class="bg-white rounded-lg shadow-md p-6 px-24 h-full">
                <h2 class="text-xl font-bold mb-6">персональные данные</h2>
                
                <form class="space-y-6">
                <div>
                    <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">Имя *</label>
                    <input 
                    type="text" 
                    id="firstName" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                    >
                </div>
    
                <div>
                    <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">фам *</label>
                    <input 
                    type="text" 
                    id="lastName" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                    >
                </div>
    
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Пол *</label>
                    <div class="space-y-2">
                    <div class="flex items-center">
                        <input 
                        type="radio" 
                        id="male" 
                        name="gender" 
                        value="male"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                        required
                        >
                        <label for="male" class="ml-2 text-sm text-gray-700">Муж *</label>
                    </div>
                    <div class="flex items-center">
                        <input 
                        type="radio" 
                        id="female" 
                        name="gender" 
                        value="female"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                        >
                        <label for="female" class="ml-2 text-sm text-gray-700">Жен *</label>
                    </div>
                    
                    </div>
                </div>
    
                <div>
                    <label for="birthday" class="block text-sm font-medium text-gray-700 mb-1">Дата рождения *</label>
                    <input 
                    type="text" 
                    id="birthday" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="10.07.2000"
                    required
                    >
                </div>
    
                <div class="pt-4">
                    <button 
                    type="submit" 
                    class="px-6 py-2 bg-primary text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:bg-primary-hover focus:ring-offset-2"
                    >
                    Сохранить
                    </button>
                </div>
                </form>
            </div>
            </div>
        </div>
        </div>
    </div>
  </template>