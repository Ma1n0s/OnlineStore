<script setup>
import { reactive, computed } from 'vue'
import { useUserStore } from '~/stores/user'
import SidebarMenu from '~/components/Account/SidebarMenu.vue'

const userStore = useUserStore()

const form = reactive({
  subject: 'Вопрос по заказу',
  message: '',
  rating: 0,
  contactMethod: 'email',
})

const subjects = reactive([
  'Вопрос по заказу',
  'Техническая поддержка',
  'Предложение сотрудничества',
  'Жалоба',
  'Другое',
])

const state = reactive({
  hoverRating: 0,
  isSubmitting: false,
  isSuccess: false,
})

const setRating = rating => {
  form.rating = rating
}

const setHoverRating = rating => {
  state.hoverRating = rating
}

const resetHoverRating = () => {
  state.hoverRating = form.rating
}

const profile = computed(() => ({
  name: userStore.user?.name || '',
  email: userStore.user?.email || '',
  phone: userStore.user?.phone || '',
  registrationDate: userStore.user?.created_at || '',
  company: userStore.user?.company_name || '',
  companyDetails: userStore.user?.companyDetails || null
}))

const submitForm = async () => {
  state.isSubmitting = true
  
  try {
    const response = await $fetch('http://127.0.0.1:8000/api/feedback', {
      method: 'POST',
      body: form,
      headers: {
        'Accept': 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
      credentials: 'include',
    })
    
    if (response.status === 'success') {
      state.isSuccess = true
      form.message = ''
      form.rating = 0
    }
  } catch (error) {
    console.error('Error submitting feedback:', error)
    alert('Произошла ошибка при отправке обращения. Пожалуйста, попробуйте позже.')
  } finally {
    state.isSubmitting = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-slate-100 py-8">
    <div class="max-w-screen-2xl mx-auto px-1 sm:px-4 lg:px-8">
      <div class="flex flex-col md:flex-row gap-6">
        <SidebarMenu class="hidden md:block" />
        <div class="flex-1 space-y-6">
          <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Обратная связь</h1>
            <p class="text-gray-600 mb-6">Мы ответим вам по выбранному способу связи</p>

            <div v-if="state.isSuccess" class="mb-6 p-4 bg-green-50 text-green-800 rounded-lg">
              <div class="flex items-center">
                <svg class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Ваше сообщение успешно отправлено! Мы свяжемся с вами в ближайшее время.</span>
              </div>
            </div>

            <form @submit.prevent="submitForm" class="space-y-6">
              <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Ваши контактные данные</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <p class="text-xs text-gray-500">Имя</p>
                    <p class="text-sm font-medium">{{ profile.name }}</p>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">Email</p>
                    <p class="text-sm font-medium">{{ profile.email }}</p>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">Телефон</p>
                    <p class="text-sm font-medium">{{ profile.phone }}</p>
                  </div>
                </div>
              </div>

              <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Тема обращения *</label>
                <select
                  id="subject"
                  v-model="form.subject"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                >
                  <option v-for="(subject, index) in subjects" :key="index" :value="subject">{{ subject }}</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Оцените нашу работу</label>
                <div class="flex items-center">
                  <div class="flex">
                    <button
                      v-for="star in 5"
                      :key="star"
                      type="button"
                      @click="setRating(star)"
                      @mouseover="setHoverRating(star)"
                      @mouseleave="resetHoverRating"
                      class="focus:outline-none"
                    >
                      <svg
                        class="h-8 w-8"
                        :class="{
                          'text-yellow-400': star <= (state.hoverRating || form.rating),
                          'text-gray-300': star > (state.hoverRating || form.rating),
                        }"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path
                          d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                      </svg>
                    </button>
                  </div>
                  <span class="ml-2 text-sm text-gray-500">{{ form.rating }}/5</span>
                </div>
              </div>

              <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Ваше сообщение *</label>
                <textarea
                  id="message"
                  v-model="form.message"
                  rows="4"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                  placeholder="Опишите ваш вопрос или проблему..."
                ></textarea>
              </div>

              <div class="pt-2">
                <button
                  type="submit"
                  :disabled="state.isSubmitting"
                  class="w-full sm:w-auto px-6 py-3 bg-primary hover:bg-primary-hover text-white font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <span v-if="!state.isSubmitting">Отправить сообщение</span>
                  <span v-else class="flex items-center justify-center">
                    <svg
                      class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                    >
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                      ></path>
                    </svg>
                    Отправка...
                  </span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
