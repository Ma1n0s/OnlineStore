<script setup lang="ts">
interface ICrumb {
	name: string
	url?: string
	color?: string
}

const { list } = defineProps<{ list: ICrumb }>()
</script>

<template>
	<nav class="flex items-center gap-2 text-gray-600 dark:text-gray-400 mb-4 text-sm">
		<NuxtLink to="/" class="hover:text-primary transition-colors" exact-active-class="text-primary font-medium">
			Главная
		</NuxtLink>

		<template v-for="(crumb, index) in list" :key="index">
			<Icon name="heroicons:chevron-right-20-solid" class="h-4 w-4 text-gray-400" />
			<component
				:is="crumb.url ? NuxtLink : div"
				:to="crumb?.url"
				:class="['transition-colors', crumb?.color ? `text-${crumb.color}` : 'hover:text-primary']"
				exact-active-class="text-primary font-medium"
			>
				{{ crumb.name }}
			</component>
		</template>
	</nav>
</template>
