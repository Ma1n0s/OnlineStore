<script setup>
import TextInput from '~/components/ui/Inputs/TextInput.vue'
import Textarea from '~/components/ui/Textarea/Textarea.vue'
import Button from '~/components/ui/Button/Button.vue'
import Gis from '~/components/Map/2Gis.vue'

const form = reactive({
  name: '',
  email: '',
  subject: '',
  message: '',
})

const submitForm = () => {
  console.log('Форма отправлена:', form)
}
</script>

<template>
  <div class="min-h-screen bg-neutral-light">
    <div class="relative text-black py-20">
      <div class="container mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Свяжитесь с нами</h1>
        <p class="text-xl md:text-2xl max-w-2xl mx-auto opacity-90">Мы всегда рады вашим вопросам и предложениям</p>
      </div>
      <div class="absolute bottom-0 left-0 right-0 h-12 bg-neutral-light transform skew-y-1 origin-top-left"></div>
    </div>

    <div class="container mx-auto px-6 py-16 -mt-10">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="space-y-6">
          <div
            class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 border-l-4 border-primary"
          >
            <div class="flex items-start gap-4">
              <div class="bg-primary/10 p-3 rounded-full">
                <Icon name="heroicons:map-pin" class="w-6 h-6 text-primary" />
              </div>
              <div>
                <h3 class="text-lg font-semibold mb-2 text-dark">Наш офис</h3>
                <p class="text-neutral-dark">г. Нижний тагил, Аганичева 101А</p>
                <p class="text-neutral-dark">Абсолют Техно"</p>
              </div>
            </div>
          </div>

          <div
            class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 border-l-4 border-accent-DEFAULT"
          >
            <div class="flex items-start gap-4">
              <div class="bg-accent/10 p-3 rounded-full">
                <Icon name="heroicons:phone" class="w-6 h-6 text-accent-DEFAULT" />
              </div>
              <div>
                <h3 class="text-lg font-semibold mb-2 text-dark">Телефоны</h3>
                <p class="text-neutral-dark mb-1">+7 (800) 555-33-55</p>
                <p class="text-neutral-dark">+7 (800) 555-33-55</p>
              </div>
            </div>
          </div>

          <div
            class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 border-l-4 border-primary-active"
          >
            <div class="flex items-start gap-4">
              <div class="bg-primary-active/10 p-3 rounded-full">
                <Icon name="heroicons:envelope" class="w-6 h-6 text-primary-active" />
              </div>
              <div>
                <h3 class="text-lg font-semibold mb-2 text-dark">Электронная почта</h3>
                <p class="text-neutral-dark mb-1">info@company.com</p>
                <p class="text-neutral-dark">support@company.com</p>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white p-8 rounded-xl shadow-lg lg:col-span-2">
          <h2 class="text-2xl font-bold mb-6 text-dark">Напишите нам сообщение</h2>
          <form @submit.prevent="submitForm" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="name" class="block text-xs font-medium text-neutral-dark mb-1">Ваше имя</label>
                <TextInput
                  v-model="form.name"
                  id="name"
                  type="text"
                  placeholder="Иван Иванов"
                  required
                  class="text-sm py-1 px-2"
                />
              </div>
              <div>
                <label for="email" class="block text-xs font-medium text-neutral-dark mb-1">Email</label>
                <TextInput
                  v-model="form.email"
                  id="email"
                  type="email"
                  placeholder="mailname@gmail.com"
                  required
                  class="text-sm py-1 px-2"
                />
              </div>
            </div>
            <div>
              <label for="subject" class="block text-sm font-medium text-neutral-dark mb-2">Тема</label>
              <TextInput
                v-model="form.subject"
                id="subject"
                type="text"
                placeholder="О чем вы хотите поговорить?"
                required
              />
            </div>
            <div>
              <label for="message" class="block text-sm font-medium text-neutral-dark mb-2">Сообщение</label>
              <Textarea
                v-model="form.message"
                id="message"
                rows="5"
                placeholder="Ваше сообщение..."
                required
              ></Textarea>
            </div>
            <Button
              type="submit"
              class="w-full bg-primary hover:bg-primary-hover text-white font-medium py-3 px-6 rounded-lg transition duration-300 shadow-md hover:shadow-lg"
            >
              Отправить сообщение
            </Button>
          </form>
        </div>
      </div>

      <div class="mt-16 rounded-xl shadow-lg overflow-hidden">
        <div class="h-96 w-full relative">
          <Gis />
          <div class="absolute bottom-6 right-6 bg-white p-3 rounded-lg shadow-md border border-neutral-light">
            <div class="flex items-center gap-2">
              <div class="bg-primary p-2 rounded-full">
                <Icon name="heroicons:map-pin" class="w-4 h-4 text-white" />
              </div>
              <span class="font-medium text-sm text-dark">г. Нижний тагил, Аганичева 101А</span>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-16 bg-gradient-to-r from-primary to-primary-active rounded-xl p-8 text-white shadow-lg">
        <h2 class="text-2xl font-bold mb-6">Часы работы</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm">
            <h3 class="text-lg font-semibold mb-2">Офис</h3>
            <p>Понедельник - Пятница</p>
            <p class="font-medium">9:00 - 18:00</p>
          </div>
          <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm">
            <h3 class="text-lg font-semibold mb-2">Техподдержка</h3>
            <p>Понедельник - Суббота</p>
            <p class="font-medium">8:00 - 20:00</p>
          </div>
          <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm">
            <h3 class="text-lg font-semibold mb-2">Выходные</h3>
            <p>Воскресенье</p>
            <p class="font-medium">Закрыто</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
