<template>
  <div class="relative w-full h-full shadow-2xl rounded-full" ref="target">
    <TextInput :setAtr="true" v-model="search" @click="showSearch" class="!rounded-full !outline-none">
      <template v-slot:right>
        <div class="cursor-pointer h-full flex justify-center">
          <Icon name="material-symbols:search-rounded" class="mr-1 h-8 w-8" />
        </div>
      </template>
    </TextInput>
    <SearchList
      v-if="searchBar"
      v-model:products="products"
      v-model:categories="categories"
      :target="target"
      @close="hideSearch"
    />
  </div>
</template>

<script setup lang="ts">
import TextInput from '~/components/ui/Inputs/TextInput.vue'
import SearchList from './SearchList.vue'
const searchBar = ref(false)

const search = ref('')

const products = ref([])
const categories = ref([])

const showSearch = () => {
  searchBar.value = !!search.value
}

const hideSearch = () => {
  searchBar.value = false
}

const target = useTemplateRef<HTMLElement>('target')

const {
  public: { backendUrl },
} = useRuntimeConfig()

const debouncedSearch = useDebounce(search)

watch(debouncedSearch, async newQuery => {
  console.log(newQuery)
  if (!!newQuery && newQuery.trim().length > 0) {
    await performSearch(newQuery)
    showSearch()
  } else {
    products.value = []
    categories.value = []
    hideSearch()
  }
})

const performSearch = async (query: string) => {
  const { data } = await useAsyncData('search', async () => {
    return await $fetch(`${backendUrl}/api/search`, {
      method: 'GET',
      params: {
        query: query,
      },
    })
  })

  console.log(data.value)
  products.value = data.value.products
  categories.value = data.value.categories
}
</script>
