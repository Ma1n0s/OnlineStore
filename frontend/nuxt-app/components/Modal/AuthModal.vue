<template>
  <Modal :isOpen="isModalOpen" @close="closeModal" @confirm="handleConfirm" title="">
    <AuthForm />
  </Modal>
</template>
<script setup lang="ts">
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
