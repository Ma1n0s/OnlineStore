<template>
  <div class="fixed z-50 w-full">
    <div
      class="bg-zinc-200 justify-center items-center flex-col w-full lg:flex transition-all duration-300"
      :class="{ 'opacity-0 h-0 overflow-hidden': isScrolled }"
    >
      <div class="flex justify-center items-center gap-1 xl:gap-2 w-full max-w-screen-2xl px-8">
        <div class="flex items-center justify-around gap-2 w-full py-2">
          <div class="flex items-center gap-8 xl:gap-16 justify-start w-full">
            <NuxtLink to="/contacts" class="text-dark hover:text-primary font-bold text-sm">Контакты</NuxtLink>
            <NuxtLink to="/about" class="text-dark hover:text-primary font-bold text-sm">О компании</NuxtLink>
            <NuxtLink to="/news/3" class="text-dark hover:text-primary font-bold text-sm">Условия аренды</NuxtLink>
            <NuxtLink to="/news/1" class="text-dark hover:text-primary font-bold text-sm">Гарантия</NuxtLink>
            <NuxtLink to="/news/5" class="text-dark hover:text-primary font-bold text-sm">Оплата и доставка</NuxtLink>
          </div>

          <div class="flex items-center justify-between gap-4 xl:gap-8">
            <div class="flex flex-col items-start">
              <NuxtLink
                to="tel:+79169999999"
                class="text-primary font-bold text-nowrap text-xs xl:text-sm text-center flex items-center gap-1"
              >
                <Icon name="material-symbols:phone-android-rounded" class="h-4 w-4" alt="Телефон" />
                +7 (916) 999-99-99</NuxtLink
              >
              <p class="text-gray font-bold text-nowrap text-xs xl:text-sm flex items-center gap-1">
                <Icon
                  name="material-symbols:nest-clock-farsight-analog-outline-rounded"
                  class="h-4 w-4"
                  alt="Время работы"
                />
                с 9:00 до 18:00 (Пн-Пт)
              </p>
            </div>

            <div>
              <p class="text-gray font-bold text-nowrap text-xs xl:text-sm flex items-start gap-1">
                <Icon
                  name="material-symbols:location-on-rounded"
                  class="h-4 w-4 mt-[2px] inline text-gray"
                  alt="Адрес"
                />
                Нижний Тагил, <br />
                ул. Аганичева 101а
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div
      class="bg-zinc-200 hidden lg:flex justify-center items-center gap-4 py-2 w-full transition-all duration-300"
      :class="{ '!py-1': isScrolled }"
    >
      <div class="flex justify-between gap-2 items-center w-full max-w-screen-2xl px-8">
        <div class="flex items-center gap-2 w-full">
          <NuxtLink to="/" class="flex items-center gap-2 w-[210px]">
            <NuxtImg src="full_logo_text.svg" class="h-[50px] w-fit" alt="Абсолют Техно" />
          </NuxtLink>

          <!-- <CatalogButton /> -->
          <div class="w-full max-w-[700px]">
            <Search />
          </div>
        </div>

        <div class="flex items-center gap-2">
         

          <div class="relative w-full h-full">
            <Button
              v-if="isAuth"
              variant="transparent"
              class="flex items-center gap-2 h-full !px-4"
              @click="miniMenu = true"
              >{{ userDisplayName }}
              <Icon name="material-symbols:account-circle" class="h-8 w-8 text-dark" alt="Пользователь" />
            </Button>
            <Button variant="transparent" class="flex items-center gap-2 h-full !px-4" @click="openModal" v-else>
              <Icon name="material-symbols:account-circle" class="h-8 w-8 text-dark" alt="Вход"
            /></Button>


          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="bg-zinc-200 flex justify-center w-full px-2 lg:hidden fixed z-50">
    <div class="flex justify-around flex-col md:flex-row items-center gap-4 pt-[2px] w-full max-w-screen-xl">
      <div class="w-full h-full flex items-center gap-2" v-if="isMenuOpen">
        <div class="w-full">
          <Search @close="isMenuOpen = false" />
        </div>
        <Button variant="transparent" class="flex items-center gap-2 h-full" @click="isMenuOpen = false"
          ><Icon name="material-symbols:close-rounded" class="h-8 w-8 text-danger" alt="Закрыть"
        /></Button>
      </div>

      <div class="flex items-center justify-between gap-4 w-full" v-else>
        <div>
          <NuxtLink to="/" class="flex items-center gap-2 w-full">
            <NuxtImg src="full_logo_text.svg" class="h-[40px] w-fit" alt="Абсолют Техно" />
          </NuxtLink>
        </div>

        <div class="flex items-center gap-2">
          <Button variant="transparent" class="flex items-center gap-2 h-full" @click="isMenuOpen = true"
            ><Icon name="material-symbols:search-rounded" class="h-8 w-8" alt="Поиск"
          /></Button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Button from '~/components/ui/Button/Button.vue'
import { useUserStore } from '~/stores/user'
import Search from '../Search/Search.vue'


const userStore = useUserStore()
const { clearUser } = userStore

// Используем computed для доступа к реактивным свойствам store
const isAuth = computed(() => userStore.isAuth)
const user = computed(() => userStore.user)

// Вычисляемое свойство для отображения имени пользователя
const userDisplayName = computed(() => {
  if (!user.value) return 'Пользователь'
  return user.value.name || user.value.email || 'Пользователь'
})

const miniMenu = ref(false)

const isMenuOpen = ref(false)


const isScrolled = ref(false)

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})

const handleScroll = () => {
  isScrolled.value = window.scrollY > 50
}
</script>

<style scoped>
.opacity-0 {
  opacity: 0;
}
</style>
