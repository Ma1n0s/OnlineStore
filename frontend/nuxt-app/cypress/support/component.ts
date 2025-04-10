// ***********************************************************
// Этот файл используется для настройки компонентного тестирования в Cypress
// ***********************************************************

// Импортируем необходимые зависимости
import { mount } from 'cypress/vue'

// Делаем mount доступным как команду Cypress
Cypress.Commands.add('mount', mount)

// Объявляем типы для команды mount
declare global {
  // eslint-disable-next-line no-unused-vars
  namespace Cypress {
    // eslint-disable-next-line no-unused-vars
    interface Chainable {
      /**
       * Монтирует Vue компонент
       * @example cy.mount(MyComponent)
       */
      mount: typeof mount
    }
  }
}
