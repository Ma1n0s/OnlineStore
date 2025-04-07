// интерфейс ICrumb

<script setup>
const { list } = defineProps({
	list: {
		type: Array,
		default: [],
	},
})
</script>

<template>
	<nav class="flex flex-wrap items-center gap-2 text-gray mb-4">
		<template v-for="(item, index) in list" :key="index">
			<div class="flex items-center gap-2">
				<NuxtLink :to="item.url" :class="`text-${item.color}`" class="hover:underline">
					{{ item.name }}
				</NuxtLink>
				<span v-if="index < list.length - 1">/</span>
			</div>
		</template>
	</nav>
</template>
