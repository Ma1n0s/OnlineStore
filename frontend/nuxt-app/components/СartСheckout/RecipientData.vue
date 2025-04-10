<script setup>
import TextInput from '~/components/ui/Inputs/TextInput.vue'
import Button from '~/components/ui/Button/Button.vue'
const props = defineProps({
	customer: {
		type: Object,
		required: true,
	},
	showSecondForm: {
		type: Boolean,
		required: true,
	},
	secondCustomer: {
		type: Object,
		required: true,
	},
	deliveryAddress: {
		type: String,
		required: true,
	},
	paymentMethod: {
		type: String,
		required: true,
	},
})

const emit = defineEmits([
	'update:showSecondForm',
	'update:secondCustomer',
	'update:deliveryAddress',
	'update:paymentMethod',
])
</script>

<template>
	<div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm">
		<h2 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4 text-gray-800">Данные получателя</h2>

		<div class="flex items-center mb-4 sm:mb-6">
			<div class="flex items-center text-xs sm:text-sm text-gray-500">
				<img src="" alt="" class="" />
				<span>Все поля обязательны</span>
			</div>
		</div>

		<div class="grid grid-cols-1 gap-3 sm:gap-4 mb-3 sm:mb-4">
			<TextInput disabled id="name" label="Имя" :modelValue="customer.name" placeholder="Ваше имя" size="small" />
			<TextInput
				id="phone"
				disabled
				label="Телефон"
				type="tel"
				:modelValue="customer.phone"
				placeholder="+7 (___) ___-__-__"
				size="small"
			/>
		</div>

		<div class="mb-3 sm:mb-4">
			<label class="inline-flex items-center cursor-pointer">
				<input
					type="checkbox"
					:checked="showSecondForm"
					@change="$emit('update:showSecondForm', $event.target.checked)"
					class="sr-only peer"
				/>
				<div
					class="bg-black relative w-9 h-5 sm:w-11 sm:h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 sm:after:h-5 sm:after:w-5 after:transition-all peer-checked:bg-primary-active"
				></div>
				<span class="ms-2 text-xs sm:text-sm font-medium text-gray-700">Другой получатель</span>
			</label>
		</div>

		<div v-if="showSecondForm" class="grid grid-cols-1 gap-3 sm:gap-4 mb-3 sm:mb-4">
			<TextInput
				id="secondName"
				label="Имя"
				:modelValue="secondCustomer.name"
				@update:modelValue="val => $emit('update:secondCustomer', { ...secondCustomer, name: val })"
				placeholder="Имя получателя"
				size="small"
			/>
			<TextInput
				id="secondPhone"
				label="Телефон"
				type="tel"
				:modelValue="secondCustomer.phone"
				@update:modelValue="val => $emit('update:secondCustomer', { ...secondCustomer, phone: val })"
				placeholder="+7 (___) ___-__-__"
				size="small"
			/>
		</div>

		<div class="mb-3 sm:mb-4">
			<TextInput
				id="address"
				label="Адрес доставки"
				:modelValue="deliveryAddress"
				@update:modelValue="$emit('update:deliveryAddress', $event)"
				placeholder="Город, улица, дом, квартира"
				size="small"
			/>
		</div>

		<div class="mt-6 sm:mt-8 pt-6 sm:pt-8 border-t border-gray-100">
			<h3 class="text-lg sm:text-xl font-bold mb-4 sm:mb-5 text-gray-900">Способ оплаты</h3>
			<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5">
				<label
					class="relative bg-white p-4 sm:p-5 rounded-xl cursor-pointer border border-gray-200 hover:border-primary transition-all shadow-sm hover:shadow-md"
					:class="{ 'border-primary shadow-md': paymentMethod === 'cash' }"
				>
					<input
						type="radio"
						:checked="paymentMethod === 'cash'"
						@change="$emit('update:paymentMethod', 'cash')"
						class="hidden"
					/>
					<div class="flex items-start">
						<div
							class="flex items-center justify-center h-5 w-5 rounded-full border-2 border-gray-300 mr-3 mt-0.5 flex-shrink-0"
							:class="{ 'border-primary bg-primary': paymentMethod === 'cash' }"
						>
							<div v-if="paymentMethod === 'cash'" class="h-2 w-2 rounded-full bg-white"></div>
						</div>
						<div>
							<div class="text-base sm:text-lg font-semibold text-gray-900">Наличные</div>
							<p class="text-sm sm:text-base text-gray-500 mt-1">Оплата наличными</p>
						</div>
					</div>
				</label>

				<label
					class="relative bg-white p-4 sm:p-5 rounded-xl cursor-pointer border border-gray-200 hover:border-primary transition-all shadow-sm hover:shadow-md"
					:class="{ 'border-primary shadow-md': paymentMethod === 'card' }"
				>
					<input
						type="radio"
						:checked="paymentMethod === 'card'"
						@change="$emit('update:paymentMethod', 'card')"
						class="hidden"
					/>
					<div class="flex items-start">
						<div
							class="flex items-center justify-center h-5 w-5 rounded-full border-2 border-gray-300 mr-3 mt-0.5 flex-shrink-0"
							:class="{ 'border-primary bg-primary': paymentMethod === 'card' }"
						>
							<div v-if="paymentMethod === 'card'" class="h-2 w-2 rounded-full bg-white"></div>
						</div>
						<div>
							<div class="text-base sm:text-lg font-semibold text-gray-900">Картой онлайн</div>
							<p class="text-sm sm:text-base text-gray-500 mt-1">Оплата на сайте</p>
						</div>
					</div>
				</label>

				<label
					class="relative bg-white p-4 sm:p-5 rounded-xl cursor-pointer border border-gray-200 hover:border-primary transition-all shadow-sm hover:shadow-md"
					:class="{ 'border-primary shadow-md': paymentMethod === 'qr' }"
				>
					<input
						type="radio"
						:checked="paymentMethod === 'qr'"
						@change="$emit('update:paymentMethod', 'qr')"
						class="hidden"
					/>
					<div class="flex items-start">
						<div
							class="flex items-center justify-center h-5 w-5 rounded-full border-2 border-gray-300 mr-3 mt-0.5 flex-shrink-0"
							:class="{ 'border-primary bg-primary': paymentMethod === 'qr' }"
						>
							<div v-if="paymentMethod === 'qr'" class="h-2 w-2 rounded-full bg-white"></div>
						</div>
						<div>
							<div class="text-base sm:text-lg font-semibold text-gray-900">QR-код</div>
							<p class="text-sm sm:text-base text-gray-500 mt-1">Оплата по QR-коду</p>
						</div>
					</div>
				</label>

				<label
					class="relative bg-white p-4 sm:p-5 rounded-xl cursor-pointer border border-gray-200 hover:border-primary transition-all shadow-sm hover:shadow-md"
					:class="{ 'border-primary shadow-md': paymentMethod === 'installment' }"
				>
					<input
						type="radio"
						:checked="paymentMethod === 'installment'"
						@change="$emit('update:paymentMethod', 'installment')"
						class="hidden"
					/>
					<div class="flex items-start">
						<div
							class="flex items-center justify-center h-5 w-5 rounded-full border-2 border-gray-300 mr-3 mt-0.5 flex-shrink-0"
							:class="{ 'border-primary bg-primary': paymentMethod === 'installment' }"
						>
							<div v-if="paymentMethod === 'installment'" class="h-2 w-2 rounded-full bg-white"></div>
						</div>
						<div>
							<div class="text-base sm:text-lg font-semibold text-gray-900">Рассрочка</div>
							<p class="text-sm sm:text-base text-gray-500 mt-1">2% на 6 месяцев</p>
						</div>
					</div>
				</label>
			</div>
		</div>
	</div>
</template>
