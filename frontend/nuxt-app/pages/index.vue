<script setup>
import { ref } from "vue";
import Footer from "~/components/Footer/Footer.vue";
import Modal from "~/components/Modal/Modal.vue";
import AuthForm from "~/shared/ui/FormField/AuthForm.vue";
import Button from "~/shared/ui/Button/Button.vue";
import GoogleMap from "~/components/Map/GoogleMap.vue";

const isModalOpen = ref(false);

const openModal = () => {
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};

const handleConfirm = () => {
  alert("Действие подтверждено!");
  closeModal();
};
</script>
<template>
  <div class="bg-gray-800 w-full ~text-sm/xs ~p-0/80">
    <div>
      <button @click="openModal" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        Открыть модальное окно
      </button>

      <Modal :isOpen="isModalOpen" @close="closeModal" title="Авторизация">
        <AuthForm />
      </Modal>
    </div>
    <div>
      <h1 class="text-2xl font-bold mb-4">Google Map</h1>
      <GoogleMap />
    </div>
  </div>
</template>
