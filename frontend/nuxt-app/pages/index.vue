<script setup>
import { ref } from "vue";
import Modal from "~/components/Modal/Modal.vue";
import Select from "~/shared/ui/Select/Select.vue";

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
  <div class="bg-gray-800 w-full ~text-sm/xs ~p-0/80">
    <div>
      <button @click="openModal" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        Открыть модальное окно
      </button>
      <Modal :isOpen="isModalOpen" @close="closeModal" @confirm="handleConfirm" title="">
        <p>Текст</p>
      </Modal>
    </div>

    {{ JSON.stringify(s) }}
    <Select
      :options="[
        { value: '2222', label: '123' },
        { value: '2', label: '1' },
      ]"
      v-model="s"
    />
  </div>
</template>
