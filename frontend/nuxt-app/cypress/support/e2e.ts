// ***********************************************************
// Этот файл используется для импорта дополнительных команд
// и настройки окружения для всех E2E тестов
// ***********************************************************

// Импортируем команды Testing Library для более удобного поиска элементов
import '@testing-library/cypress/add-commands'

// Глобальная настройка для всех тестов
beforeEach(() => {
  // Добавляем класс для отключения анимаций и переходов
  cy.on('window:before:load', win => {
    const style = win.document.createElement('style')
    style.innerHTML = `
      * {
        animation-duration: 0s !important;
        transition-duration: 0s !important;
      }
    `
    win.document.head.appendChild(style)
  })
})

// Пользовательская команда для стабилизации страницы перед скриншотом
Cypress.Commands.add('stabilizeForScreenshot', () => {
  // Скрываем все динамические элементы перед снятием скриншота
  cy.get('[data-testid="timestamp"], .dynamic-content').invoke('css', 'visibility', 'hidden')
  // Замораживаем все анимации
  cy.get('body').invoke('addClass', 'no-animations')
})
