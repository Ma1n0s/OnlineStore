<script setup>
defineOptions({
  inheritAttrs: false,
});

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

// Функция для закрытия при клике на оверлей
const handleOverlayClick = (event) => {
  if (event.target === event.currentTarget) {
    closeModal();
  }
};
</script>

<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    @click="handleOverlayClick"
  >
    <div
      v-bind="$attrs"
      class="bg-white rounded-lg shadow-lg w-full h-full md:w-11/12 md:h-auto md:max-w-md lg:max-w-lg p-6"
    >
      <div class="flex justify-between items-center">
        <h3 class="text-xl font-semibold">{{ title }}</h3>
        <button @click="closeModal" class="text-gray-500 hover:text-primary-hover transition-colors">
          <Icon name="tabler:xbox-x" class="w-7 h-7" />
        </button>
      </div>
      <div class="">
        <slot></slot>
      </div>
    </div>
  </div>
</template>
