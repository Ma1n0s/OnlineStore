<script setup>
import TextInput from '~/components/ui/Inputs/TextInput.vue'
const props = defineProps({
	cartNumber: {
		type: String,
		default: '2741-0895-29725',
	},
})

const emit = defineEmits(['search'])

const handleSearch = event => {
	emit('search', event.target.value)
}
</script>
<template>
	<div class="bg-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-sm">
		<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6">
			<div class="flex items-center mb-3 sm:mb-0">
				<h1 class="text-xl sm:text-2xl font-bold mr-2 sm:mr-3 text-gray-800">Корзина</h1>
				<span class="text-sm sm:text-base text-gray-500">{{ cartNumber }}</span>
			</div>
			<NuxtLink to="/" class="text-sm sm:text-base text-gray-600 hover:text-gray-800 transition-colors">
				← Вернуться
			</NuxtLink>
		</div>

		<div class="relative w-full sm:w-3/4 md:w-1/2 lg:w-2/5">
			<TextInput placeholder="Поиск товара..." class="w-full pr-10 text-sm sm:text-base" @input="handleSearch">
				<Icon
					name="material-symbols:search-rounded"
					class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 sm:h-6 sm:w-6"
				/>
			</TextInput>
		</div>
	</div>
</template>
