<script setup>
import Header from '../Header.vue';
import Footer from '../Footer.vue';
</script>

<template>
    <div>
        <Header />
            <div class="px-52">
                <div>
                    <h1 class="text-sm text-gray-400">Главная/ Инструмент</h1>
                </div>
                <div class="flex ">
                    <h1 class="font-bold">Строительный инструмент</h1>
                    <h2 class="justify-end hover:">Как выбрать электроинструмент</h2>
                </div>
                <div class="grid grid-cols-6 gap-1"> 
                    <div class="bg-gray-100 flex flex-col justify-between rounded-lg p-2 h-full hover:hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">
                        <div>Шуроповерты</div>
                        <img src="" alt="alt" class="self-end h-10 w-10"/>
                    </div>
                </div>
                <h2 class="text-xl font-semibold mb-4">Часто ищут</h2>
                <div class="flex flex-wrap gap-2">
                    <RouterLink class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm hover:hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">Пылесосы с розеткой для электроинструмента</RouterLink>
                    <RouterLink class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm hover:hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">Дрели-шуруповерты для женщин</RouterLink>
                    <RouterLink class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm hover:hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">Дрели-шуруповерты для мебели</RouterLink>
                    <RouterLink class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm hover:hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">Противопожарное оборудование иInventar</RouterLink>
                    <RouterLink class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm hover:hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">Зеленые дрели-шуруповерты аккумуляторные</RouterLink>
                    <RouterLink class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm hover:hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">Синие дрели-шуруповерты аккумуляторные</RouterLink>
                    <RouterLink class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm hover:hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">Желтые дрели-шуруповерты аккумуляторные</RouterLink>
                    <RouterLink class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm hover:hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">… Показать ещё</RouterLink>
                </div>
                <div class="container mx-auto p-6">
                    <div class="flex justify-between mb-4">
                        <h2 class="text-xl font-bold">Статьи</h2>
                        <button class="bg-gray-300 rounded-md px-4 py-2">Все статьи</button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="font-semibold text-lg">Советы по выбору электроинструмента</h3>
                        <p class="text-gray-600 mt-2">Хорошие результаты получать не просто. Различные ситуации требуют...</p>
                        <p class="text-gray-400 text-sm mt-4">05.09.2024</p>
                        </div>

                        <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="font-semibold text-lg">Электроинструмент - как правильно работать</h3>
                        <p class="text-gray-600 mt-2">Каждый, кто хоть раз держал в руке электроинструмент, непременно должен знать...</p>
                        <p class="text-gray-400 text-sm mt-4">05.09.2024</p>
                        </div>

                        <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="font-semibold text-lg">Электроинструмент нужен в каждом доме</h3>
                        <p class="text-gray-600 mt-2">Вряд ли найдутся люди, готовые поспорить с тем, что мелкий домашний ремонт...</p>
                        <p class="text-gray-400 text-sm mt-4">07.06.2023</p>
                        </div>

                        <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="font-semibold text-lg">Всегда ли работает традиционная схема выбора?</h3>
                        <p class="text-gray-600 mt-2">Все рекомендации по вопросу выбора электроинструмента начинаются с...</p>
                        <p class="text-gray-400 text-sm mt-4">07.06.2023</p>
                        </div>
                    </div>
                    </div>



            </div>
        <Footer />
    </div>
</template> 