<template>
  <div class="relative w-full" ref="target">
    <TextInput @input="search" @click="search">
      <template v-slot:right>
        <div class="cursor-pointer h-full flex justify-center">
          <Icon name="material-symbols:search-rounded" class="mr-1 h-8 w-8" />
        </div>
      </template>
    </TextInput>
    <SearchList v-if="showSearch" :target="target" v-model:showSearch="showSearch" />
  </div>
</template>

<script setup lang="ts">
import TextInput from '~/components/ui/Inputs/TextInput.vue'
import SearchList from './SearchList.vue'
const showSearch = ref(false)

const search = () => {
  showSearch.value = true
}

const target = useTemplateRef<HTMLElement>('target')
</script>
