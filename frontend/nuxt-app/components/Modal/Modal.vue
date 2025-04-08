<script setup>
import Button from '~/components/ui/Button/Button.vue'

const props = defineProps({
	isOpen: {
		type: Boolean,
		required: true,
	},
	title: {
		type: String,
	},
})

const emit = defineEmits(['close', 'confirm'])

const closeModal = () => {
	emit('close')
}

const confirmAction = () => {
	emit('confirm')
}
</script>

<template>
	<div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
		<div class="bg-white rounded-lg shadow-lg w-full h-full md:w-11/12 md:h-auto md:max-w-md lg:max-w-lg p-6">
			<div class="flex justify-between items-center mb-4">
				<h3 class="text-xl font-semibold">{{ title }}</h3>
				<button @click="closeModal" class="text-gray-500 hover:text-gray-700">
					<Icon name="tabler:box" class="w-7 h-7" />
				</button>
			</div>
			<div class="mb-4">
				<slot></slot>
			</div>
		</div>
	</div>
</template>
