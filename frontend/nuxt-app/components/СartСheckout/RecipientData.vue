<script setup>
import Button from '~/components/ui/Button/Button.vue'
import { useUserStore } from '~/stores/user'
const userStore = useUserStore()
const { user } = storeToRefs(userStore)

const cartStore = useCartStore()
const { cart } = storeToRefs(cartStore)
const {
  public: { backendUrl },
} = useRuntimeConfig()

const selectedIndex = ref(cart.value.selectedCompany || null)
const companies = ref([])
const isLoading = ref(true)

const getCompanies = async () => {
  isLoading.value = true
  try {
    const data = await $fetch(`${backendUrl}/api/profile/`, {
      method: 'GET',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
      server: false,
    })

    companies.value = data
  } catch (e) {
    console.log(e)
  } finally {
    isLoading.value = false
  }
}

const updateCompany = async selectedCompany => {
  try {
    await $fetch(`${backendUrl}/api/orders/company`, {
      method: 'POST',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
      body: {
        selectedCompany,
      },
      server: false,
    })
  } catch (e) {
    console.log(e)
  } finally {
    selectedIndex.value = selectedCompany
    await cartStore.refetchCart()
    // await getCompanies()
  }
}

onMounted(() => {
  getCompanies()
})
</script>

<template>
  <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm mt-4">
    <div>
      <div class="flex justify-between items-start mb-4">
        <h2 class="text-lg sm:text-xl font-bold text-gray-800">Выберите данные покупателя</h2>
      </div>
    </div>

    <div class="flex flex-col gap-6 items-center">
      <div
        @click="() => updateCompany(null)"
        class="cursor-pointer bg-white rounded-2xl shadow-xl overflow-hidden transition-all hover:shadow-2xl p-6 md:p-8 w-full flex flex-col gap-1"
        :class="{ 'ring-4 ring-primary ring-opacity-30': selectedIndex === null }"
      >
        <div :class="{ 'text-primary': !user?.name }">Физ. лицо: {{ user.name || 'Не указан' }}</div>
        <div>Почта: {{ user.email }}</div>
        <div :class="{ 'text-primary': !user?.phone }">Телефон: {{ user.phone || 'Не указан' }}</div>
      </div>

      <div
        v-for="company in companies"
        @click="() => updateCompany(company.id)"
        :key="company.id"
        class="cursor-pointer bg-white rounded-2xl shadow-xl overflow-hidden transition-all hover:shadow-2xl w-full"
        :class="{ 'ring-4 ring-primary ring-opacity-30': selectedIndex === company.id }"
      >
        <div class="p-6 md:p-8">
          <div class="flex justify-between items-start mb-6 gap-4">
            <div class="flex items-start gap-4">
              <div class="bg-primary bg-opacity-10 p-3 rounded-xl">
                <Icon name="mdi:office-building" class="w-7 h-7 text-primary" />
              </div>
              <div>
                <h3 class="text-xl md:text-2xl font-semibold text-gray-900">
                  {{ company.name || `Компания #${company.id}` }}
                </h3>
                <div class="flex items-center gap-3 mt-2">
                  <span
                    v-if="company.is_main"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary bg-opacity-15 text-primary border border-primary border-opacity-20"
                  >
                    <Icon name="mdi:star" class="w-4 h-4 mr-1.5" />
                    Основная
                  </span>
                  <span class="text-sm text-gray-500">
                    Добавлена: {{ new Date(company.created_at).toLocaleDateString() }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
            <div class="bg-gray-50 rounded-xl p-5 shadow-inner">
              <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                <Icon name="mdi:card-account-details" class="w-5 h-5 text-primary" />
                Реквизиты
              </h4>
              <dl class="space-y-4">
                <div class="grid grid-cols-3 gap-4">
                  <dt class="text-sm text-gray-500">ИНН</dt>
                  <dd class="text-sm font-medium text-gray-900 col-span-2 font-mono tracking-wide">
                    {{ company.inn }}
                  </dd>
                </div>
                <div v-if="company.kpp" class="grid grid-cols-3 gap-4">
                  <dt class="text-sm text-gray-500">КПП</dt>
                  <dd class="text-sm font-medium text-gray-900 col-span-2 font-mono tracking-wide">
                    {{ company.kpp }}
                  </dd>
                </div>
              </dl>
            </div>

            <div class="bg-gray-50 rounded-xl p-5 shadow-inner">
              <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                <Icon name="mdi:account-box-multiple" class="w-5 h-5 text-primary" />
                Контактные данные
              </h4>
              <dl class="space-y-4">
                <div v-if="company.director" class="grid grid-cols-3 gap-4">
                  <dt class="text-sm text-gray-500">Директор</dt>
                  <dd class="text-sm font-medium text-gray-900 col-span-2">{{ company.director }}</dd>
                </div>
                <div v-if="company.legal_address" class="grid grid-cols-3 gap-4">
                  <dt class="text-sm text-gray-500">Адрес</dt>
                  <dd class="text-sm font-medium text-gray-900 col-span-2">{{ company.legal_address }}</dd>
                </div>
                <div v-if="company.phone" class="grid grid-cols-3 gap-4">
                  <dt class="text-sm text-gray-500">Телефон</dt>
                  <dd class="text-sm font-medium text-gray-900 col-span-2">
                    <a :href="`tel:${company.phone}`" class="hover:text-primary transition-colors">
                      {{ company.phone }}
                    </a>
                  </dd>
                </div>
                <div v-if="company.email" class="grid grid-cols-3 gap-4">
                  <dt class="text-sm text-gray-500">Email</dt>
                  <dd class="text-sm font-medium text-gray-900 col-span-2">
                    <a :href="`mailto:${company.email}`" class="hover:text-primary transition-colors">
                      {{ company.email }}
                    </a>
                  </dd>
                </div>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div v-if="companies.length !== 3 || isLoading">
        <Button to="/account/companies">Добавить компанию</Button>
      </div>
    </div>
  </div>
</template>
