<script setup>
    import { ref } from 'vue';
    import Header from './Header.vue';
    import Footer from './Footer.vue';

    const items = ref([]); 
    const visibleItems = ref([]); 
    const itemsToShow = 5; 
    const isLoading = ref(false); 

    const fetchItems = async () => {
    isLoading.value = true; 
        try {
            const response = await fetch('');
            const data = await response.json();
            items.value = data;
            visibleItems.value = items.value.slice(0, itemsToShow); 
        } catch (error) {
            console.error('Ошибка при загрузке данных:', error);
        } finally {
            isLoading.value = false; 
        }
    };

    const loadMore = () => {
    const nextItems = items.value.slice(visibleItems.value.length, visibleItems.value.length + itemsToShow);
    visibleItems.value.push(...nextItems);
    };
    // onMounted(fetchItems);
</script>
<template>
    <Header/>
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-6">Статьи</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-lg font-semibold">07.02.2025</h2>
                <h3 class="text-xl font-bold">Лучшие напольные унитазы: рейтинг 2025</h3>
                <p>Мы составили топ лучших напольных унитазов, чтобы вы могли подобрать санитарное изделие под своим требованиям и пожеланиям.</p>
            </div>
            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-lg font-semibold">07.02.2025</h2>
                <h3 class="text-xl font-bold">Топ сифонов: рейтинг 2025</h3>
                <p>В статье вы найдете основные параметры выбора устройства и рейтинг лучших сифонов для ванны и раковины.</p>
            </div>
            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-lg font-semibold">05.02.2025</h2>
                <h3 class="text-xl font-bold">Лучшие счетчики для воды в квартиру: рейтинг производителей</h3>
                <p>В статье вы найдете разбор параметров выбора, а также топ лучших счетчиков для воды.</p>
            </div>
            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-lg font-semibold">04.02.2025</h2>
                <h3 class="text-xl font-bold">Лучшая пропитка для дерева: топ-10</h3>
                <p>В статье вы найдете основные параметры выбора и рейтинг пропиток для наружных работ на сайте ВсеИнструменты.ру.</p>
            </div>
            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-lg font-semibold">03.02.2025</h2>
                <h3 class="text-xl font-bold">Лучшие жидкие гвозди: актуальная подборка</h3>
                <p>Мы составили список лучших жидких гвоздей и рассказали об их характеристиках, чтобы вам было проще выбрать материал для ремонта.</p>
            </div>
            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-lg font-semibold">20.01.2025</h2>
                <h3 class="text-xl font-bold">Как правильно таскать машину на тросе</h3>
                <p>Мы расскажем, как отбуксировать автомобиль и избежать ситуации на дороге.</p>
            </div>
            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-lg font-semibold">14.01.2025</h2>
                <h3 class="text-xl font-bold">Какой снегоуборщик выбрать для дома – бензиновый или электрический?</h3>
                <p>В статье мы разобрали плюсы и минусы снегоуборщиков с электрическим приводом и бензиновым двигателем.</p>
            </div>
        </div>
            <div>
            <div v-for="item in visibleItems" :key="item.id" class="bg-white p-4 rounded shadow">
                {{ item.title }}
            </div>
            </div>
            <button 
                @click="loadMore" 
                class="items-center justify-center mt-4 px-4 py-2  border-gray-300 text-gray-500 rounded hover:shadow-2xl border-black"
                :disabled="isLoading"
                >
                <span v-if="isLoading">Загрузка...</span>
                <span v-else>Показать ещё</span>
            </button>
    </div>
    <Footer/>
</template>