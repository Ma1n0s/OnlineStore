/**
 * Плагин для мониторинга и фиксации проблем с загрузкой иконок на стороне клиента
 */
export default defineNuxtPlugin(() => {
  let failedIcons = new Set<string>()
  let iconErrorCount = 0

  // Отслеживаем ошибки из консоли
  const originalConsoleError = console.error
  console.error = (...args) => {
    const errorMessage = args[0]?.toString() || ''

    // Проверяем, является ли сообщение ошибкой загрузки иконки
    if (errorMessage.includes('[Icon] failed to load icon')) {
      const iconMatch = errorMessage.match(/icon `([^`]+)`/)
      if (iconMatch && iconMatch[1]) {
        const iconName = iconMatch[1]
        failedIcons.add(iconName)
        iconErrorCount++

        // Если возникло слишком много ошибок загрузки иконок, попробуем автоматически обновить страницу
        if (iconErrorCount > 5 && !localStorage.getItem('icon_reload_attempted')) {
          console.warn('[IconLoader] Обнаружены проблемы с загрузкой иконок, перезагрузка страницы...')

          // Помечаем попытку перезагрузки, чтобы избежать бесконечного цикла
          localStorage.setItem('icon_reload_attempted', 'true')
          localStorage.setItem('icon_reload_timestamp', Date.now().toString())

          // Перезагружаем страницу
          window.location.reload()
          return
        }
      }
    }

    // Вызываем оригинальный console.error
    originalConsoleError.apply(console, args)
  }

  // Очищаем флаг перезагрузки через 1 минуту после загрузки страницы
  if (process.client) {
    setTimeout(() => {
      const lastReload = parseInt(localStorage.getItem('icon_reload_timestamp') || '0')
      // Если прошло более 1 минуты с момента последней перезагрузки
      if (Date.now() - lastReload > 60000) {
        localStorage.removeItem('icon_reload_attempted')
      }
    }, 60000)
  }
})
