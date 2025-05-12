<script setup lang="ts">
import { useUserStore } from '~/stores/user'
const target = useTemplateRef<HTMLElement>('menu')

const model = defineModel()

const close = () => {
  model.value = false
}

onClickOutside(target, close)

const userStore = useUserStore()
const { clearUser } = userStore

const handleLogout = async () => {
  try {
    const { logout } = useSanctumAuth()
    await logout()
    clearUser()
  } catch (error) {
    console.error('Logout error:', error)
  }
  close()
}
</script>

<template>
  <div class="absolute right-0 pt-2" ref="menu">
    <div class="h-fit w-48 bg-white rounded-lg">
      <nav class="flex-1">
        <NuxtLink
          @click="close"
          to="/account/personal"
          class="flex items-center gap-3 p-2 text-dark hover:bg-zinc-200 rounded-t-lg transition-colors text-sm hover:shadow-sm"
        >
          <Icon name="tabler:user" class="w-6 h-6" />
          Личные данные
        </NuxtLink>

        <NuxtLink
          @click="close"
          to="/account/cart-checkout"
          class="flex items-center gap-3 p-2 text-dark hover:bg-zinc-200 transition-colors text-sm hover:shadow-sm"
        >
          <Icon name="tabler:basket" class="w-6 h-6" />
          Корзина
        </NuxtLink>

        <NuxtLink
          @click="close"
          to="/account/bonus"
          class="flex items-center gap-3 p-2 text-dark hover:bg-zinc-200 transition-colors text-sm hover:shadow-sm"
        >
          <Icon name="tabler:gift" class="w-6 h-6" />
          Бонусы
        </NuxtLink>

        <NuxtLink
          @click="close"
          to="/account/companies"
          class="flex items-center gap-3 p-2 text-dark hover:bg-zinc-200 transition-colors text-sm hover:shadow-sm"
        >
          <Icon name="tabler:home" class="w-6 h-6" />
          Мои организации
        </NuxtLink>

        <NuxtLink
          @click="close"
          to="/account/orders"
          class="flex items-center gap-3 p-2 text-dark hover:bg-zinc-200 transition-colors text-sm hover:shadow-sm"
        >
          <Icon name="tabler:clipboard-list" class="w-6 h-6" />
          История заказов
        </NuxtLink>

        <NuxtLink
          @click="close"
          to="/account/feedback"
          class="flex items-center gap-3 p-2 text-dark hover:bg-zinc-200 rounded-b-lg transition-colors text-sm hover:shadow-sm"
        >
          <Icon name="tabler:message" class="w-6 h-6" />
          Обратная связь
        </NuxtLink>

        <NuxtLink
          to="#"
          @click.prevent="handleLogout"
          class="flex items-center gap-3 p-2 text-dark hover:bg-red-500 rounded-b-lg transition-colors text-sm hover:shadow-sm"
        >
          <Icon name="material-symbols:logout-rounded" class="w-6 h-6" />
          Выход
        </NuxtLink>
      </nav>
    </div>
  </div>
</template>
