<template>
  <div class="relative w-full h-full shadow-2xl rounded-full" ref="target">
    <TextInput :setAtr="true" v-model="search" @click.prevent="handleInputClick" class="!rounded-full !outline-none">
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
const target = useTemplateRef<HTMLElement>('target')

const {
  public: { backendUrl },
} = useRuntimeConfig()

// Handle input click (show search bar if there's text)
const handleInputClick = () => {
  searchBar.value = !!search.value && search.value.trim().length > 0
}

// Watch for search changes
watch(search, newValue => {
  if (newValue && newValue.trim().length > 0) {
    debouncedSearch()
    searchBar.value = true
  } else {
    products.value = []
    categories.value = []
    searchBar.value = false
  }
})

const debouncedSearch = useDebounceFn(async () => {
  await performSearch(search.value)
}, 500) // Reduced debounce time for better UX

const performSearch = async (query: string) => {
  try {
    const data = await $fetch(`${backendUrl}/api/search`, {
      method: 'GET',
      params: { query },
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
    })

    products.value = data?.products || []
    categories.value = data?.categories || []
  } catch (e) {
    console.log('Search error:', e)
    products.value = []
    categories.value = []
  }
}

const hideSearch = () => {
  searchBar.value = false
}
</script>
