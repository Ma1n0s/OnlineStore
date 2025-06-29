<script setup>
import { reactive, computed } from 'vue'
import { useUserStore } from '~/stores/user'
import SidebarMenu from '~/components/Account/SidebarMenu.vue'

definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: `Обратная связь | Абсолют техно`,
  meta: [
    {
      name: 'description',
      content: `Инструменты и оборудование для строительства и ремота`,
    },
  ],
})

const {
  public: { backendUrl },
} = useRuntimeConfig()

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

const profile = computed(() => ({
  name: userStore.user?.name || '',
  email: userStore.user?.email || '',
  phone: userStore.user?.phone || '',
  registrationDate: userStore.user?.created_at || '',
  company: userStore.user?.company_name || '',
  companyDetails: userStore.user?.companyDetails || null,
}))

const submitForm = async () => {
  state.isSubmitting = true

  try {
    const response = await $fetch(`${backendUrl}/api/feedback`, {
      method: 'POST',
      body: form,
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
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
    console.log('Error submitting feedback:', error)
    alert('Произошла ошибка при отправке обращения. Пожалуйста, попробуйте позже.')
  } finally {
    state.isSubmitting = false
  }
}
</script>

<template>
  <div class="min-h-screen py-8">
    <div class="max-w-screen-2xl mx-auto px-1 sm:px-4 lg:px-8">
      <div class="flex flex-col md:flex-row gap-6">
        <SidebarMenu class="hidden lg:block" />
        <div class="flex-1 space-y-6">
          <div class="bg-white shadow-2xl rounded-lg p-6">
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
