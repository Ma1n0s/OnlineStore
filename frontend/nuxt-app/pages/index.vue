<script setup>
import { ref } from "vue";
import YandexMap from "~/components/Map/YandexMap.vue";
import TwoGis from "~/components/Map/2Gis.vue";
import Modal from "~/components/Modal/Modal.vue";
// import GoogleMap from "~/components/Map/GoogleMap.vue";

const isModalOpen = ref(false);

const openModal = () => {
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};

const handleConfirm = () => {
  closeModal();
};
</script>
<template>
  <div class="bg-gray-800 w-full ~text-sm/xs ~p-0/80">
    <div>
      <button @click="openModal" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        Открыть модальное окно
      </button>
      <Modal :isOpen="isModalOpen" @close="closeModal" @confirm="handleConfirm" title="">
        <p>Текст</p>
      </Modal>
    </div>
    <div>
      <h1 class="text-2xl font-bold mb-4">Google Map</h1>
      <YandexMap />
      <TwoGis />
      <!-- <GoogleMap /> -->
    </div>
  </div>
</template>
