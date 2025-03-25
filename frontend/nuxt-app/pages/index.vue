<script setup>
import { ref } from "vue";
import Modal from "~/components/Modal/Modal.vue";
import BrandSwiper from "~/components/Swiper/BrandSwiper.vue";

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

const s = ref();
</script>
<template>
  <div class="bg-white w-full ~text-sm/xs ~p-0/80">
    <div>
      <button @click="openModal" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        Открыть модальное окно
      </button>
      <Modal :isOpen="isModalOpen" @close="closeModal" @confirm="handleConfirm" title="">
        <p>Текст</p>
      </Modal>

      <BrandSwiper />
    </div>
  </div>
</template>
