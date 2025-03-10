<script setup>
import BaceIcon from './BaceIcon.vue';

</script>

<template>
    <div class="fixed inset-0 z-10 bg-black bg-opacity-80">
        <div class="bg-white max-w-sm mx-auto my-8">
            <div class="p-2 text-right">
                <div class="p-2 focus:outline-none">
                    <BaceIcon name="x"/>
                </div>
            </div>
            <div class="p-6">
                <h2 class="text-lg font-bold">Вход или Регистрация</h2>
                    <form @submit.prevent="handleSubmit">
                        <div>
                            <label for="username">Имя пользователя:</label>
                            <input id="username" type="text" v-model="username" class="border rounded px-2 py-1 w-full" />
                        </div>
                        <div class="mt-4 flex items-center justify-center h-full">
                            <button type="submit" class="border rounded px-4 py-2 bg-blue-500 text-white">Прождолжить</button>
                        </div>
                        <div >
                            <h3>или войдите через</h3>
                            <div class="flex items-center justify-center">
                                <img src="" alt="vk" class=""/>
                                <button type="submit" class="border rounded px-4 py-2 bg-blue-500 text-white font-bold">VK ID</button>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</template> 