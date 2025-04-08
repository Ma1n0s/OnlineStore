<script setup lang="ts">
import BrandSwiper from '~/components/Swiper/BrandSwiper.vue'
import CategorySwiper from '~/components/Swiper/CategorySwiper.vue'
import ProductSwiper from '~/components/Swiper/ProductSwiper/ProductSwiper.vue'
import BigSwiper from '~/components/Swiper/BigSwiper.vue'
import CategoryList from '~/components/CategoryItems/CategoryList/CategoryList.vue'
import Modal from '~/components/Modal/Modal.vue'
import AuthForm from '~/shared/ui/FormField/AuthForm.vue'

const isModalOpen = ref(false)

const openModal = () => {
	isModalOpen.value = true
}

const closeModal = () => {
	isModalOpen.value = false
}

const handleConfirm = () => {
	alert('Действие подтверждено!')
	closeModal()
}
</script>
<template>
	<div class="bg-white w-full ~text-sm/xs ~p-4/8 flex flex-col items-center">
		<div class="w-full xl:max-w-7xl">
			<div class="mb-16">
				<BigSwiper />
			</div>

			<div class="mb-16">
				<WrapperHeader title="Кататегории товаров">
					<CategoryList />
				</WrapperHeader>
			</div>

			<div class="mb-16">
				<WrapperHeader title="Кататегории товаров">
					<CategorySwiper />
				</WrapperHeader>
			</div>
			<div class="mb-8">
				<WrapperHeader title="Акции">
					<ProductSwiper />
				</WrapperHeader>
			</div>
			<BrandSwiper />
		</div>

		<div>
			<button @click="openModal" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
				Открыть модальное окно
			</button>
			<Modal :isOpen="isModalOpen" @close="closeModal" @confirm="handleConfirm" title="">
				<AuthForm />
			</Modal>
		</div>
	</div>
</template>
