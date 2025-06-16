<script setup>
import Button from '~/components/ui/Button/Button.vue'
import { useUserStore } from '~/stores/user'
const userStore = useUserStore()
const { user } = storeToRefs(userStore)
const {
  public: { backendUrl },
} = useRuntimeConfig()

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

    console.log(data, 'companies')
  } catch (e) {
    console.log(e)
  } finally {
    isLoading.value = false
  }
}

const updateCompany = async selectedCompany => {
  try {
    const data = await $fetch(`${backendUrl}/api/orders/company`, {
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

    companies.value = data

    console.log(data, 'companies')
  } catch (e) {
    console.log(e)
  } finally {
    await getCompanies()
  }
}

onMounted(() => {
  getCompanies()
})
</script>

<template>
  <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm">
    <div>
      <div class="flex justify-between items-start mb-4">
        <h2 class="text-lg sm:text-xl font-bold text-gray-800">Данные покупателя</h2>
      </div>
    </div>

    <div>
      <div>
        <div>{{ user.name }}</div>
        <Button @click="updateCompany(null)">Добавить компанию</Button>
      </div>
      <div v-for="company in companies">
        {{ company.id }}
        <Button @click="updateCompany(company.id)">Добавить компанию</Button>
      </div>
    </div>

    <div v-if="companies.length !== 3 || isLoading">
      <Button to="/account/companies">Добавить компанию</Button>
    </div>
  </div>
</template>
