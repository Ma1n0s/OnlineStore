<script setup lang="ts">
const {
  public: { backendUrl },
} = useRuntimeConfig()

const form = ref({
  title: '',
  icon: '',
  description: '',
  link: '',
  is_special: false,
  tags: [] as string[],
  sort_order: 0
})

const tagInput = ref('')

const addTag = () => {
  if (tagInput.value.trim() && !form.value.tags.includes(tagInput.value.trim())) {
    form.value.tags.push(tagInput.value.trim())
    tagInput.value = ''
  }
}

const removeTag = (index: number) => {
  form.value.tags.splice(index, 1)
}

const submit = async () => {
  try {
    await $fetch(`${backendUrl}/api/advantages`, {
      method: 'POST',
      body: form.value
    })
    navigateTo('/admin/advantages')
  } catch (error) {
    console.error('Error creating advantage:', error)
  }
}
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-8">Создание преимущества</h1>
    
    <form @submit.prevent="submit" class="space-y-6 max-w-2xl">
      <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Заголовок</label>
        <input 
          v-model="form.title"
          type="text" 
          id="title" 
          required
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
      </div>

      <div>
        <label for="icon" class="block text-sm font-medium text-gray-700">Иконка (Material Symbols)</label>
        <input 
          v-model="form.icon"
          type="text" 
          id="icon" 
          placeholder="material-symbols:check-circle-outline-rounded"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
      </div>

      <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Описание</label>
        <textarea 
          v-model="form.description"
          id="description" 
          rows="3"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        ></textarea>
      </div>

      <div>
        <label for="link" class="block text-sm font-medium text-gray-700">Ссылка</label>
        <input 
          v-model="form.link"
          type="text" 
          id="link" 
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
      </div>

      <div>
        <label for="sort_order" class="block text-sm font-medium text-gray-700">Порядок сортировки</label>
        <input 
          v-model.number="form.sort_order"
          type="number" 
          id="sort_order" 
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
      </div>

      <div class="flex items-center">
        <input 
          v-model="form.is_special"
          type="checkbox" 
          id="is_special" 
          class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
        >
        <label for="is_special" class="ml-2 block text-sm text-gray-700">Специальный блок</label>
      </div>

      <div v-if="form.is_special">
        <label class="block text-sm font-medium text-gray-700">Теги</label>
        <div class="mt-1 flex">
          <input 
            v-model="tagInput"
            type="text" 
            @keydown.enter.prevent="addTag"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="Введите тег и нажмите Enter"
          >
          <button 
            type="button"
            @click="addTag"
            class="ml-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Добавить
          </button>
        </div>
        
        <div class="mt-2 flex flex-wrap gap-2">
          <span 
            v-for="(tag, index) in form.tags"
            :key="index"
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
          >
            {{ tag }}
            <button 
              type="button"
              @click="removeTag(index)"
              class="ml-1.5 inline-flex text-indigo-400 hover:text-indigo-600 focus:outline-none"
            >
              &times;
            </button>
          </span>
        </div>
      </div>

      <div class="flex justify-end space-x-4">
        <NuxtLink 
          to="/admin/advantages"
          class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Отмена
        </NuxtLink>
        <button 
          type="submit"
          class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Сохранить
        </button>
      </div>
    </form>
  </div>
</template>