/**
 * Плагин для предварительной загрузки часто используемых иконок
 */
import { loadIcon } from '@iconify/vue'

export default defineNuxtPlugin(() => {
  // Список иконок для предварительной загрузки
  const iconsToPreload = [
    'material-symbols:close-rounded',
    'material-symbols:check-circle-outline-rounded',
    'material-symbols:phone-android-rounded',
    'material-symbols:location-on-rounded',
    'material-symbols:shopping-cart-rounded',
    'material-symbols:account-circle',
    'material-symbols:search-rounded',
    'material-symbols:view-list-rounded',
    'tabler:heart',
    'tabler:user-circle',
    'tabler:filter',
    'heroicons:map-pin',
    'heroicons:phone',
    'heroicons:envelope',
  ]

  // Функция для загрузки списка иконок
  const preloadIcons = async () => {
    try {
      // Загружаем все иконки параллельно
      await Promise.all(iconsToPreload.map(icon => loadIcon(icon)))
      console.log('[IconPreloader] Иконки успешно предзагружены')
    } catch (error) {
      console.warn('[IconPreloader] Ошибка предзагрузки иконок:', error)
    }
  }

  // Запускаем загрузку
  if (process.client) {
    // Задержка для предотвращения блокировки основного потока при начальной загрузке
    setTimeout(() => {
      preloadIcons()
    }, 200)
  }
})
