<script setup>
import Button from "~/shared/ui/Button/Button.vue";

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
  title: {
    type: String,
  },
});

const emit = defineEmits(["close", "confirm"]);

const closeModal = () => {
  emit("close");
};

const confirmAction = () => {
  emit("confirm");
};
</script>
<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-11/12 md:w-1/2 lg:w-1/3 p-6">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-semibold">{{ title }}</h3>
        <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div class="mb-4">
        <slot></slot>
      </div>
      <div class="flex justify-end">
        <Button
          @click="closeModal"
          variant="warning"
          class="px-6 py-2 rounded-lg text-white bg-[#D62828] hover:bg-[#B22222] transition duration-300"
          >Закрыть</Button
        >
        <Button
          @click="confirmAction"
          variant="primary"
          class="px-6 py-2 rounded-lg text-white bg-[#89CFF0] hover:bg-[#60BBCB] transition duration-300"
          >Открыть</Button
        >
      </div>
    </div>
  </div>
</template>
